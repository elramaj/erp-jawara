<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
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
        $suppliers = Supplier::orderBy('nama')->get();
        return view('keuangan.supplier.index', compact('suppliers'));
    }

    public function create()
    {
        $this->cekAkses();
        // Auto generate kode supplier
        $lastKode = Supplier::orderBy('id', 'desc')->first();
        $noUrut   = $lastKode ? (intval(substr($lastKode->kode, 3)) + 1) : 1;
        $kode     = 'SUP' . str_pad($noUrut, 4, '0', STR_PAD_LEFT);
        return view('keuangan.supplier.create', compact('kode'));
    }

    public function store(Request $request)
    {
        $this->cekAkses();
        $request->validate([
            'kode' => 'required|unique:suppliers,kode',
            'nama' => 'required|string|max:150',
        ]);

        Supplier::create([
            'kode'              => $request->kode,
            'nama'              => $request->nama,
            'termin_pembayaran' => $request->termin_pembayaran,
            'batas_hutang'      => $request->batas_hutang,
            'coa_hutang'        => $request->coa_hutang,
            'no_npwp'           => $request->no_npwp,
            'diskon_persen'     => $request->diskon_persen,
            'keterangan'        => $request->keterangan,
            'termasuk_customer' => $request->has('termasuk_customer') ? 1 : 0,
            'lokasi'            => $request->lokasi,
            'alamat1'           => $request->alamat1,
            'alamat2'           => $request->alamat2,
            'alamat3'           => $request->alamat3,
            'kota'              => $request->kota,
            'propinsi'          => $request->propinsi,
            'kontak'            => $request->kontak,
            'phone1'            => $request->phone1,
            'phone2'            => $request->phone2,
            'phone3'            => $request->phone3,
            'phone4'            => $request->phone4,
            'phone5'            => $request->phone5,
            'fax1'              => $request->fax1,
            'fax2'              => $request->fax2,
            'bank_account'      => $request->bank_account,
            'default_kirim'     => $request->has('default_kirim') ? 1 : 0,
            'default_penagihan' => $request->has('default_penagihan') ? 1 : 0,
            'default_pajak'     => $request->has('default_pajak') ? 1 : 0,
            'is_active'         => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan!');
    }

    public function edit(Supplier $supplier)
    {
        $this->cekAkses();
        return view('keuangan.supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->cekAkses();
        $request->validate([
            'kode' => 'required|unique:suppliers,kode,' . $supplier->id,
            'nama' => 'required|string|max:150',
        ]);

        $supplier->update([
            'kode'              => $request->kode,
            'nama'              => $request->nama,
            'termin_pembayaran' => $request->termin_pembayaran,
            'batas_hutang'      => $request->batas_hutang,
            'coa_hutang'        => $request->coa_hutang,
            'no_npwp'           => $request->no_npwp,
            'diskon_persen'     => $request->diskon_persen,
            'keterangan'        => $request->keterangan,
            'termasuk_customer' => $request->has('termasuk_customer') ? 1 : 0,
            'lokasi'            => $request->lokasi,
            'alamat1'           => $request->alamat1,
            'alamat2'           => $request->alamat2,
            'alamat3'           => $request->alamat3,
            'kota'              => $request->kota,
            'propinsi'          => $request->propinsi,
            'kontak'            => $request->kontak,
            'phone1'            => $request->phone1,
            'phone2'            => $request->phone2,
            'phone3'            => $request->phone3,
            'phone4'            => $request->phone4,
            'phone5'            => $request->phone5,
            'fax1'              => $request->fax1,
            'fax2'              => $request->fax2,
            'bank_account'      => $request->bank_account,
            'default_kirim'     => $request->has('default_kirim') ? 1 : 0,
            'default_penagihan' => $request->has('default_penagihan') ? 1 : 0,
            'default_pajak'     => $request->has('default_pajak') ? 1 : 0,
            'is_active'         => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diupdate!');
    }

    public function destroy(Supplier $supplier)
    {
        $this->cekAkses();
        $supplier->delete();
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus!');
    }
}