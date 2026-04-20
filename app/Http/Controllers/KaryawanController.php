<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = User::with(['role', 'department', 'company'])->orderBy('name')->get();
        return view('karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        $roles       = Role::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $companies   = Company::where('is_active', 1)->orderBy('nama')->get();
        return view('karyawan.create', compact('roles', 'departments', 'companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:150',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'role_id'       => 'required|exists:roles,id',
            'company_id'    => 'required|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'phone'         => 'nullable|string|max:20',
            'join_date'     => 'nullable|date',
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
            'role_id'       => $request->role_id,
            'company_id'    => $request->company_id,
            'department_id' => $request->department_id,
            'phone'         => $request->phone,
            'join_date'     => $request->join_date,
            'is_active'     => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('karyawan.index')
            ->with('success', 'Karyawan berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        $roles       = Role::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $companies   = Company::where('is_active', 1)->orderBy('nama')->get();
        return view('karyawan.edit', compact('user', 'roles', 'departments', 'companies'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'          => 'required|string|max:150',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'role_id'       => 'required|exists:roles,id',
            'company_id'    => 'required|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'phone'         => 'nullable|string|max:20',
            'join_date'     => 'nullable|date',
        ]);

        $data = [
            'name'          => $request->name,
            'email'         => $request->email,
            'role_id'       => $request->role_id,
            'company_id'    => $request->company_id,
            'department_id' => $request->department_id,
            'phone'         => $request->phone,
            'join_date'     => $request->join_date,
            'is_active'     => $request->has('is_active') ? 1 : 0,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('karyawan.index')
            ->with('success', 'Data karyawan berhasil diupdate!');
    }

public function destroy(User $user)
{
    if ($user->id === auth()->id()) {
        return redirect()->route('karyawan.index')
            ->with('error', 'Tidak bisa menghapus akun sendiri!');
    }

    $adminId = auth()->id();

    \App\Models\Absensi::where('user_id', $user->id)->delete();
    \App\Models\PengajuanIzin::where('user_id', $user->id)->delete();
    \App\Models\ProyekAnggota::where('user_id', $user->id)->delete();
    \App\Models\GudangStokMasuk::where('created_by', $user->id)->update(['created_by' => $adminId]);
    \App\Models\GudangStokKeluar::where('created_by', $user->id)->update(['created_by' => $adminId]);
    \App\Models\Komplain::where('created_by', $user->id)->update(['created_by' => $adminId]);
    DB::table('komplain_timeline')->where('created_by', $user->id)->update(['created_by' => $adminId]);
    DB::table('proyek')->where('created_by', $user->id)->update(['created_by' => $adminId]);

    $user->delete();

    return redirect()->route('karyawan.index')
        ->with('success', 'Karyawan berhasil dihapus!');
}
}