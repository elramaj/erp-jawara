<?php

namespace App\Http\Controllers;

use App\Support\HakAkses;
use App\Models\Po;
use App\Services\PenerimaanBarangService;
use App\Support\CetakTemplate;
use App\Models\PoDetail;
use App\Models\Fb;
use App\Models\FbBayar;
use App\Models\Supplier;
use App\Models\Proyek;
use App\Models\GudangBarang;
use App\Models\GudangStokMasuk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PoController extends Controller
{
    private function cekAkses()
    {
        if (!HakAkses::bisa('po')) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index()
    {
        $this->cekAkses();
        $po = Po::with(['supplier', 'creator', 'company'])
            ->orderBy('created_at', 'desc')->get();
        return view('keuangan.po.index', compact('po'));
    }

    public function create()
    {
        $this->cekAkses();
        $suppliers = Supplier::where('is_active', 1)->orderBy('nama')->get();
        $proyek    = Proyek::where('status', 'aktif')
            ->orderBy('nama_proyek')->get();
        $barang    = GudangBarang::orderBy('nama_barang')->get();
        $no_po     = 'PO-' . date('Ymd') . '-' . str_pad(Po::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
        return view('keuangan.po.create', compact('suppliers', 'proyek', 'barang', 'no_po'));
    }

    public function store(Request $request)
    {
        $this->cekAkses();
        $companyId = $this->companyIdUntukSimpan();
        $request->validate([
            // Nomor unik PER PT (penomoran otomatis dihitung per PT, jadi PT lain boleh punya nomor sama).
            'no_po'       => ['required', Rule::unique('po', 'no_po')->where('company_id', $companyId)],
            'tanggal'     => 'required|date',
            // exists biasa tidak ikut filter PT, jadi dibatasi manual ke PT sendiri.
            'supplier_id' => ['required', Rule::exists('suppliers', 'id')->where('company_id', $companyId)],
            'proyek_id'   => ['nullable', Rule::exists('proyek', 'id')->where('company_id', $companyId)],
            'barang_id'   => 'required|array|min:1',
            'barang_id.*' => ['required', Rule::exists('gudang_barang', 'id')->where('company_id', $companyId)],
            'jumlah'      => 'required|array',
            'jumlah.*'    => 'required|integer|min:1',
            'harga'       => 'required|array',
            'harga.*'     => 'required|numeric|min:0',
        ]);

        $po = Po::create([
            'company_id'  => $companyId,
            'no_po'       => $request->no_po,
            'tanggal'     => $request->tanggal,
            'supplier_id' => $request->supplier_id,
            'proyek_id'   => $request->proyek_id,
            'status'      => 'confirmed',
            'catatan'     => $request->catatan,
            'created_by'  => auth()->id(),
        ]);

        foreach ($request->barang_id as $i => $barangId) {
            PoDetail::create([
                'po_id'     => $po->id,
                'barang_id' => $barangId,
                'jumlah'    => $request->jumlah[$i],
                'harga'     => $request->harga[$i],
            ]);
        }

        return redirect()->route('po.show', $po)->with('success', 'PO berhasil dibuat!');
    }

    public function show(Po $po)
    {
        $this->cekAkses();
        $po->load(['supplier', 'detail.barang', 'fb.pembayaran', 'creator']);
        return view('keuangan.po.show', compact('po'));
    }

    // Cetak Purchase Order (template: resources/views/cetak/<template PT>/po.blade.php)
    public function cetak(Po $po)
    {
        $this->cekAkses();
        $po->load(['supplier', 'detail.barang', 'creator', 'proyek', 'company']);
        return CetakTemplate::view('po', $po->company, ['po' => $po]);
    }

    // Catat barang datang → auto update stok gudang
    public function storeBarangDatang(Request $request, Po $po)
    {
        $this->cekAkses();
        $request->validate([
            'tanggal'   => 'required|date',
            'barang_id' => 'required|array',
            'jumlah'    => 'required|array',
        ]);

        $baris = [];
        foreach ($request->barang_id as $i => $barangId) {
            $baris[] = ['barang_id' => $barangId, 'jumlah' => $request->jumlah[$i] ?? 0];
        }

        // Logika pencatatan dipakai bersama dengan halaman Penerimaan Barang (Gudang)
        PenerimaanBarangService::catat($po, $request->tanggal, $baris, auth()->id());

        return back()->with('success', 'Barang datang berhasil dicatat & stok gudang otomatis ditambah!');
    }

    // Buat Faktur Beli dari PO
    public function storeFb(Request $request, Po $po)
    {
        $this->cekAkses();
        $request->validate([
            'tanggal'     => 'required|date',
            'jatuh_tempo' => 'nullable|date',
        ]);

        $total = $po->detail->sum(fn($d) => $d->jumlah * $d->harga);
        $no_fb = 'FB-' . date('Ymd') . '-' . str_pad(Fb::withoutCompanyScope()->whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);

        Fb::create([
            'company_id'  => $po->company_id,
            'no_fb'       => $no_fb,
            'tanggal'     => $request->tanggal,
            'po_id'       => $po->id,
            'total'       => $total,
            'terbayar'    => 0,
            'status'      => 'unpaid',
            'jatuh_tempo' => $request->jatuh_tempo,
            'catatan'     => $request->catatan,
            'created_by'  => auth()->id(),
        ]);

        return back()->with('success', 'Faktur Beli berhasil dibuat!');
    }

    // Catat pembayaran FB
    public function storeBayarFb(Request $request, Fb $fb)
    {
        $this->cekAkses();
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah'  => 'required|numeric|min:1|max:' . $fb->sisa,
            'metode'  => 'required|in:tunai,transfer,cek,lainnya',
        ]);

        FbBayar::create([
            'fb_id'      => $fb->id,
            'tanggal'    => $request->tanggal,
            'jumlah'     => $request->jumlah,
            'metode'     => $request->metode,
            'keterangan' => $request->keterangan,
            'created_by' => auth()->id(),
        ]);

        $terbayar = $fb->pembayaran()->sum('jumlah');
        $status   = $terbayar >= $fb->total ? 'paid' : 'partial';
        $fb->update(['terbayar' => $terbayar, 'status' => $status]);

        if ($status == 'paid') {
            $fb->po->update(['status' => 'selesai']);
        }

        return back()->with('success', 'Pembayaran berhasil dicatat!');
    }
}