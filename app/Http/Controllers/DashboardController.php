<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use App\Models\PengajuanIzin;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // Absensi hari ini milik user
        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        // Statistik bulan ini milik user
        $totalHadir = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();

        $totalTerlambat = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', 'terlambat')
            ->count();

        $totalIzin = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->whereIn('status', ['izin', 'sakit'])
            ->count();

        // Pengajuan izin pending milik user
        $izinPending = PengajuanIzin::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Khusus admin: statistik kantor
        $totalKaryawan = 0;
        $hadirHariIni = 0;
        $izinPendingAdmin = 0;

        if ($user->role_id == 11) {
            $totalKaryawanQuery = User::where('is_active', 1);
            $hadirHariIniQuery = Absensi::whereDate('tanggal', $today)
                ->whereIn('status', ['hadir', 'terlambat']);
            $izinPendingAdminQuery = PengajuanIzin::where('status', 'pending');

            // Bukan Super Admin -> batasi cuma ke company sendiri.
            // Super Admin -> biarin, biar liat angka gabungan semua company.
            if (!$user->isSuperAdmin()) {
                $totalKaryawanQuery->where('company_id', $user->company_id);
                $hadirHariIniQuery->whereHas('user', function ($q) use ($user) {
                    $q->where('company_id', $user->company_id);
                });
                $izinPendingAdminQuery->whereHas('user', function ($q) use ($user) {
                    $q->where('company_id', $user->company_id);
                });
            }

            $totalKaryawan = $totalKaryawanQuery->count();
            $hadirHariIni = $hadirHariIniQuery->count();
            $izinPendingAdmin = $izinPendingAdminQuery->count();
        }

        // Data grafik kehadiran 7 hari terakhir milik user
        $grafik = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i);
            $absen = Absensi::where('user_id', $user->id)
                ->whereDate('tanggal', $tgl)
                ->first();
            $grafik[] = [
                'hari'   => $tgl->translatedFormat('D'),
                'status' => $absen ? $absen->status : 'alfa',
            ];
        }

        return view('dashboard', compact(
            'absensiHariIni',
            'totalHadir',
            'totalTerlambat',
            'totalIzin',
            'izinPending',
            'totalKaryawan',
            'hadirHariIni',
            'izinPendingAdmin',
            'grafik'
        ));
    }
}