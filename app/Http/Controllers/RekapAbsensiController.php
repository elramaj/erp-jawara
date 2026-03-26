<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\AbsensiExport;
use Maatwebsite\Excel\Facades\Excel;

class RekapAbsensiController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role_id != 11) {
            abort(403, 'Akses ditolak.');
        }

        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        $karyawan = User::with(['absensi' => function($q) use ($bulan, $tahun) {
            $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
        }])->get();

        $totalHariKerja = $this->hitungHariKerja($bulan, $tahun);

        return view('rekap.index', compact('karyawan', 'bulan', 'tahun', 'totalHariKerja'));
    }

    public function detail(Request $request, User $user)
    {
        if (auth()->user()->role_id != 11) {
            abort(403, 'Akses ditolak.');
        }

        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        $absensi = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->get();

        return view('rekap.detail', compact('user', 'absensi', 'bulan', 'tahun'));
    }

    private function hitungHariKerja($bulan, $tahun)
    {
        $awal = Carbon::createFromDate($tahun, $bulan, 1);
        $akhir = $awal->copy()->endOfMonth();
        $hari = 0;
        while ($awal->lte($akhir)) {
            if (!$awal->isWeekend()) $hari++;
            $awal->addDay();
        }
        return $hari;
    }

    public function export(Request $request)
    {
        if (auth()->user()->role_id != 11) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $mulai   = $request->tanggal_mulai;
        $selesai = $request->tanggal_selesai;
        $filename = 'rekap-absensi_' . $mulai . '_' . $selesai . '.xlsx';

        return Excel::download(new AbsensiExport($mulai, $selesai), $filename);
    }
}