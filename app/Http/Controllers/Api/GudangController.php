<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GudangBarang;
use App\Models\GudangSerialNumber;
use App\Models\GudangStokMasuk;
use App\Models\GudangStokKeluar;
use App\Models\GudangFifoDetail;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    // List semua barang
    public function index(Request $request)
    {
        $barang = GudangBarang::with('kategori')
            ->where('company_id', $request->user()->company_id)
            ->get()
            ->map(fn($b) => [
                'id'          => $b->id,
                'kode'        => $b->kode_barang,
                'nama'        => $b->nama_barang,
                'kategori'    => $b->kategori->nama ?? '-',
                'satuan'      => $b->satuan,
                'stok'        => $b->total_stok,
                'stok_min'    => $b->stok_minimum,
                'has_sn'      => $b->has_sn,
                'status_stok' => $b->total_stok <= 0 ? 'habis' : ($b->total_stok <= $b->stok_minimum ? 'menipis' : 'aman'),
            ]);

        return response()->json(['success' => true, 'data' => $barang]);
    }

    // Detail barang + SN tersedia
    public function show(Request $request, $id)
    {
        $barang = GudangBarang::with(['kategori', 'serialNumbers' => function($q) {
            $q->where('status', 'tersedia');
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'       => $barang->id,
                'kode'     => $barang->kode_barang,
                'nama'     => $barang->nama_barang,
                'satuan'   => $barang->satuan,
                'stok'     => $barang->total_stok,
                'has_sn'   => $barang->has_sn,
                'sn_list'  => $barang->serialNumbers->map(fn($sn) => [
                    'id'     => $sn->id,
                    'sn'     => $sn->serial_number,
                    'kondisi'=> $sn->kondisi,
                ]),
            ]
        ]);
    }

    // Cari barang/SN via scan barcode
    public function scan(Request $request)
    {
        $kode = $request->kode;

        // Cek apakah SN
        $sn = GudangSerialNumber::with('barang')
            ->where('serial_number', $kode)
            ->first();

        if ($sn) {
            return response()->json([
                'success' => true,
                'type'    => 'serial_number',
                'data'    => [
                    'sn_id'       => $sn->id,
                    'sn'          => $sn->serial_number,
                    'status'      => $sn->status,
                    'kondisi'     => $sn->kondisi,
                    'barang_id'   => $sn->barang->id,
                    'nama_barang' => $sn->barang->nama_barang,
                    'kode_barang' => $sn->barang->kode_barang,
                ]
            ]);
        }

        // Cek apakah kode barang
        $barang = GudangBarang::where('kode_barang', $kode)
            ->where('company_id', $request->user()->company_id)
            ->first();

        if ($barang) {
            return response()->json([
                'success' => true,
                'type'    => 'barang',
                'data'    => [
                    'id'     => $barang->id,
                    'kode'   => $barang->kode_barang,
                    'nama'   => $barang->nama_barang,
                    'stok'   => $barang->total_stok,
                    'satuan' => $barang->satuan,
                    'has_sn' => $barang->has_sn,
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Kode tidak ditemukan!'], 404);
    }

    // Catat stok masuk via mobile
    public function storeMasuk(Request $request)
    {
        $request->validate([
            'barang_id'      => 'required|exists:gudang_barang,id',
            'jumlah'         => 'required|integer|min:1',
            'tanggal'        => 'required|date',
            'serial_numbers' => 'nullable|array',
        ]);

        $barang = GudangBarang::findOrFail($request->barang_id);

        $masuk = GudangStokMasuk::create([
            'barang_id'  => $barang->id,
            'tanggal'    => $request->tanggal,
            'jumlah'     => $request->jumlah,
            'sisa'       => $request->jumlah,
            'supplier'   => $request->supplier,
            'no_dokumen' => $request->no_dokumen,
            'keterangan' => $request->keterangan ?? 'Input via Mobile',
            'created_by' => $request->user()->id,
        ]);

        // Simpan SN jika ada
        if ($barang->has_sn && !empty($request->serial_numbers)) {
            foreach ($request->serial_numbers as $sn) {
                GudangSerialNumber::create([
                    'barang_id'     => $barang->id,
                    'masuk_id'      => $masuk->id,
                    'serial_number' => $sn,
                    'kondisi'       => 'baru',
                    'status'        => 'tersedia',
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Stok masuk berhasil dicatat!',
        ]);
    }

    // Catat stok keluar via mobile (FIFO)
    public function storeKeluar(Request $request)
    {
        $request->validate([
            'barang_id'      => 'required|exists:gudang_barang,id',
            'jumlah'         => 'required|integer|min:1',
            'tanggal'        => 'required|date',
            'serial_numbers' => 'nullable|array',
        ]);

        $barang = GudangBarang::findOrFail($request->barang_id);

        if ($request->jumlah > $barang->total_stok) {
            return response()->json(['success' => false, 'message' => 'Stok tidak cukup! Stok tersedia: ' . $barang->total_stok], 400);
        }

        $keluar = GudangStokKeluar::create([
            'barang_id'  => $barang->id,
            'tanggal'    => $request->tanggal,
            'jumlah'     => $request->jumlah,
            'tujuan'     => $request->tujuan,
            'keterangan' => $request->keterangan ?? 'Output via Mobile',
            'created_by' => $request->user()->id,
        ]);

        // FIFO
        $sisaKeluar = $request->jumlah;
        $batches    = GudangStokMasuk::where('barang_id', $barang->id)
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

        // Update SN
        if ($barang->has_sn && !empty($request->serial_numbers)) {
            GudangSerialNumber::whereIn('id', $request->serial_numbers)
                ->update(['status' => 'terjual', 'keluar_id' => $keluar->id]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Stok keluar berhasil dicatat dengan FIFO!',
        ]);
    }
}