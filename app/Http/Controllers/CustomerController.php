<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    private function cekAkses()
    {
        if (!in_array(auth()->user()->role_id, [1, 2, 3, 11, 14])) {
            abort(403, 'Akses ditolak.');
        }
    }

    /**
     * Tentukan company_id yang berlaku untuk create/update. Admin biasa
     * dipaksa ke company miliknya sendiri (walau kirim company_id lain di
     * request). Super Admin wajib pilih company dari dropdown karena
     * company_id dia sendiri bisa NULL.
     */
    private function resolveCompanyId(Request $request): int
    {
        if (auth()->user()->isSuperAdmin()) {
            $request->validate(['company_id' => 'required|exists:companies,id']);
            return (int) $request->company_id;
        }

        return (int) auth()->user()->company_id;
    }

    public function index()
    {
        $this->cekAkses();
        $customers = Customer::with('company')->orderBy('nama')->get();
        return view('keuangan.customer.index', compact('customers'));
    }

    public function create()
    {
        $this->cekAkses();
        $companies = auth()->user()->isSuperAdmin()
            ? Company::where('is_active', 1)->orderBy('nama')->get()
            : collect();
        $kode = $this->generateKode(auth()->user()->company_id);
        return view('keuangan.customer.create', compact('kode', 'companies'));
    }

    private function generateKode(?int $companyId): string
    {
        $lastKode = Customer::withoutCompanyScope()
            ->where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->first();
        $noUrut = $lastKode ? (intval(substr($lastKode->kode, 3)) + 1) : 1;
        return 'CUS' . str_pad($noUrut, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $this->cekAkses();
        $companyId = $this->resolveCompanyId($request);

        $request->validate([
            'kode' => [
                'required',
                Rule::unique('customers', 'kode')->where(fn ($q) => $q->where('company_id', $companyId)),
            ],
            'nama' => 'required|string|max:150',
        ]);

        Customer::create([
            'company_id'        => $companyId,
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
        $companies = auth()->user()->isSuperAdmin()
            ? Company::where('is_active', 1)->orderBy('nama')->get()
            : collect();
        return view('keuangan.customer.edit', compact('customer', 'companies'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->cekAkses();
        $companyId = $this->resolveCompanyId($request);

        $request->validate([
            'kode' => [
                'required',
                Rule::unique('customers', 'kode')
                    ->where(fn ($q) => $q->where('company_id', $companyId))
                    ->ignore($customer->id),
            ],
            'nama' => 'required|string|max:150',
        ]);

        $customer->update([
            'company_id'        => $companyId,
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

        return redirect()->route('customer.index')->with('success', 'Customer berhasil diperbarui!');
    }

    public function destroy(Customer $customer)
    {
        $this->cekAkses();
        $customer->delete();
        return redirect()->route('customer.index')->with('success', 'Customer berhasil dihapus!');
    }
}