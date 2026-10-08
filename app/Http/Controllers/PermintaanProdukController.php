<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PermintaanProduk;
use App\Models\PermintaanProdukItem;
use App\Models\ProdukOpsi;
use App\Models\Proyek;
use App\Models\Tayang;
use App\Services\ProdukTayangService as Akses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermintaanProdukController extends Controller
{
    private function cekLihat(): void
    {
        if (!Akses::lihatPermintaan(auth()->user())) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function cekProduk(): void
    {
        if (!Akses::kelolaProduk(auth()->user())) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function cekBuat(): void
    {
        $u = auth()->user();
        if (!($u->isSuperAdmin() || in_array((int) $u->role_id, [1, 5, 11], true))) {
            abort(403, 'Hanya Sales yang bisa membuat permintaan produk.');
        }
    }

    private function cekPermintaan(PermintaanProduk $p): void
    {
        $this->cekLihat();
        if (!Akses::bisaLihatPermintaan(auth()->user(), $p)) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index(Request $request)
    {
        $this->cekLihat();
        $user = auth()->user();
        $filter = $request->query('tampil', 'berjalan');

        $q = PermintaanProduk::with(['creator', 'proyek', 'picProduk'])->withCount('item')->orderBy('created_at', 'desc');
        if (!Akses::lihatSemuaPermintaan($user)) {
            $q->where('created_by', $user->id);
        }
        if ($filter === 'berjalan') {
            $q->whereIn('status', ['baru', 'dicari', 'opsi_siap']);
        } elseif ($filter === 'selesai') {
            $q->whereIn('status', ['dikonfirmasi', 'dibatalkan']);
        }

        return view('produk.index', [
            'permintaan' => $q->get(),
            'filter'     => $filter,
            'bisaBuat'   => $user->isSuperAdmin() || in_array((int) $user->role_id, [1, 5, 11], true),
        ]);
    }

    public function create()
    {
        $this->cekBuat();
        return view('produk.create', $this->dataForm());
    }

    private function dataForm(): array
    {
        $user = auth()->user();
        $proyek = Proyek::whereNotIn('status', ['selesai', 'dibatalkan'])->orderBy('nama_proyek');
        if ((int) $user->role_id === 5 && !$user->isSuperAdmin()) {
            $proyek->whereHas('anggota', fn($a) => $a->where('user_id', $user->id));
        }
        return [
            'proyek'    => $proyek->get(['id', 'kode_proyek', 'nama_proyek']),
            'customers' => Customer::where('is_active', 1)->orderBy('nama')->get(['id', 'nama']),
        ];
    }

    public function store(Request $request)
    {
        $this->cekBuat();

        $data = $request->validate([
            'judul'                    => 'required|string|max:200',
            'proyek_id'                => 'nullable|integer',
            'customer_id'              => 'nullable|integer',
            'sumber'                   => 'nullable|string|max:100',
            'target_tanggal'           => 'nullable|date|after_or_equal:today',
            'catatan'                  => 'nullable|string|max:2000',
            'item'                     => 'required|array|min:1|max:50',
            'item.*.nama_kebutuhan'    => 'required|string|max:200',
            'item.*.spesifikasi'       => 'nullable|string|max:3000',
            'item.*.jumlah'            => 'required|integer|min:1|max:1000000',
            'item.*.satuan'            => 'nullable|string|max:30',
        ]);

        // proyek / customer harus milik PT yang sama (scope company otomatis lewat model)
        if (!empty($data['proyek_id']) && !Proyek::whereKey($data['proyek_id'])->exists()) {
            return back()->withInput()->withErrors(['proyek_id' => 'Proyek tidak ditemukan.']);
        }
        if (!empty($data['customer_id']) && !Customer::whereKey($data['customer_id'])->exists()) {
            return back()->withInput()->withErrors(['customer_id' => 'Customer tidak ditemukan.']);
        }

        $companyId = auth()->user()->company_id;
        if (!empty($data['proyek_id'])) {
            $companyId = Proyek::whereKey($data['proyek_id'])->value('company_id');
        }
        if (!$companyId) {
            return back()->withInput()->withErrors(['judul' => 'Akun Anda belum terhubung ke PT. Pilih proyek atau hubungi Admin.']);
        }

        $permintaan = DB::transaction(function () use ($data, $companyId) {
            $prefix = 'PRM-' . date('Ymd') . '-';
            $urut = PermintaanProduk::withoutCompanyScope()
                ->where('company_id', $companyId)
                ->where('no_permintaan', 'like', $prefix . '%')
                ->count() + 1;

            $p = PermintaanProduk::create([
                'company_id'     => $companyId,
                'no_permintaan'  => $prefix . str_pad($urut, 3, '0', STR_PAD_LEFT),
                'proyek_id'      => $data['proyek_id'] ?? null,
                'customer_id'    => $data['customer_id'] ?? null,
                'judul'          => $data['judul'],
                'sumber'         => $data['sumber'] ?? null,
                'target_tanggal' => $data['target_tanggal'] ?? null,
                'catatan'        => $data['catatan'] ?? null,
                'status'         => 'baru',
                'created_by'     => auth()->id(),
            ]);

            foreach ($data['item'] as $it) {
                $p->item()->create([
                    'nama_kebutuhan' => $it['nama_kebutuhan'],
                    'spesifikasi'    => $it['spesifikasi'] ?? null,
                    'jumlah'         => $it['jumlah'],
                    'satuan'         => $it['satuan'] ?? null,
                ]);
            }
            return $p;
        });

        return redirect()->route('produk.show', $permintaan)
            ->with('success', 'Permintaan dikirim ke tim Produk.');
    }

    public function show(PermintaanProduk $permintaan)
    {
        $this->cekPermintaan($permintaan);
        $user = auth()->user();

        $permintaan->load(['item.opsi', 'proyek', 'customer', 'creator', 'picProduk', 'konfirmator', 'tayang']);

        return view('produk.show', [
            'p'              => $permintaan,
            'kelolaProduk'   => Akses::kelolaProduk($user),
            'bisaKonfirmasi' => Akses::bisaKonfirmasi($user, $permintaan),
            'lihatHargaBeli' => Akses::lihatHargaBeli($user),
            'bisaBatal'      => Akses::kelolaProduk($user) || (int) $permintaan->created_by === (int) $user->id,
        ]);
    }

    // Produk mengambil permintaan
    public function ambil(PermintaanProduk $permintaan)
    {
        $this->cekProduk();
        if ($permintaan->status !== 'baru') {
            return back()->with('error', 'Permintaan ini sudah diambil.');
        }
        $permintaan->update(['status' => 'dicari', 'produk_pic' => auth()->id(), 'diambil_at' => now()]);
        return back()->with('success', 'Permintaan diambil. Silakan tambahkan opsi barang untuk tiap item.');
    }

    public function tambahOpsi(Request $request, PermintaanProduk $permintaan, PermintaanProdukItem $item)
    {
        $this->cekProduk();
        $this->pastikanItem($permintaan, $item);
        $this->pastikanStatus($permintaan, 'dicari', 'Opsi hanya bisa ditambah saat permintaan berstatus "Sedang Dicarikan".');

        $data = $request->validate([
            'nama_barang'            => 'required|string|max:255',
            'merk'                   => 'nullable|string|max:100',
            'spesifikasi_ditawarkan' => 'nullable|string|max:3000',
            'harga_beli'             => 'nullable|numeric|min:0',
            'harga_jual'             => 'nullable|numeric|min:0',
            'supplier_nama'          => 'nullable|string|max:255',
            'link_sumber'            => 'nullable|url|max:500',
            'catatan'                => 'nullable|string|max:1000',
        ]);

        $item->opsi()->create($data + ['created_by' => auth()->id()]);

        return back()->with('success', 'Opsi barang ditambahkan.');
    }

    public function hapusOpsi(PermintaanProduk $permintaan, ProdukOpsi $opsi)
    {
        $this->cekProduk();
        $this->pastikanStatus($permintaan, 'dicari', 'Opsi hanya bisa dihapus saat permintaan berstatus "Sedang Dicarikan".');
        $this->pastikanItem($permintaan, $opsi->item);

        $opsi->delete();
        return back()->with('success', 'Opsi dihapus.');
    }

    // Produk menyerahkan opsi ke Sales
    public function kirimKeSales(PermintaanProduk $permintaan)
    {
        $this->cekProduk();
        $this->pastikanStatus($permintaan, 'dicari', 'Permintaan ini tidak sedang dicarikan.');

        $permintaan->load('item.opsi');
        $kosong = $permintaan->item->filter(fn($i) => $i->opsi->isEmpty());
        if ($kosong->isNotEmpty()) {
            return back()->with('error', 'Masih ada item tanpa opsi: ' . $kosong->pluck('nama_kebutuhan')->join(', ') . '.');
        }

        $permintaan->update(['status' => 'opsi_siap', 'opsi_dikirim_at' => now(), 'alasan_revisi' => null]);
        return back()->with('success', 'Opsi dikirim ke Sales untuk dikonfirmasi.');
    }

    // Sales memilih opsi per item -> otomatis masuk antrian Tayang
    public function konfirmasi(Request $request, PermintaanProduk $permintaan)
    {
        $this->cekPermintaan($permintaan);
        $this->pastikanBisaKonfirmasi($permintaan);
        $this->pastikanStatus($permintaan, 'opsi_siap', 'Permintaan ini tidak sedang menunggu konfirmasi.');

        $permintaan->load('item.opsi');
        $pilih = (array) $request->input('pilih', []);

        $errors = [];
        foreach ($permintaan->item as $item) {
            $opsiId = (int) ($pilih[$item->id] ?? 0);
            if (!$opsiId || !$item->opsi->contains('id', $opsiId)) {
                $errors[] = $item->nama_kebutuhan;
            }
        }
        if ($errors) {
            return back()->with('error', 'Pilih satu opsi untuk setiap item. Belum dipilih: ' . implode(', ', $errors) . '.');
        }

        DB::transaction(function () use ($permintaan, $pilih) {
            foreach ($permintaan->item as $item) {
                ProdukOpsi::where('item_id', $item->id)->update(['is_terpilih' => false]);
                ProdukOpsi::where('id', (int) $pilih[$item->id])->where('item_id', $item->id)->update(['is_terpilih' => true]);
            }

            $permintaan->update([
                'status'            => 'dikonfirmasi',
                'dikonfirmasi_oleh' => auth()->id(),
                'dikonfirmasi_at'   => now(),
            ]);

            if (!$permintaan->tayang()->exists()) {
                Tayang::create([
                    'company_id'    => $permintaan->company_id,
                    'permintaan_id' => $permintaan->id,
                    'proyek_id'     => $permintaan->proyek_id,
                    'judul'         => $permintaan->judul,
                    'status'        => 'antrian',
                ]);
            }
        });

        return back()->with('success', 'Pilihan dikonfirmasi. Permintaan masuk antrian Tayang.');
    }

    public function mintaOpsiLain(Request $request, PermintaanProduk $permintaan)
    {
        $this->cekPermintaan($permintaan);
        $this->pastikanBisaKonfirmasi($permintaan);
        $this->pastikanStatus($permintaan, 'opsi_siap', 'Permintaan ini tidak sedang menunggu konfirmasi.');

        $request->validate(['alasan_revisi' => 'required|string|max:1000']);

        $permintaan->update(['status' => 'dicari', 'alasan_revisi' => $request->alasan_revisi]);
        return back()->with('success', 'Permintaan dikembalikan ke tim Produk untuk dicarikan opsi lain.');
    }

    public function batalkan(Request $request, PermintaanProduk $permintaan)
    {
        $this->cekPermintaan($permintaan);
        $user = auth()->user();
        if (!(Akses::kelolaProduk($user) || (int) $permintaan->created_by === (int) $user->id)) {
            abort(403, 'Akses ditolak.');
        }
        if (!in_array($permintaan->status, ['baru', 'dicari', 'opsi_siap'], true)) {
            return back()->with('error', 'Permintaan ini sudah tidak bisa dibatalkan.');
        }

        $request->validate(['alasan_batal' => 'required|string|max:1000']);
        $permintaan->update(['status' => 'dibatalkan', 'alasan_batal' => $request->alasan_batal]);

        return redirect()->route('produk.index')->with('success', 'Permintaan dibatalkan.');
    }

    // ---------------------------------------------------------------- helper

    private function pastikanItem(PermintaanProduk $permintaan, ?PermintaanProdukItem $item): void
    {
        if (!$item || (int) $item->permintaan_id !== (int) $permintaan->id) {
            abort(404);
        }
    }

    private function pastikanStatus(PermintaanProduk $permintaan, string $status, string $pesan): void
    {
        if ($permintaan->status !== $status) {
            abort(422, $pesan);
        }
    }

    private function pastikanBisaKonfirmasi(PermintaanProduk $permintaan): void
    {
        if (!Akses::bisaKonfirmasi(auth()->user(), $permintaan)) {
            abort(403, 'Hanya Sales pemilik permintaan ini yang bisa mengonfirmasi.');
        }
    }
}
