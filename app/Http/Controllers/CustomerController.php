<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
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
        $customers = Customer::orderBy('nama')->get();
        return view('keuangan.customer.index', compact('customers'));
    }

    public function create()
    {
        $this->cekAkses();
        // Auto generate kode customer
        $lastKode = Customer::orderBy('id', 'desc')->first();
        $noUrut   = $lastKode ? (intval(substr($lastKode->kode, 3)) + 1) : 1;
        $kode     = 'CUS' . str_pad($noUrut, 4, '0', STR_PAD_LEFT);
        return view('keuangan.customer.create', compact('kode'));
    }

    public function store(Request $request)
    {
        $this->cekAkses();
        $request->validate([
            'kode' => 'required|unique:customers,kode',
            'nama' => 'required|string|max:150',
        ]);

        Customer::create([
            'kode'              => $request->kode,
            'nama'              => $request->nama,
            'sales_pic'         => $request->sales_pic,
            'termin_pembayaran' => $request->termin_pembayaran,
            'batas_jtempo'      => $request->batas_jtempo,
            'batas_piutang'     => $request->batas_piutang,
            'rayon'             => $request->rayon,
            'coa_piutang'       => $request->coa_piutang,
            'tipe_harga_jual'   => $request->tipe_harga_jual ?? 1,
            'no_npwp'           => $request->no_npwp,
            'diskon_persen'     => $request->diskon_persen,
            'keterangan'        => $request->keterangan,
            'termasuk_supplier' => $request->has('termasuk_supplier') ? 1 : 0,
            'lokasi'            => $request->lokasi,
            'alamat1'           => $request->alamat1,
            'alamat2'           => $request->alamat2,
            'alamat3'           => $request->alamat3,
            'kota'              => $request->kota,
            'propinsi'          => $request->propinsi,
            'kontak'            => $request->kontak,
            'tgl_lahir'         => $request->tgl_lahir,
            'phone1'            => $request->phone1,
            'phone2'            => $request->phone2,
            'phone3'            => $request->phone3,
            'phone4'            => $request->phone4,
            'phone5'            => $request->phone5,
            'fax1'              => $request->fax1,
            'fax2'              => $request->fax2,
            'bank_account'      => $request->bank_account,
            'default_kirim'     => $request->has('default_kirim') ? 1 : 0,
            'default_tagihan'   => $request->has('default_tagihan') ? 1 : 0,
            'default_pajak'     => $request->has('default_pajak') ? 1 : 0,
            'is_active'         => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('customer.index')->with('success', 'Customer berhasil ditambahkan!');
    }

    public function edit(Customer $customer)
    {
        $this->cekAkses();
        return view('keuangan.customer.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->cekAkses();
        $request->validate([
            'kode' => 'required|unique:customers,kode,' . $customer->id,
            'nama' => 'required|string|max:150',
        ]);

        $customer->update([
            'kode'              => $request->kode,
            'nama'              => $request->nama,
            'sales_pic'         => $request->sales_pic,
            'termin_pembayaran' => $request->termin_pembayaran,
            'batas_jtempo'      => $request->batas_jtempo,
            'batas_piutang'     => $request->batas_piutang,
            'rayon'             => $request->rayon,
            'coa_piutang'       => $request->coa_piutang,
            'tipe_harga_jual'   => $request->tipe_harga_jual ?? 1,
            'no_npwp'           => $request->no_npwp,
            'diskon_persen'     => $request->diskon_persen,
            'keterangan'        => $request->keterangan,
            'termasuk_supplier' => $request->has('termasuk_supplier') ? 1 : 0,
            'lokasi'            => $request->lokasi,
            'alamat1'           => $request->alamat1,
            'alamat2'           => $request->alamat2,
            'alamat3'           => $request->alamat3,
            'kota'              => $request->kota,
            'propinsi'          => $request->propinsi,
            'kontak'            => $request->kontak,
            'tgl_lahir'         => $request->tgl_lahir,
            'phone1'            => $request->phone1,
            'phone2'            => $request->phone2,
            'phone3'            => $request->phone3,
            'phone4'            => $request->phone4,
            'phone5'            => $request->phone5,
            'fax1'              => $request->fax1,
            'fax2'              => $request->fax2,
            'bank_account'      => $request->bank_account,
            'default_kirim'     => $request->has('default_kirim') ? 1 : 0,
            'default_tagihan'   => $request->has('default_tagihan') ? 1 : 0,
            'default_pajak'     => $request->has('default_pajak') ? 1 : 0,
            'is_active'         => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('customer.index')->with('success', 'Customer berhasil diupdate!');
    }

    public function destroy(Customer $customer)
    {
        $this->cekAkses();
        $customer->delete();
        return redirect()->route('customer.index')->with('success', 'Customer berhasil dihapus!');
    }
}