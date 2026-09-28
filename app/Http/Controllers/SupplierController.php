<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    private function cekAkses()
    {
        if (!in_array(auth()->user()->role_id, [1, 2, 3, 11, 14])) {
            abort(403, 'Akses ditolak.');
        }
    }

    /**
     * Sama seperti CustomerController -- Admin biasa dipaksa ke company
     * miliknya sendiri, Super Admin wajib pilih company dari dropdown.
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
        $suppliers = Supplier::with('company')->orderBy('nama')->get();
        return view('keuangan.supplier.index', compact('suppliers'));
    }

    public function create()
    {
        $this->cekAkses();
        $companies = auth()->user()->isSuperAdmin()
            ? Company::where('is_active', 1)->orderBy('nama')->get()
            : collect();
        $kode = $this->generateKode(auth()->user()->company_id);
        return view('keuangan.supplier.create', compact('kode', 'companies'));
    }

    private function generateKode(?int $companyId): string
    {
        $lastKode = Supplier::withoutCompanyScope()
            ->where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->first();
        $noUrut = $lastKode ? (intval(substr($lastKode->kode, 3)) + 1) : 1;
        return 'SUP' . str_pad($noUrut, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $this->cekAkses();
        $companyId = $this->resolveCompanyId($request);

        $request->validate([
            'kode' => [
                'required',
                Rule::unique('suppliers', 'kode')->where(fn ($q) => $q->where('company_id', $companyId)),
            ],
            'nama' => 'required|string|max:150',
        ]);

        Supplier::create([
            'company_id'        => $companyId,
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
        $companies = auth()->user()->isSuperAdmin()
            ? Company::where('is_active', 1)->orderBy('nama')->get()
            : collect();
        return view('keuangan.supplier.edit', compact('supplier', 'companies'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->cekAkses();
        $companyId = $this->resolveCompanyId($request);

        $request->validate([
            'kode' => [
                'required',
                Rule::unique('suppliers', 'kode')
                    ->where(fn ($q) => $q->where('company_id', $companyId))
                    ->ignore($supplier->id),
            ],
            'nama' => 'required|string|max:150',
        ]);

        $supplier->update([
            'company_id'        => $companyId,
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

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diperbarui!');
    }

    public function destroy(Supplier $supplier)
    {
        $this->cekAkses();
        $supplier->delete();
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus!');
    }
}