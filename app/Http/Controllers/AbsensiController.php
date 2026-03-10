<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    // Halaman utama absensi
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Cek apakah sudah absen hari ini
        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        // Riwayat 7 hari terakhir
        $riwayat = Absensi::where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->take(7)
            ->get();

        return view('absensi.index', compact('absensiHariIni', 'riwayat'));
    }

    // Check-in
    public function checkIn()
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Cek sudah check-in belum
        $sudahAbsen = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($sudahAbsen) {
            return redirect()->route('absensi.index')
                ->with('error', 'Kamu sudah check-in hari ini!');
        }

        // Tentukan status: hadir atau terlambat
        $jamSekarang = Carbon::now();
        $jamMasuk = Carbon::today()->setTimeFromTimeString('08:00:00');
        $toleransi = Carbon::today()->setTimeFromTimeString('08:15:00');
        $status = $jamSekarang->lte($toleransi) ? 'hadir' : 'terlambat';

        Absensi::create([
            'user_id'    => $user->id,
            'tanggal'    => $today,
            'jam_masuk'  => $jamSekarang->format('H:i:s'),
            'status'     => $status,
        ]);

        return redirect()->route('absensi.index')
            ->with('success', 'Check-in berhasil! Jam: ' . $jamSekarang->format('H:i'));
    }

    // Check-out
    public function checkOut()
    {
        $user = auth()->user();
        $today = Carbon::today();

        $absensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$absensi) {
            return redirect()->route('absensi.index')
                ->with('error', 'Kamu belum check-in hari ini!');
        }

        if ($absensi->jam_keluar) {
            return redirect()->route('absensi.index')
                ->with('error', 'Kamu sudah check-out hari ini!');
        }

        $absensi->update([
            'jam_keluar' => Carbon::now()->format('H:i:s'),
        ]);

        return redirect()->route('absensi.index')
            ->with('success', 'Check-out berhasil! Jam: ' . Carbon::now()->format('H:i'));
    }
}