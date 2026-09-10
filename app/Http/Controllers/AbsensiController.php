<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\JamKerja;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    // Halaman utama absensi (desktop)
    public function index()
    {
        $user  = auth()->user();
        $today = Carbon::today();

        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        $riwayat = Absensi::where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->take(7)
            ->get();

        return view('absensi.index', compact('absensiHariIni', 'riwayat'));
    }

    // Halaman absensi mobile (GPS + foto)
    public function mobile()
    {
        $user  = auth()->user();
        $today = Carbon::today();

        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        $lokasiKantor = \DB::table('pengaturan_lokasi')
            ->where('is_active', 1)
            ->first();

        return view('absensi.mobile', compact('absensiHariIni', 'lokasiKantor'));
    }

    // Check-in (desktop)
    public function checkIn()
    {
        $user  = auth()->user();
        $today = Carbon::today();

        $sudahAbsen = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)->first();

        if ($sudahAbsen) {
            return redirect()->route('absensi.index')
                ->with('error', 'Kamu sudah check-in hari ini!');
        }

        $jamSekarang = Carbon::now();
        $jamKerja    = JamKerja::untukTanggal($today);
        $status      = $this->tentukanStatusMasuk($jamSekarang, $jamKerja);

        Absensi::create([
            'user_id'   => $user->id,
            'tanggal'   => $today,
            'jam_masuk' => $jamSekarang->format('H:i:s'),
            'status'    => $status,
        ]);

        return redirect()->route('absensi.index')
            ->with('success', 'Check-in berhasil! Jam: ' . $jamSekarang->format('H:i'));
    }

    // Check-out (desktop)
    public function checkOut()
    {
        $user  = auth()->user();
        $today = Carbon::today();

        $absensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)->first();

        if (!$absensi) {
            return redirect()->route('absensi.index')
                ->with('error', 'Kamu belum check-in hari ini!');
        }

        if ($absensi->jam_keluar) {
            return redirect()->route('absensi.index')
                ->with('error', 'Kamu sudah check-out hari ini!');
        }

        $jamSekarang = Carbon::now();
        $jamKerja    = JamKerja::untukTanggal($today);
        $lemburMenit = $this->hitungLemburMenit($jamSekarang, $jamKerja);

        $absensi->update([
            'jam_keluar'   => $jamSekarang->format('H:i:s'),
            'lembur_menit' => $lemburMenit,
        ]);

        $pesanLembur = $lemburMenit > 0 ? " (lembur {$lemburMenit} menit)" : '';

        return redirect()->route('absensi.index')
            ->with('success', 'Check-out berhasil! Jam: ' . $jamSekarang->format('H:i') . $pesanLembur);
    }

    // Check-in mobile (GPS + foto)
    public function checkInMobile(Request $request)
    {
        $user  = auth()->user();
        $today = Carbon::today();

        $sudahAbsen = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)->first();

        if ($sudahAbsen) {
            return redirect()->route('absensi.mobile')
                ->with('error', 'Kamu sudah check-in hari ini!');
        }

        // Simpan foto selfie dari base64
        $fotoPath = null;
        if ($request->filled('foto')) {
            $fotoPath = $this->simpanFotoBase64($request->foto, 'absensi/masuk');
        }

        // Tentukan status
        $jamSekarang = Carbon::now();
        $jamKerja    = JamKerja::untukTanggal($today);
        $status      = $this->tentukanStatusMasuk($jamSekarang, $jamKerja);

        // Keterangan jika di luar area
        $keterangan = null;
        if ($request->lokasi_valid == '0') {
            $keterangan = 'Absen di luar area kantor (WFH/Dinas Luar)';
        }

        Absensi::create([
            'user_id'      => $user->id,
            'tanggal'      => $today,
            'jam_masuk'    => $jamSekarang->format('H:i:s'),
            'status'       => $status,
            'foto_masuk'   => $fotoPath,
            'lat_masuk'    => $request->lat,
            'lng_masuk'    => $request->lng,
            'lokasi_valid' => $request->lokasi_valid,
            'keterangan'   => $keterangan,
        ]);

        return redirect()->route('absensi.mobile')
            ->with('success', 'Check-in berhasil! Jam: ' . $jamSekarang->format('H:i') .
                ($keterangan ? ' (Di luar area kantor)' : ''));
    }

    // Check-out mobile (GPS + foto)
    public function checkOutMobile(Request $request)
    {
        $user  = auth()->user();
        $today = Carbon::today();

        $absensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)->first();

        if (!$absensi) {
            return redirect()->route('absensi.mobile')
                ->with('error', 'Kamu belum check-in hari ini!');
        }

        if ($absensi->jam_keluar) {
            return redirect()->route('absensi.mobile')
                ->with('error', 'Kamu sudah check-out hari ini!');
        }

        // Simpan foto selfie keluar
        $fotoPath = null;
        if ($request->filled('foto')) {
            $fotoPath = $this->simpanFotoBase64($request->foto, 'absensi/keluar');
        }

        $jamSekarang = Carbon::now();
        $jamKerja    = JamKerja::untukTanggal($today);
        $lemburMenit = $this->hitungLemburMenit($jamSekarang, $jamKerja);

        $absensi->update([
            'jam_keluar'   => $jamSekarang->format('H:i:s'),
            'foto_keluar'  => $fotoPath,
            'lat_keluar'   => $request->lat,
            'lng_keluar'   => $request->lng,
            'lembur_menit' => $lemburMenit,
        ]);

        $pesanLembur = $lemburMenit > 0 ? " (lembur {$lemburMenit} menit)" : '';

        return redirect()->route('absensi.mobile')
            ->with('success', 'Check-out berhasil! Jam: ' . $jamSekarang->format('H:i') . $pesanLembur);
    }

    // Helper: simpan foto base64 ke storage
    private function simpanFotoBase64($base64, $folder)
    {
        // Hapus prefix data:image/jpeg;base64,
        $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $image = base64_decode($image);

        $filename = $folder . '/' . auth()->id() . '_' . time() . '.jpg';
        Storage::disk('public')->put($filename, $image);

        return $filename;
    }

    /**
     * Tentukan status hadir/terlambat berdasarkan setting Jam Kerja hari ini
     * (Pengaturan > Jam Kerja), bukan angka hardcode 08:15 lagi. Toleransi
     * (menit) dari setting ikut dipakai.
     *
     * Kalau hari ini gak ada setting jam kerja sama sekali (belum diisi
     * admin), pakai fallback aman 08:15 supaya absensi tetap jalan normal.
     */
    private function tentukanStatusMasuk(Carbon $jamSekarang, ?JamKerja $jamKerja): string
    {
        if (!$jamKerja || !$jamKerja->jam_masuk) {
            $batasToleransi = Carbon::today()->setTimeFromTimeString('08:15:00');
            return $jamSekarang->lte($batasToleransi) ? 'hadir' : 'terlambat';
        }

        // Hari libur di jadwal (mis. Minggu) tapi tetap absen (dinas luar dsb)
        // -> jangan dihukum "terlambat", gak ada jam masuk resmi buat dibandingkan.
        if ($jamKerja->is_libur) {
            return 'hadir';
        }

        $toleransi = (int) ($jamKerja->toleransi_menit ?? 0);
        $batasToleransi = Carbon::today()
            ->setTimeFromTimeString($jamKerja->jam_masuk)
            ->addMinutes($toleransi);

        return $jamSekarang->lte($batasToleransi) ? 'hadir' : 'terlambat';
    }

    /**
     * Hitung lembur (dalam menit) kalau check-out melewati jam pulang resmi
     * di setting Jam Kerja hari ini. Kalau belum lewat jam pulang (atau gak
     * ada setting), hasilnya 0.
     */
    private function hitungLemburMenit(Carbon $jamSekarang, ?JamKerja $jamKerja): int
    {
        if (!$jamKerja || !$jamKerja->jam_keluar || $jamKerja->is_libur) {
            return 0;
        }

        $jamPulangResmi = Carbon::today()->setTimeFromTimeString($jamKerja->jam_keluar);

        if ($jamSekarang->lte($jamPulangResmi)) {
            return 0;
        }

        return $jamPulangResmi->diffInMinutes($jamSekarang);
    }
}
