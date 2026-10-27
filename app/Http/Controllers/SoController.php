<?php

namespace App\Http\Controllers;

use App\Support\HakAkses;
use App\Models\So;
use App\Models\SoDetail;
use App\Models\Sj;
use App\Models\SjDetail;
use App\Models\Fj;
use App\Models\FjBayar;
use App\Models\Customer;
use App\Models\Proyek;
use App\Models\GudangBarang;
use App\Models\GudangStokKeluar;
use App\Models\GudangFifoDetail;
use App\Models\GudangStokMasuk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Support\CetakTemplate;

class SoController extends Controller
{
    private function cekAkses()
    {
        if (!HakAkses::bisa('so')) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index()
    {
        $this->cekAkses();
        $so = So::with(['customer', 'creator', 'company'])
            ->orderBy('created_at', 'desc')->get();
        return view('keuangan.so.index', compact('so'));
    }

    public function create()
    {
        $this->cekAkses();
        $customers = Customer::where('is_active', 1)->orderBy('nama')->get();
        $proyek    = Proyek::where('status', 'aktif')
            ->orderBy('nama_proyek')->get();
        $barang    = GudangBarang::orderBy('nama_barang')->get()->map(function($b) {
                $b->stok = $b->total_stok;
                return $b;
            });
        $no_so = 'SO-' . date('Ymd') . '-' . str_pad(So::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
        return view('keuangan.so.create', compact('customers', 'proyek', 'barang', 'no_so'));
    }

    public function store(Request $request)
    {
        $this->cekAkses();
        $companyId = $this->companyIdUntukSimpan();
        $request->validate([
            'no_so'       => ['required', Rule::unique('so', 'no_so')->where('company_id', $companyId)],
            'tanggal'     => 'required|date',
            'customer_id' => ['required', Rule::exists('customers', 'id')->where('company_id', $companyId)],
            'proyek_id'   => ['nullable', Rule::exists('proyek', 'id')->where('company_id', $companyId)],
            'barang_id'   => 'required|array|min:1',
            'barang_id.*' => ['required', Rule::exists('gudang_barang', 'id')->where('company_id', $companyId)],
            'jumlah'      => 'required|array',
            'jumlah.*'    => 'required|integer|min:1',
            'harga'       => 'required|array',
            'harga.*'     => 'required|numeric|min:0',
        ]);

        $so = So::create([
            'company_id'  => $companyId,
            'no_so'       => $request->no_so,
            'tanggal'     => $request->tanggal,
            'customer_id' => $request->customer_id,
            'proyek_id'   => $request->proyek_id,
            'status'      => 'confirmed',
            'catatan'     => $request->catatan,
            'created_by'  => auth()->id(),
        ]);

        foreach ($request->barang_id as $i => $barangId) {
            SoDetail::create([
                'so_id'     => $so->id,
                'barang_id' => $barangId,
                'jumlah'    => $request->jumlah[$i],
                'harga'     => $request->harga[$i],
            ]);
        }

        return redirect()->route('so.show', $so)->with('success', 'SO berhasil dibuat!');
    }

    public function show(So $so)
    {
        $this->cekAkses();
        $so->load(['customer', 'detail.barang', 'sj.detail.barang', 'fj.pembayaran', 'creator']);
        return view('keuangan.so.show', compact('so'));
    }

    // Cetak Sales Order (template: resources/views/cetak/<template PT>/so.blade.php)
    public function cetak(So $so)
    {
        $this->cekAkses();
        $so->load(['customer', 'detail.barang', 'creator', 'proyek', 'company']);
        return CetakTemplate::view('so', $so->company, ['so' => $so]);
    }

    // Buat Surat Jalan dari SO
    public function storeSj(Request $request, So $so)
    {
        $this->cekAkses();
        $request->validate([
            'tanggal'     => 'required|date',
            'barang_id'   => 'required|array',
            'barang_id.*' => 'required|integer',
            'jumlah'      => 'required|array',
            'jumlah.*'    => 'nullable|integer|min:0',
        ]);

        // Surat Jalan hanya boleh berisi barang yang memang ada di SO ini.
        // Tanpa ini, ID barang bisa dikarang (bahkan milik PT lain) dan stok
        // gudang yang bersangkutan ikut berkurang.
        $barangDiSo = $so->detail()->pluck('barang_id')->map(fn($v) => (int) $v)->all();
        foreach ($request->barang_id as $barangId) {
            if (!in_array((int) $barangId, $barangDiSo, true)) {
                return back()->withErrors(['barang_id' => 'Ada barang yang bukan bagian dari SO ini.']);
            }
        }

        // Nomor SJ di-scope per company (pakai company_id milik SO-nya) supaya
        // tidak tabrakan urutan kalau ada lebih dari 1 company aktif dalam sistem.
        $companyId = $so->company_id;
        $no_sj = 'SJ-' . date('Ymd') . '-' . str_pad(
            Sj::whereDate('created_at', today())
                ->whereHas('so', fn($q) => $q->where('company_id', $companyId))
                ->count() + 1,
            3, '0', STR_PAD_LEFT
        );

        $sj = Sj::create([
            'no_sj'      => $no_sj,
            'tanggal'    => $request->tanggal,
            'so_id'      => $so->id,
            'status'     => 'dikirim',
            'catatan'    => $request->catatan,
            'created_by' => auth()->id(),
        ]);

        foreach ($request->barang_id as $i => $barangId) {
            $jumlah = (int) $request->jumlah[$i];
            if ($jumlah <= 0) continue;

            SjDetail::create([
                'sj_id'     => $sj->id,
                'barang_id' => $barangId,
                'jumlah'    => $jumlah,
            ]);

            $keluar = GudangStokKeluar::create([
                'barang_id'  => $barangId,
                'tanggal'    => $request->tanggal,
                'jumlah'     => $jumlah,
                'tujuan'     => 'SO: ' . $so->no_so . ' | SJ: ' . $no_sj,
                'keterangan' => 'Auto dari Surat Jalan',
                'created_by' => auth()->id(),
            ]);

            $sisaKeluar = $jumlah;
            $batches = GudangStokMasuk::where('barang_id', $barangId)
                ->where('sisa', '>', 0)
                ->orderBy('tanggal')->orderBy('id')->get();

            foreach ($batches as $batch) {
                if ($sisaKeluar <= 0) break;
                $ambil = min($batch->sisa, $sisaKeluar);
                GudangFifoDetail::create([
                    'keluar_id' => $keluar->id,
                    'masuk_id'  => $batch->id,
                    'jumlah'    => $ambil,
                ]);
                $batch->update(['sisa' => $batch->sisa - $ambil]);
                $sisaKeluar -= $ambil;
            }
        }

        return back()->with('success', 'Surat Jalan berhasil dibuat & stok gudang otomatis dikurangi!');
    }

    // Buat Faktur Jual dari SO
    public function storeFj(Request $request, So $so)
    {
        $this->cekAkses();
        $request->validate([
            'tanggal'     => 'required|date',
            'jatuh_tempo' => 'nullable|date',
        ]);

        $total = $so->detail->sum(fn($d) => $d->jumlah * $d->harga);
        $no_fj = 'FJ-' . date('Ymd') . '-' . str_pad(Fj::withoutCompanyScope()->whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);

        Fj::create([
            'company_id'  => $so->company_id,
            'no_fj'       => $no_fj,
            'tanggal'     => $request->tanggal,
            'so_id'       => $so->id,
            'total'       => $total,
            'terbayar'    => 0,
            'status'      => 'unpaid',
            'jatuh_tempo' => $request->jatuh_tempo,
            'catatan'     => $request->catatan,
            'created_by'  => auth()->id(),
        ]);

        $so->update(['status' => 'partial']);

        return back()->with('success', 'Faktur Jual berhasil dibuat!');
    }

    // Catat pembayaran FJ
    public function storeBayarFj(Request $request, Fj $fj)
    {
        $this->cekAkses();
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah'  => 'required|numeric|min:1|max:' . $fj->sisa,
            'metode'  => 'required|in:tunai,transfer,cek,lainnya',
        ]);

        FjBayar::create([
            'fj_id'      => $fj->id,
            'tanggal'    => $request->tanggal,
            'jumlah'     => $request->jumlah,
            'metode'     => $request->metode,
            'keterangan' => $request->keterangan,
            'created_by' => auth()->id(),
        ]);

        $terbayar = $fj->pembayaran()->sum('jumlah');
        $status   = $terbayar >= $fj->total ? 'paid' : 'partial';
        $fj->update(['terbayar' => $terbayar, 'status' => $status]);

        if ($status == 'paid') {
            $fj->so->update(['status' => 'selesai']);
        }

        return back()->with('success', 'Pembayaran berhasil dicatat!');
    }
}