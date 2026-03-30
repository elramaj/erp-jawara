<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    private function cekAkses()
    {
        if (auth()->user()->role_id != 11) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index()
    {
        $this->cekAkses();
        $companies = Company::withCount('users')->orderBy('nama')->get();
        return view('pengaturan.company.index', compact('companies'));
    }

    public function create()
    {
        $this->cekAkses();
        return view('pengaturan.company.create');
    }

    public function store(Request $request)
    {
        $this->cekAkses();
        $request->validate([
            'nama' => 'required|string|max:150',
            'kode' => 'required|unique:companies,kode|max:20',
            'telepon' => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:100',
            'alamat'  => 'nullable|string',
        ]);

        Company::create($request->only(['nama', 'kode', 'alamat', 'telepon', 'email']) + ['is_active' => 1]);

        return redirect()->route('company.index')->with('success', 'PT berhasil ditambahkan!');
    }

    public function edit(Company $company)
    {
        $this->cekAkses();
        $users = User::where('is_active', 1)->orderBy('name')->get();
        return view('pengaturan.company.edit', compact('company', 'users'));
    }

    public function update(Request $request, Company $company)
    {
        $this->cekAkses();
        $request->validate([
            'nama' => 'required|string|max:150',
            'kode' => 'required|unique:companies,kode,' . $company->id,
        ]);

        $company->update($request->only(['nama', 'kode', 'alamat', 'telepon', 'email']) + [
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('company.index')->with('success', 'PT berhasil diupdate!');
    }

    public function destroy(Company $company)
    {
        $this->cekAkses();
        if ($company->users()->count() > 0) {
            return redirect()->route('company.index')
                ->with('error', 'Tidak bisa hapus PT yang masih punya karyawan!');
        }
        $company->delete();
        return redirect()->route('company.index')->with('success', 'PT berhasil dihapus!');
    }
}