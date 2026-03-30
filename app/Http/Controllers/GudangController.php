<?php

namespace App\Http\Controllers;

use App\Models\GudangBarang;
use App\Models\GudangKategori;
use App\Models\GudangStokMasuk;
use App\Models\GudangStokKeluar;
use App\Models\GudangFifoDetail;
use App\Models\GudangSerialNumber;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GudangController extends Controller
{
    private function cekAkses()
    {
        if (!in_array(auth()->user()->role_id, [1, 2, 3, 4, 11])) {
            abort(403, 'Akses ditolak.');
        }
    }

    // Daftar barang & stok
    public function index()
    {
        $this->cekAkses();
        $companyId = auth()->user()->company_id;
        $barang = GudangBarang::with('kategori')
            ->where('company_id', $companyId)
            ->get()->map(function($b) {
                $b->total_stok = $b->total_stok;
                return $b;
            });
        $alertStok = $barang->filter(fn($b) => $b->total_stok <= $b->stok_minimum && $b->stok_minimum > 0);
        return view('gudang.index', compact('barang', 'alertStok'));
    }

    // Form tambah barang
    public function createBarang()
    {
        $this->cekAkses();
        $kategori = GudangKategori::orderBy('nama')->get();
        return view('gudang.barang-create', compact('kategori'));
    }

    // Simpan barang baru
    public function storeBarang(Request $request)
    {
        $this->cekAkses();
        $request->validate([
            'kode_barang'  => 'required|unique:gudang_barang,kode_barang',
            'nama_barang'  => 'required|string|max:255',
            'kategori_id'  => 'nullable|exists:gudang_kategori,id',
            'satuan'       => 'required|string|max:50',
            'stok_minimum' => 'required|integer|min:0',
        ]);

        GudangBarang::create([
            'company_id'   => auth()->user()->company_id,
            'kode_barang'  => $request->kode_barang,
            'nama_barang'  => $request->nama_barang,
            'kategori_id'  => $request->kategori_id,
            'satuan'       => $request->satuan,
            'stok_minimum' => $request->stok_minimum,
            'has_sn'       => $request->has('has_sn') ? 1 : 0,
            'deskripsi'    => $request->deskripsi,
        ]);

        return redirect()->route('gudang.index')
            ->with('success', 'Barang berhasil ditambahkan!');
    }

    // Detail barang
    public function showBarang(GudangBarang $barang)
    {
        $this->cekAkses();
        $barang->load(['kategori', 'serialNumbers.masuk']);
        $stokMasuk  = GudangStokMasuk::where('barang_id', $barang->id)
            ->orderBy('tanggal')->orderBy('id')->get();
        $stokKeluar = GudangStokKeluar::where('barang_id', $barang->id)
            ->with(['proyek', 'creator'])
            ->orderBy('tanggal', 'desc')->get();
        $proyek = Proyek::where('status', 'aktif')
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('nama_proyek')->get();

        return view('gudang.barang-show', compact('barang', 'stokMasuk', 'stokKeluar', 'proyek'));
    }

    // Simpan stok masuk
    public function storeMasuk(Request $request, GudangBarang $barang)
    {
        $this->cekAkses();
        $request->validate([
            'tanggal'        => 'required|date',
            'jumlah'         => 'required|integer|min:1',
            'harga_beli'     => 'nullable|numeric',
            'supplier'       => 'nullable|string|max:150',
            'no_dokumen'     => 'nullable|string|max:100',
            'keterangan'     => 'nullable|string',
            'serial_numbers' => 'nullable|string',
        ]);

        $masuk = GudangStokMasuk::create([
            'barang_id'  => $barang->id,
            'tanggal'    => $request->tanggal,
            'jumlah'     => $request->jumlah,
            'sisa'       => $request->jumlah,
            'harga_beli' => $request->harga_beli,
            'supplier'   => $request->supplier,
            'no_dokumen' => $request->no_dokumen,
            'keterangan' => $request->keterangan,
            'created_by' => auth()->id(),
        ]);

        if ($barang->has_sn && $request->filled('serial_numbers')) {
            $sns = array_filter(array_map('trim', explode("\n", $request->serial_numbers)));
            foreach ($sns as $sn) {
                GudangSerialNumber::create([
                    'barang_id'     => $barang->id,
                    'masuk_id'      => $masuk->id,
                    'serial_number' => $sn,
                    'kondisi'       => 'baru',
                    'status'        => 'tersedia',
                ]);
            }
        }

        return back()->with('success', 'Stok masuk berhasil dicatat!');
    }

    // Simpan stok keluar (FIFO)
    public function storeKeluar(Request $request, GudangBarang $barang)
    {
        $this->cekAkses();
        $request->validate([
            'tanggal'        => 'required|date',
            'jumlah'         => 'required|integer|min:1',
            'harga_jual'     => 'nullable|numeric',
            'tujuan'         => 'nullable|string|max:255',
            'proyek_id'      => 'nullable|exists:proyek,id',
            'no_dokumen'     => 'nullable|string|max:100',
            'keterangan'     => 'nullable|string',
            'serial_numbers' => 'nullable|array',
        ]);

        $totalStok = $barang->total_stok;
        if ($request->jumlah > $totalStok) {
            return back()->withErrors(['jumlah' => "Stok tidak cukup! Stok tersedia: {$totalStok} {$barang->satuan}"]);
        }

        if ($barang->has_sn) {
            $snDipilih = $request->serial_numbers ?? [];
            if (count($snDipilih) != $request->jumlah) {
                return back()->withErrors(['jumlah' => "Jumlah SN dipilih harus sama dengan jumlah keluar ({$request->jumlah})!"]);
            }
        }

        $keluar = GudangStokKeluar::create([
            'barang_id'  => $barang->id,
            'tanggal'    => $request->tanggal,
            'jumlah'     => $request->jumlah,
            'harga_jual' => $request->harga_jual,
            'proyek_id'  => $request->proyek_id,
            'tujuan'     => $request->tujuan,
            'no_dokumen' => $request->no_dokumen,
            'keterangan' => $request->keterangan,
            'created_by' => auth()->id(),
        ]);

        $sisaKeluar = $request->jumlah;
        $batches = GudangStokMasuk::where('barang_id', $barang->id)
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

        if ($barang->has_sn && !empty($request->serial_numbers)) {
            GudangSerialNumber::whereIn('id', $request->serial_numbers)
                ->update([
                    'status'    => 'terjual',
                    'keluar_id' => $keluar->id,
                ]);
        }

        return back()->with('success', 'Stok keluar berhasil dicatat dengan metode FIFO!');
    }

    // Stok opname
    public function opname()
    {
        $this->cekAkses();
        $companyId = auth()->user()->company_id;
        $barang = GudangBarang::with('kategori')
            ->where('company_id', $companyId)
            ->get()->map(function($b) {
                $b->total_stok = $b->total_stok;
                return $b;
            });
        return view('gudang.opname', compact('barang'));
    }

    // Simpan hasil opname
    public function storeOpname(Request $request)
    {
        $this->cekAkses();
        $request->validate([
            'barang_id'  => 'required|array',
            'stok_fisik' => 'required|array',
        ]);

        foreach ($request->barang_id as $i => $barangId) {
            $barang     = GudangBarang::find($barangId);
            $stokSistem = $barang->total_stok;
            $stokFisik  = (int) $request->stok_fisik[$i];
            $selisih    = $stokFisik - $stokSistem;

            if ($selisih != 0) {
                if ($selisih > 0) {
                    GudangStokMasuk::create([
                        'barang_id'  => $barangId,
                        'tanggal'    => now()->toDateString(),
                        'jumlah'     => $selisih,
                        'sisa'       => $selisih,
                        'keterangan' => 'Penyesuaian stok opname',
                        'created_by' => auth()->id(),
                    ]);
                } else {
                    $keluar = GudangStokKeluar::create([
                        'barang_id'  => $barangId,
                        'tanggal'    => now()->toDateString(),
                        'jumlah'     => abs($selisih),
                        'keterangan' => 'Penyesuaian stok opname',
                        'created_by' => auth()->id(),
                    ]);

                    $sisaKeluar = abs($selisih);
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
            }
        }

        return redirect()->route('gudang.index')
            ->with('success', 'Stok opname berhasil disimpan!');
    }

    // Hapus barang permanen
    public function destroyBarang(GudangBarang $barang)
    {
        $this->cekAkses();

        $masukIds  = GudangStokMasuk::where('barang_id', $barang->id)->pluck('id');
        $keluarIds = GudangStokKeluar::where('barang_id', $barang->id)->pluck('id');

        GudangFifoDetail::whereIn('masuk_id', $masukIds)->delete();
        GudangFifoDetail::whereIn('keluar_id', $keluarIds)->delete();
        GudangSerialNumber::where('barang_id', $barang->id)->delete();
        GudangStokMasuk::where('barang_id', $barang->id)->delete();
        GudangStokKeluar::where('barang_id', $barang->id)->delete();

        $barang->delete();

        return redirect()->route('gudang.index')
            ->with('success', 'Barang berhasil dihapus!');
    }
}