<?php

namespace App\Http\Controllers;

use App\Models\Po;
use App\Models\PoDetail;
use App\Models\Fb;
use App\Models\FbBayar;
use App\Models\Supplier;
use App\Models\Proyek;
use App\Models\GudangBarang;
use App\Models\GudangStokMasuk;
use Illuminate\Http\Request;

class PoController extends Controller
{
    private function cekAkses()
{
    if (!in_array(auth()->user()->role_id, [1, 2, 3, 11, 14])) {
        abort(403, 'Akses ditolak.');
    }
}

    public function index()
    {
        $this->cekAkses();
        $po = Po::with(['supplier', 'creator'])->orderBy('created_at', 'desc')->get();
        return view('keuangan.po.index', compact('po'));
    }

    public function create()
    {
        $this->cekAkses();
        $suppliers = Supplier::where('is_active', 1)->orderBy('nama')->get();
        $proyek    = Proyek::where('status', 'aktif')->orderBy('nama_proyek')->get();
        $barang    = GudangBarang::orderBy('nama_barang')->get();
        $no_po     = 'PO-' . date('Ymd') . '-' . str_pad(Po::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
        return view('keuangan.po.create', compact('suppliers', 'proyek', 'barang', 'no_po'));
    }

    public function store(Request $request)
    {
        $this->cekAkses();
        $request->validate([
            'no_po'       => 'required|unique:po,no_po',
            'tanggal'     => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'barang_id'   => 'required|array|min:1',
            'jumlah'      => 'required|array',
            'harga'       => 'required|array',
        ]);

        $po = Po::create([
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

    // Catat barang datang → auto update stok gudang
    public function storeBarangDatang(Request $request, Po $po)
    {
        $this->cekAkses();
        $request->validate([
            'tanggal'   => 'required|date',
            'barang_id' => 'required|array',
            'jumlah'    => 'required|array',
        ]);

        foreach ($request->barang_id as $i => $barangId) {
            $jumlah = (int) $request->jumlah[$i];
            if ($jumlah <= 0) continue;

            // Update jumlah diterima di PO detail
            $detail = PoDetail::where('po_id', $po->id)
                ->where('barang_id', $barangId)->first();
            if ($detail) {
                $detail->increment('jumlah_diterima', $jumlah);
            }

            // Auto tambah stok gudang
            GudangStokMasuk::create([
                'barang_id'  => $barangId,
                'tanggal'    => $request->tanggal,
                'jumlah'     => $jumlah,
                'sisa'       => $jumlah,
                'supplier'   => $po->supplier->nama ?? null,
                'no_dokumen' => $po->no_po,
                'keterangan' => 'Auto dari PO: ' . $po->no_po,
                'created_by' => auth()->id(),
            ]);
        }

        // Cek apakah semua barang sudah diterima
        $allDone = $po->detail->every(fn($d) => $d->jumlah_diterima >= $d->jumlah);
        $po->update(['status' => $allDone ? 'selesai' : 'partial']);

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
        $no_fb = 'FB-' . date('Ymd') . '-' . str_pad(Fb::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);

        Fb::create([
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