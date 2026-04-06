<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

class PengaturanController extends Controller
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
        $departments  = Department::orderBy('name')->get();
        $jamKerja     = DB::table('jam_kerja')->orderBy('id')->get();
        $companies    = Company::withCount('users')->orderBy('nama')->get();
        $lokasiKantor = DB::table('pengaturan_lokasi')->where('is_active', 1)->first();
        return view('pengaturan.index', compact('departments', 'jamKerja', 'companies', 'lokasiKantor'));
    }

    // Department
    public function storeDepartment(Request $request)
    {
        $this->cekAkses();
        $request->validate(['name' => 'required|string|max:100|unique:departments,name']);
        Department::create(['name' => $request->name, 'description' => $request->description]);
        return back()->with('success', 'Departemen berhasil ditambahkan!');
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $this->cekAkses();
        $request->validate(['name' => 'required|string|max:100|unique:departments,name,' . $department->id]);
        $department->update(['name' => $request->name, 'description' => $request->description]);
        return back()->with('success', 'Departemen berhasil diupdate!');
    }

    public function destroyDepartment(Department $department)
    {
        $this->cekAkses();
        $department->delete();
        return back()->with('success', 'Departemen berhasil dihapus!');
    }

    // Jam Kerja
    public function updateJamKerja(Request $request)
    {
        $this->cekAkses();
        $request->validate([
            'jam_kerja_id'    => 'required|array',
            'jam_masuk'       => 'required|array',
            'jam_keluar'      => 'required|array',
            'toleransi_menit' => 'required|array',
            'is_libur'        => 'nullable|array',
        ]);

        foreach ($request->jam_kerja_id as $i => $id) {
            DB::table('jam_kerja')->where('id', $id)->update([
                'jam_masuk'       => $request->jam_masuk[$i],
                'jam_keluar'      => $request->jam_keluar[$i],
                'toleransi_menit' => $request->toleransi_menit[$i],
                'is_libur'        => in_array($id, $request->is_libur ?? []) ? 1 : 0,
            ]);
        }

        return back()->with('success', 'Jam kerja berhasil diupdate!');
    }

    // Lokasi Kantor
    public function updateLokasi(Request $request)
    {
        $this->cekAkses();
        $request->validate([
            'nama'         => 'required|string|max:100',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:10|max:5000',
        ]);

        $lokasi = DB::table('pengaturan_lokasi')->where('is_active', 1)->first();

        if ($lokasi) {
            DB::table('pengaturan_lokasi')->where('id', $lokasi->id)->update([
                'nama'         => $request->nama,
                'latitude'     => $request->latitude,
                'longitude'    => $request->longitude,
                'radius_meter' => $request->radius_meter,
                'updated_at'   => now(),
            ]);
        } else {
            DB::table('pengaturan_lokasi')->insert([
                'nama'         => $request->nama,
                'latitude'     => $request->latitude,
                'longitude'    => $request->longitude,
                'radius_meter' => $request->radius_meter,
                'is_active'    => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        return back()->with('success', 'Lokasi kantor berhasil diupdate!');
    }
}