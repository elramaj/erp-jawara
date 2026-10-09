<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Support\CetakTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    private function cekAkses()
    {
        if (auth()->user()->role_id != 11) {
            abort(403, 'Akses ditolak.');
        }
    }

    /** Admin biasa hanya boleh mengubah PT-nya sendiri; Super Admin boleh semua PT. */
    private function cekAksesCompany(Company $company): void
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && (int) $user->company_id !== (int) $company->id) {
            abort(403, 'Anda hanya boleh mengubah data PT Anda sendiri.');
        }
    }

    private function aturanCetak(): array
    {
        return [
            'logo'           => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'template_cetak' => ['nullable', Rule::in(array_keys(CetakTemplate::daftar()))],
        ];
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
        return view('pengaturan.company.create', ['templates' => CetakTemplate::daftar()]);
    }

    public function store(Request $request)
    {
        $this->cekAkses();
        $request->validate([
            'nama'         => 'required|string|max:150',
            'kode'         => 'required|unique:companies,kode|max:20',
            'telepon'      => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:100',
            'alamat'       => 'nullable|string',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'radius_meter' => 'nullable|integer|min:10|max:5000',
        ] + $this->aturanCetak());

        $data = $request->only([
            'nama', 'kode', 'alamat', 'telepon', 'email',
            'latitude', 'longitude', 'radius_meter', 'template_cetak',
        ]) + ['is_active' => 1];
        // Kosong = pakai template "default" (desain PT pertama)
        $data['template_cetak'] = $request->filled('template_cetak') ? $request->template_cetak : null;
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logo-pt', 'public');
        }

        Company::create($data);

        return redirect()->route('pengaturan.index')->with('success', 'PT berhasil ditambahkan!');
    }

    public function edit(Company $company)
    {
        $this->cekAkses();
        $this->cekAksesCompany($company);
        $users = User::where('is_active', 1)
            ->where('company_id', $company->id)
            ->orderBy('name')->get();
        $templates = CetakTemplate::daftar();
        return view('pengaturan.company.edit', compact('company', 'users', 'templates'));
    }

    public function update(Request $request, Company $company)
    {
        $this->cekAkses();
        $this->cekAksesCompany($company);
        $request->validate([
            'nama'         => 'required|string|max:150',
            'kode'         => 'required|unique:companies,kode,' . $company->id,
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'radius_meter' => 'nullable|integer|min:10|max:5000',
        ] + $this->aturanCetak());

        $data = $request->only([
            'nama', 'kode', 'alamat', 'telepon', 'email',
            'latitude', 'longitude', 'radius_meter',
        ]) + [
            'is_active'      => $request->has('is_active') ? 1 : 0,
            'template_cetak' => $request->filled('template_cetak') ? $request->template_cetak : null,
        ];

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $request->file('logo')->store('logo-pt', 'public');
        } elseif ($request->boolean('hapus_logo') && $company->logo) {
            Storage::disk('public')->delete($company->logo);
            $data['logo'] = null;
        }

        $company->update($data);

        return redirect()->route('pengaturan.index')->with('success', 'PT berhasil diperbarui!');
    }

    public function destroy(Company $company)
    {
        $this->cekAkses();
        if ($company->users()->count() > 0) {
            return redirect()->route('pengaturan.index')
                ->with('error', 'Tidak bisa hapus PT yang masih punya karyawan!');
        }
        $company->delete();
        return redirect()->route('pengaturan.index')->with('success', 'PT berhasil dihapus!');
    }
}