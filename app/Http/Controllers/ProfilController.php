<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Aset;
use App\Models\PengajuanIzin;
use App\Models\Reimburse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $bulan = Carbon::now()->month;
        $tahun = Carbon::now()->year;

        // Ringkasan kehadiran bulan ini
        $hadir     = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
            ->where('status', 'hadir')->count();
        $terlambat = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
            ->where('status', 'terlambat')->count();
        $lemburMenitBulanIni = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
            ->sum('lembur_menit');

        // Izin/cuti yang diambil tahun ini (disetujui)
        $izinTahunIni = PengajuanIzin::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahun)
            ->count();

        // Aset yang lagi dipegang
        $asetDipegang = Aset::where('user_id', $user->id)
            ->with('kategori')
            ->orderBy('nama_aset')
            ->get();

        // Reimburse yang masih pending punya dia
        $reimbursePending = Reimburse::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        return view('profil.index', compact(
            'user', 'hadir', 'terlambat', 'lemburMenitBulanIni',
            'izinTahunIni', 'asetDipegang', 'reimbursePending'
        ));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:150',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'name'  => $request->name,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('photo')) {
            // Hapus foto lama biar storage gak numpuk file yatim
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('profil', 'public');
        }

        $user->update($data);

        return redirect()->route('profil.index')
            ->with('success', 'Profil berhasil diupdate!');
    }

    public function gantiPassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'password_lama'     => 'required',
            'password_baru'     => 'required|min:6|confirmed',
        ], [
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_baru.min'       => 'Password minimal 6 karakter.',
        ]);

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai!']);
        }

        $user->update([
            'password' => bcrypt($request->password_baru),
        ]);

        return redirect()->route('profil.index')
            ->with('success', 'Password berhasil diganti!');
    }
}
