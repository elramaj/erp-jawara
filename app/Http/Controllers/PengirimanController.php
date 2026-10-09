<?php

namespace App\Http\Controllers;

use App\Models\Pengiriman;
use App\Models\Proyek;
use App\Models\ProyekMilestone;
use App\Models\So;
use App\Services\PengirimanService;
use App\Support\CetakTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengirimanController extends Controller
{
    private const MILESTONE_KIRIM = 'Pengiriman ke Customer';

    /** Tim Pengiriman (6), Direktur (1), Admin (11), Super Admin: atur jadwal. */
    private function cekKelola(): void
    {
        if (!PengirimanService::bisaKelola(auth()->user())) {
            abort(403, 'Akses ditolak.');
        }
    }

    /** Kelola + Gudang (4) + Sales (5). */
    private function cekLihat(): void
    {
        if (!PengirimanService::bisaLihat(auth()->user())) {
            abort(403, 'Akses ditolak.');
        }
    }

    /** Sales hanya boleh membuka jadwal dari proyek yang dia ikuti. */
    private function cekAksesPengiriman(Pengiriman $pengiriman): void
    {
        $this->cekLihat();
        $user = auth()->user();
        if (PengirimanService::lihatSemua($user)) {
            return;
        }
        $punya = $pengiriman->proyek->anggota()->where('user_id', $user->id)->exists();
        if (!$punya) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index(Request $request)
    {
        $this->cekLihat();
        $user = auth()->user();
        $kelola = PengirimanService::bisaKelola($user);

        $filter = $request->query('tampil', 'berjalan');

        $q = Pengiriman::with('proyek')->orderBy('tanggal_kirim', 'desc');
        if (!PengirimanService::lihatSemua($user)) {
            $q->whereHas('proyek.anggota', fn($a) => $a->where('user_id', $user->id));
        }
        if ($filter === 'berjalan') {
            $q->whereNotIn('status', ['diterima', 'dibatalkan']);
        } elseif ($filter === 'selesai') {
            $q->whereIn('status', ['diterima', 'dibatalkan']);
        }
        $pengiriman = $q->get();

        $siap = $kelola ? PengirimanService::perluDijadwalkan() : collect();
        $menunggu = PengirimanService::menungguKonfirmasi($user);

        // Dropdown jadwal manual (proyek aktif apa pun, mis. kirim bertahap)
        $proyekAktif = $kelola
            ? Proyek::where('status', 'aktif')->orderBy('nama_proyek')->get(['id', 'kode_proyek', 'nama_proyek'])
            : collect();

        return view('pengiriman.index', compact('pengiriman', 'siap', 'menunggu', 'proyekAktif', 'filter', 'kelola'));
    }

    public function create(Proyek $proyek)
    {
        $this->cekKelola();
        $this->pastikanProyekBisaDijadwalkan($proyek);

        $proyek->load(['po.detail.barang', 'anggota.user']);
        $kel = $proyek->kelengkapanBarang();

        return view('pengiriman.form', [
            'proyek'     => $proyek,
            'kel'        => $kel,
            'pengiriman' => null,
        ]);
    }

    public function store(Request $request, Proyek $proyek)
    {
        $this->cekKelola();
        $this->pastikanProyekBisaDijadwalkan($proyek);

        $data = $this->validasi($request);

        $pengiriman = DB::transaction(function () use ($proyek, $data) {
            $prefix = 'PGR-' . date('Ymd') . '-';
            $urut = Pengiriman::withoutCompanyScope()
                ->where('company_id', $proyek->company_id)
                ->where('no_pengiriman', 'like', $prefix . '%')
                ->count() + 1;

            return Pengiriman::create($data + [
                'company_id'    => $proyek->company_id,
                'proyek_id'     => $proyek->id,
                'no_pengiriman' => $prefix . str_pad($urut, 3, '0', STR_PAD_LEFT),
                'status'        => 'diusulkan',
                'created_by'    => auth()->id(),
            ]);
        });

        return redirect()->route('pengiriman.show', $pengiriman)
            ->with('success', 'Jadwal pengiriman dibuat. Menunggu konfirmasi Sales pemilik paket atau Gudang.');
    }

    public function show(Pengiriman $pengiriman)
    {
        $pengiriman->load(['proyek.anggota.user', 'proyek.po.detail.barang', 'creator', 'konfirmator']);
        $this->cekAksesPengiriman($pengiriman);

        $user = auth()->user();
        $kel = $pengiriman->proyek->kelengkapanBarang();

        return view('pengiriman.show', [
            'pengiriman'    => $pengiriman,
            'kel'           => $kel,
            'kelola'        => PengirimanService::bisaKelola($user),
            'bisaKonfirmasi' => PengirimanService::bisaKonfirmasi($user, $pengiriman->proyek),
        ]);
    }

    // Cetak Delivery Order (template: resources/views/cetak/<template PT>/do.blade.php)
    public function cetak(Pengiriman $pengiriman)
    {
        $pengiriman->load(['proyek.po.detail.barang', 'company']);
        $this->cekAksesPengiriman($pengiriman);

        // Daftar barang: ambil dari SO proyek (apa yang dijual ke customer).
        // Kalau proyek belum punya SO, jatuh ke barang PO yang sudah diterima gudang.
        $daftarSo = So::where('proyek_id', $pengiriman->proyek_id)->with('detail.barang')->get();
        $barang = [];
        foreach ($daftarSo as $so) {
            foreach ($so->detail as $d) {
                $barang[] = ['nama' => $d->barang->nama_barang ?? '-', 'jumlah' => $d->jumlah, 'satuan' => $d->barang->satuan ?? ''];
            }
        }
        if (!$barang) {
            foreach ($pengiriman->proyek->po as $po) {
                if (in_array($po->status, ['draft', 'batal'], true)) continue;
                foreach ($po->detail as $d) {
                    if ($d->jumlah_diterima > 0) {
                        $barang[] = ['nama' => $d->barang->nama_barang ?? '-', 'jumlah' => $d->jumlah_diterima, 'satuan' => $d->barang->satuan ?? ''];
                    }
                }
            }
        }

        return CetakTemplate::view('do', $pengiriman->company, [
            'pengiriman' => $pengiriman,
            'daftarSo'   => $daftarSo,
            'barang'     => $barang,
        ]);
    }

    public function edit(Pengiriman $pengiriman)
    {
        $this->cekKelola();
        $this->pastikanBisaDiubah($pengiriman);

        $pengiriman->load(['proyek.po.detail.barang', 'proyek.anggota.user']);

        return view('pengiriman.form', [
            'proyek'     => $pengiriman->proyek,
            'kel'        => $pengiriman->proyek->kelengkapanBarang(),
            'pengiriman' => $pengiriman,
        ]);
    }

    public function update(Request $request, Pengiriman $pengiriman)
    {
        $this->cekKelola();
        $this->pastikanBisaDiubah($pengiriman);

        $data = $this->validasi($request);

        // Jadwal yang diubah harus dikonfirmasi ulang oleh Sales.
        $pengiriman->update($data + [
            'status'            => 'diusulkan',
            'alasan_revisi'     => null,
            'dikonfirmasi_oleh' => null,
            'dikonfirmasi_at'   => null,
        ]);

        return redirect()->route('pengiriman.show', $pengiriman)
            ->with('success', 'Jadwal diperbarui dan diajukan ulang untuk konfirmasi.');
    }

    // Sales (pemilik paket) atau Gudang menyetujui jadwal
    public function konfirmasi(Pengiriman $pengiriman)
    {
        $this->cekAksesPengiriman($pengiriman);
        $this->pastikanBisaKonfirmasi($pengiriman);

        $pengiriman->update([
            'status'            => 'terjadwal',
            'alasan_revisi'     => null,
            'dikonfirmasi_oleh' => auth()->id(),
            'dikonfirmasi_at'   => now(),
        ]);

        return back()->with('success', 'Jadwal dikonfirmasi. Tim Pengiriman bisa memproses pengiriman.');
    }

    // Sales meminta jadwal diubah
    public function mintaRevisi(Request $request, Pengiriman $pengiriman)
    {
        $this->cekAksesPengiriman($pengiriman);
        $this->pastikanBisaKonfirmasi($pengiriman);

        $request->validate(['alasan_revisi' => 'required|string|max:500']);

        $pengiriman->update([
            'status'        => 'revisi',
            'alasan_revisi' => $request->alasan_revisi,
        ]);

        return back()->with('success', 'Permintaan revisi dikirim ke tim Pengiriman.');
    }

    // Barang berangkat
    public function kirim(Pengiriman $pengiriman)
    {
        $this->cekKelola();
        if ($pengiriman->status !== 'terjadwal') {
            return back()->with('error', 'Hanya jadwal yang sudah dikonfirmasi Sales yang bisa diberangkatkan.');
        }

        DB::transaction(function () use ($pengiriman) {
            $pengiriman->update(['status' => 'dikirim', 'dikirim_at' => now()]);

            ProyekMilestone::where('proyek_id', $pengiriman->proyek_id)
                ->where('judul', self::MILESTONE_KIRIM)
                ->where('status', 'belum')
                ->update(['status' => 'proses']);
        });

        return back()->with('success', 'Status diubah: barang dalam pengiriman.');
    }

    // Barang sampai di customer
    public function terima(Pengiriman $pengiriman)
    {
        $this->cekKelola();
        if ($pengiriman->status !== 'dikirim') {
            return back()->with('error', 'Hanya pengiriman yang sedang berjalan yang bisa ditandai diterima.');
        }

        DB::transaction(function () use ($pengiriman) {
            $pengiriman->update(['status' => 'diterima', 'diterima_at' => now()]);

            ProyekMilestone::where('proyek_id', $pengiriman->proyek_id)
                ->where('judul', self::MILESTONE_KIRIM)
                ->where('status', '!=', 'selesai')
                ->update(['status' => 'selesai', 'tanggal_selesai' => now()]);
        });

        return back()->with('success', 'Pengiriman diterima customer. Milestone "Pengiriman ke Customer" ditandai selesai.');
    }

    public function batalkan(Request $request, Pengiriman $pengiriman)
    {
        $this->cekKelola();
        if (!in_array($pengiriman->status, ['diusulkan', 'revisi', 'terjadwal'], true)) {
            return back()->with('error', 'Jadwal ini sudah tidak bisa dibatalkan.');
        }

        $request->validate(['alasan_batal' => 'required|string|max:500']);

        $pengiriman->update([
            'status'       => 'dibatalkan',
            'alasan_batal' => $request->alasan_batal,
        ]);

        return redirect()->route('pengiriman.index')->with('success', 'Jadwal pengiriman dibatalkan.');
    }

    // ---------------------------------------------------------------- helper

    private function validasi(Request $request): array
    {
        return $request->validate([
            'tanggal_kirim'   => 'required|date|after_or_equal:today',
            'jam'             => 'nullable|date_format:H:i',
            'alamat_tujuan'   => 'required|string|max:1000',
            'penerima_nama'   => 'nullable|string|max:150',
            'penerima_kontak' => 'nullable|string|max:100',
            'kendaraan'       => 'nullable|string|max:100',
            'driver'          => 'nullable|string|max:100',
            'catatan'         => 'nullable|string|max:1000',
        ]);
    }

    private function pastikanProyekBisaDijadwalkan(Proyek $proyek): void
    {
        if ($proyek->status !== 'aktif') {
            abort(422, 'Hanya proyek berstatus Aktif yang bisa dijadwalkan pengirimannya.');
        }

        $sedangBerjalan = $proyek->pengiriman()
            ->whereIn('status', ['diusulkan', 'revisi', 'terjadwal', 'dikirim'])
            ->exists();
        if ($sedangBerjalan) {
            abort(422, 'Proyek ini masih punya jadwal pengiriman yang berjalan. Selesaikan atau batalkan dulu.');
        }
    }

    private function pastikanBisaDiubah(Pengiriman $pengiriman): void
    {
        if (!in_array($pengiriman->status, ['diusulkan', 'revisi', 'terjadwal'], true)) {
            abort(422, 'Jadwal yang sudah berangkat/selesai/dibatalkan tidak bisa diubah.');
        }
    }

    private function pastikanBisaKonfirmasi(Pengiriman $pengiriman): void
    {
        if (!PengirimanService::bisaKonfirmasi(auth()->user(), $pengiriman->proyek)) {
            abort(403, 'Hanya Sales pemilik paket ini atau tim Gudang yang bisa mengonfirmasi jadwal.');
        }
        if ($pengiriman->status !== 'diusulkan') {
            abort(422, 'Jadwal ini tidak sedang menunggu konfirmasi.');
        }
    }
}
