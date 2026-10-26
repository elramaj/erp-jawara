<?php

namespace App\Http\Controllers;

use App\Models\Po;
use App\Services\PenerimaanBarangService;
use Illuminate\Http\Request;

/**
 * Halaman Penerimaan Barang untuk Gudang (dan PO/Direktur/Admin): mencatat barang datang dari PO
 * TANPA membuka halaman PO penuh. Harga, faktur, dan pembayaran tidak ditampilkan di sini.
 */
class PenerimaanController extends Controller
{
    private function cekAkses(): void
    {
        $u = auth()->user();
        abort_unless($u->isSuperAdmin() || in_array((int) $u->role_id, [1, 3, 4, 11], true), 403, 'Akses ditolak.');
    }

    public function index(Request $request)
    {
        $this->cekAkses();
        $filter = $request->query('tampil', 'menunggu');

        $q = Po::with(['supplier', 'proyek', 'detail'])->orderByDesc('tanggal')->orderByDesc('id');
        match ($filter) {
            'selesai' => $q->where('status', 'selesai'),
            'semua'   => $q->whereNotIn('status', ['draft', 'batal']),
            default   => $q->whereIn('status', ['confirmed', 'partial']),
        };

        return view('penerimaan.index', ['daftar' => $q->get(), 'filter' => $filter]);
    }

    public function show(Po $po)
    {
        $this->cekAkses();
        $po->load(['supplier', 'proyek', 'detail.barang']);

        return view('penerimaan.show', [
            'po'      => $po,
            'terbuka' => !in_array($po->status, ['selesai', 'batal', 'draft'], true),
        ]);
    }

    public function store(Request $request, Po $po)
    {
        $this->cekAkses();
        $request->validate([
            'tanggal'            => 'required|date|before_or_equal:today',
            'barang_id'          => 'required|array',
            'barang_id.*'        => 'integer',
            'jumlah'             => 'required|array',
            'jumlah.*'           => 'nullable|integer|min:0|max:1000000',
            'serial_numbers'     => 'nullable|array',
            'serial_numbers.*'   => 'nullable|string|max:5000',
        ]);

        $baris = [];
        foreach ($request->barang_id as $i => $barangId) {
            $baris[] = [
                'barang_id'      => $barangId,
                'jumlah'         => $request->jumlah[$i] ?? 0,
                'serial_numbers' => $request->serial_numbers[$i] ?? null,
            ];
        }

        $n = PenerimaanBarangService::catat($po, $request->tanggal, $baris, auth()->id());

        return redirect()->route('penerimaan.show', $po)
            ->with('success', "Penerimaan {$n} jenis barang dicatat. Stok gudang bertambah dan status PO diperbarui.");
    }
}
