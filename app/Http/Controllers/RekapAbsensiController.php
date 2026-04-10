<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\AbsensiExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

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

        // Siapkan data JSON untuk JavaScript (hindari arrow function di Blade)
        $absensiJson = $absensi->keyBy('id')->map(function($a) {
            return [
                'id'           => $a->id,
                'tanggal'      => $a->tanggal->format('d M Y'),
                'hari'         => $a->tanggal->translatedFormat('l'),
                'tipe'         => $a->tipe ?? 'masuk_kantor',
                'nama_tujuan'  => $a->nama_tujuan,
                'catatan'      => $a->catatan,
                'jam_masuk'    => $a->jam_masuk,
                'jam_keluar'   => $a->jam_keluar,
                'status'       => $a->status,
                'lokasi_valid' => $a->lokasi_valid,
                'lat_masuk'    => $a->lat_masuk,
                'lng_masuk'    => $a->lng_masuk,
                'lat_keluar'   => $a->lat_keluar,
                'lng_keluar'   => $a->lng_keluar,
                'foto_masuk'   => $a->foto_masuk ? Storage::url($a->foto_masuk) : null,
                'foto_keluar'  => $a->foto_keluar ? Storage::url($a->foto_keluar) : null,
                'keterangan'   => $a->keterangan,
            ];
        });

        return view('rekap.detail', compact('user', 'absensi', 'bulan', 'tahun', 'absensiJson'));
    }

    private function hitungHariKerja($bulan, $tahun)
    {
        $awal  = Carbon::createFromDate($tahun, $bulan, 1);
        $akhir = $awal->copy()->endOfMonth();
        $hari  = 0;
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
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $mulai    = $request->tanggal_mulai;
        $selesai  = $request->tanggal_selesai;
        $filename = 'rekap-absensi_' . $mulai . '_' . $selesai . '.xlsx';

        return Excel::download(new AbsensiExport($mulai, $selesai), $filename);
    }
}