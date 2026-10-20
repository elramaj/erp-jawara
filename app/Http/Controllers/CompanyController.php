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

    /** Tambah/hapus PT hanya untuk Super Admin (Admin biasa cuma mengubah PT-nya sendiri). */
    private function cekSuperAdmin(): void
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang boleh menambah atau menghapus PT.');
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

    private const FIELD_DOKUMEN = ['npwp', 'bank_nama', 'bank_rekening', 'bank_atas_nama', 'penandatangan_nama', 'penandatangan_jabatan', 'footer_dokumen'];

    private function aturanCetak(): array
    {
        return [
            'npwp'                  => 'nullable|string|max:30',
            'bank_nama'             => 'nullable|string|max:100',
            'bank_rekening'         => 'nullable|string|max:50',
            'bank_atas_nama'        => 'nullable|string|max:150',
            'penandatangan_nama'    => 'nullable|string|max:150',
            'penandatangan_jabatan' => 'nullable|string|max:100',
            'footer_dokumen'        => 'nullable|string|max:1000',
            'logo'           => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'template_cetak' => ['nullable', Rule::in(array_keys(CetakTemplate::daftar()))],
        ];
    }

    public function index()
    {
        $this->cekAkses();
        $user = auth()->user();
        $companies = Company::withCount('users')
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('id', $user->company_id))
            ->orderBy('nama')->get();
        return view('pengaturan.company.index', compact('companies'));
    }

    public function create()
    {
        $this->cekAkses();
        $this->cekSuperAdmin();
        return view('pengaturan.company.create', ['templates' => CetakTemplate::daftar()]);
    }

    public function store(Request $request)
    {
        $this->cekAkses();
        $this->cekSuperAdmin();
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
        ] ) + $request->only(self::FIELD_DOKUMEN) + ['is_active' => 1];
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
        $super = auth()->user()->isSuperAdmin();
        $request->validate([
            'nama'         => 'required|string|max:150',
            'kode'         => ($super ? 'required' : 'nullable') . '|unique:companies,kode,' . $company->id,
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'radius_meter' => 'nullable|integer|min:10|max:5000',
        ] + $this->aturanCetak());

        $data = $request->only([
            'nama', 'alamat', 'telepon', 'email',
            'latitude', 'longitude', 'radius_meter',
        ]) + $request->only(self::FIELD_DOKUMEN) + [
            'template_cetak' => $request->filled('template_cetak') ? $request->template_cetak : null,
        ];
        // Kode PT & status aktif hanya boleh diubah Super Admin (Admin PT tidak bisa menonaktifkan PT-nya sendiri).
        if ($super) {
            $data['kode']      = $request->kode;
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
        }

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
        $this->cekSuperAdmin();
        if ($company->users()->count() > 0) {
            return redirect()->route('pengaturan.index')
                ->with('error', 'Tidak bisa hapus PT yang masih punya karyawan!');
        }
        $company->delete();
        return redirect()->route('pengaturan.index')->with('success', 'PT berhasil dihapus!');
    }
}