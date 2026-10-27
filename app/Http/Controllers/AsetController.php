<?php

namespace App\Http\Controllers;

use App\Support\HakAkses;
use App\Models\Aset;
use App\Models\AsetKategori;
use App\Models\AsetRiwayat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AsetController extends Controller
{
    private function cekAkses()
    {
        if (!HakAkses::bisa('aset')) {
            abort(403, 'Akses ditolak.');
        }
    }

    // Daftar aset
    public function index(Request $request)
    {
        $this->cekAkses();
        $query = Aset::with(['kategori', 'pemegang', 'company'])
            ->where('status', '!=', 'dihapus');

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_aset', 'like', "%{$q}%")
                    ->orWhere('kode_aset', 'like', "%{$q}%")
                    ->orWhere('serial_number', 'like', "%{$q}%");
            });
        }

        $aset = $query->orderBy('nama_aset')->get();
        $kategori = AsetKategori::orderBy('nama')->get();

        return view('aset.index', compact('aset', 'kategori'));
    }

    // Form tambah aset
    public function create()
    {
        $this->cekAkses();
        $kategori = AsetKategori::orderBy('nama')->get();
        $karyawan = User::where('is_active', true)->forCurrentCompany()->orderBy('name')->get();
        return view('aset.create', compact('kategori', 'karyawan'));
    }

    // Simpan aset baru
    public function store(Request $request)
    {
        $this->cekAkses();
        $companyId = $this->companyIdUntukSimpan();
        $request->validate([
            'kode_aset'      => ['required', 'string', 'max:100', Rule::unique('aset', 'kode_aset')->where('company_id', $companyId)],
            'nama_aset'      => 'required|string|max:255',
            'kategori_id'    => 'nullable|exists:aset_kategori,id',
            'merk'           => 'nullable|string|max:150',
            'model'          => 'nullable|string|max:150',
            'serial_number'  => 'nullable|string|max:150',
            'spesifikasi'    => 'nullable|string',
            'tanggal_beli'   => 'nullable|date',
            'harga_beli'     => 'nullable|numeric|min:0',
            'kondisi'        => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'dipegang_oleh'  => ['nullable', Rule::exists('users', 'id')->where('company_id', $companyId)],
            'catatan'        => 'nullable|string',
        ]);

        $status = $request->dipegang_oleh ? 'dipakai' : 'tersedia';

        $aset = Aset::create([
            'company_id'     => $companyId,
            'kode_aset'      => $request->kode_aset,
            'nama_aset'      => $request->nama_aset,
            'kategori_id'    => $request->kategori_id,
            'merk'           => $request->merk,
            'model'          => $request->model,
            'serial_number'  => $request->serial_number,
            'spesifikasi'    => $request->spesifikasi,
            'tanggal_beli'   => $request->tanggal_beli,
            'harga_beli'     => $request->harga_beli,
            'kondisi'        => $request->kondisi,
            'status'         => $status,
            'dipegang_oleh'  => $request->dipegang_oleh,
            'catatan'        => $request->catatan,
            'created_by'     => auth()->id(),
        ]);

        // Catatan awal riwayat kepemilikan
        AsetRiwayat::create([
            'aset_id'         => $aset->id,
            'user_id'         => $request->dipegang_oleh,
            'status'          => $request->dipegang_oleh ? 'dipakai' : 'gudang',
            'tanggal_mulai'   => now()->toDateString(),
            'catatan'         => 'Aset baru didaftarkan.',
            'created_by'      => auth()->id(),
        ]);

        return redirect()->route('aset.index')->with('success', 'Aset berhasil didaftarkan!');
    }

    // Detail aset + riwayat lengkap
    public function show(Aset $aset)
    {
        $this->cekAkses();
        $aset->load(['kategori', 'pemegang', 'company', 'riwayat.user', 'riwayat.creator']);
        $karyawan = User::where('is_active', true)->forCurrentCompany()->orderBy('name')->get();
        return view('aset.show', compact('aset', 'karyawan'));
    }

    // Form edit aset
    public function edit(Aset $aset)
    {
        $this->cekAkses();
        $kategori = AsetKategori::orderBy('nama')->get();
        return view('aset.edit', compact('aset', 'kategori'));
    }

    // Simpan perubahan data aset (bukan pindah tangan — itu fungsi terpisah)
    public function update(Request $request, Aset $aset)
    {
        $this->cekAkses();
        $request->validate([
            'kode_aset'      => ['required', 'string', 'max:100', Rule::unique('aset', 'kode_aset')->where('company_id', $aset->company_id)->ignore($aset->id)],
            'nama_aset'      => 'required|string|max:255',
            'kategori_id'    => 'nullable|exists:aset_kategori,id',
            'merk'           => 'nullable|string|max:150',
            'model'          => 'nullable|string|max:150',
            'serial_number'  => 'nullable|string|max:150',
            'spesifikasi'    => 'nullable|string',
            'tanggal_beli'   => 'nullable|date',
            'harga_beli'     => 'nullable|numeric|min:0',
            'kondisi'        => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'catatan'        => 'nullable|string',
        ]);

        $aset->update($request->only([
            'kode_aset', 'nama_aset', 'kategori_id', 'merk', 'model',
            'serial_number', 'spesifikasi', 'tanggal_beli', 'harga_beli',
            'kondisi', 'catatan',
        ]));

        return redirect()->route('aset.show', $aset)->with('success', 'Data aset berhasil diperbarui!');
    }

    // Pindah tangan / ubah status pemegang — otomatis nutup riwayat lama & buka riwayat baru
    public function pindahTangan(Request $request, Aset $aset)
    {
        $this->cekAkses();
        $request->validate([
            'status_baru'   => 'required|in:dipakai,gudang,diperbaiki,hilang',
            'user_id'       => ['nullable', 'required_if:status_baru,dipakai', Rule::exists('users', 'id')->where('company_id', $aset->company_id)],
            'catatan'       => 'nullable|string',
        ]);

        // Tutup riwayat yang masih berlangsung (tanggal_selesai masih null)
        AsetRiwayat::where('aset_id', $aset->id)
            ->whereNull('tanggal_selesai')
            ->update(['tanggal_selesai' => now()->toDateString()]);

        // Buka riwayat baru
        AsetRiwayat::create([
            'aset_id'       => $aset->id,
            'user_id'       => $request->status_baru === 'dipakai' ? $request->user_id : null,
            'status'        => $request->status_baru,
            'tanggal_mulai' => now()->toDateString(),
            'catatan'       => $request->catatan,
            'created_by'    => auth()->id(),
        ]);

        // Update status & pemegang terkini di tabel aset
        $statusAsetMap = [
            'dipakai'    => 'dipakai',
            'gudang'     => 'tersedia',
            'diperbaiki' => 'diperbaiki',
            'hilang'     => 'hilang',
        ];
        $aset->update([
            'status'        => $statusAsetMap[$request->status_baru],
            'dipegang_oleh' => $request->status_baru === 'dipakai' ? $request->user_id : null,
            'kondisi'       => $request->status_baru === 'hilang' ? 'hilang' : $aset->kondisi,
        ]);

        return back()->with('success', 'Status aset berhasil diperbarui!');
    }

    // Hapus aset (soft — status jadi "dihapus" biar riwayat tetap kejaga)
    public function destroy(Aset $aset)
    {
        $this->cekAkses();
        $aset->update(['status' => 'dihapus', 'dipegang_oleh' => null]);
        return redirect()->route('aset.index')->with('success', 'Aset berhasil dihapus dari daftar aktif.');
    }
}