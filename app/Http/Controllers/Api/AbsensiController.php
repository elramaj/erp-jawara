<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function status(Request $request)
    {
        $user  = $request->user();
        $today = Carbon::today();

        $absensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)->first();

        $lokasiKantor = null;
        if ($user->company_id) {
            $company = \App\Models\Company::find($user->company_id);
            if ($company && $company->latitude) {
                $lokasiKantor = [
                    'latitude'     => $company->latitude,
                    'longitude'    => $company->longitude,
                    'radius_meter' => $company->radius_meter ?? 100,
                ];
            }
        }

        if (!$lokasiKantor) {
            $lok = \DB::table('pengaturan_lokasi')->where('is_active', 1)->first();
            if ($lok) {
                $lokasiKantor = [
                    'latitude'     => $lok->latitude,
                    'longitude'    => $lok->longitude,
                    'radius_meter' => $lok->radius_meter,
                ];
            }
        }

        return response()->json([
            'success'       => true,
            'absensi'       => $absensi ? [
                'id'           => $absensi->id,
                'tanggal'      => $absensi->tanggal,
                'jam_masuk'    => $absensi->jam_masuk,
                'jam_keluar'   => $absensi->jam_keluar,
                'status'       => $absensi->status,
                'lokasi_valid' => $absensi->lokasi_valid,
                'foto_masuk'   => $absensi->foto_masuk ? Storage::url($absensi->foto_masuk) : null,
                'foto_keluar'  => $absensi->foto_keluar ? Storage::url($absensi->foto_keluar) : null,
            ] : null,
            'lokasi_kantor' => $lokasiKantor,
        ]);
    }

    public function checkIn(Request $request)
    {
        $user  = $request->user();
        $today = Carbon::today();

        if (Absensi::where('user_id', $user->id)->whereDate('tanggal', $today)->exists()) {
            return response()->json(['success' => false, 'message' => 'Sudah check-in hari ini!'], 400);
        }

        // Simpan foto
        $fotoPath = null;
        if ($request->filled('foto')) {
            $image    = preg_replace('/^data:image\/\w+;base64,/', '', $request->foto);
            $image    = base64_decode($image);
            $fotoPath = 'absensi/masuk/' . $user->id . '_' . time() . '.jpg';
            Storage::disk('public')->put($fotoPath, $image);
        }

        $jamSekarang = Carbon::now();
        $toleransi   = Carbon::today()->setTimeFromTimeString('08:15:00');
        $status      = $jamSekarang->lte($toleransi) ? 'hadir' : 'terlambat';
        $keterangan  = $request->lokasi_valid == 0 ? 'Absen di luar area kantor (WFH/Dinas Luar)' : null;

        $absensi = Absensi::create([
            'user_id'      => $user->id,
            'tanggal'      => $today,
            'jam_masuk'    => $jamSekarang->format('H:i:s'),
            'status'       => $status,
            'foto_masuk'   => $fotoPath,
            'lat_masuk'    => $request->lat,
            'lng_masuk'    => $request->lng,
            'lokasi_valid' => $request->lokasi_valid ?? 0,
            'keterangan'   => $keterangan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil! Jam: ' . $jamSekarang->format('H:i'),
            'status'  => $status,
        ]);
    }

    public function checkOut(Request $request)
    {
        $user    = $request->user();
        $today   = Carbon::today();
        $absensi = Absensi::where('user_id', $user->id)->whereDate('tanggal', $today)->first();

        if (!$absensi) {
            return response()->json(['success' => false, 'message' => 'Belum check-in hari ini!'], 400);
        }

        if ($absensi->jam_keluar) {
            return response()->json(['success' => false, 'message' => 'Sudah check-out hari ini!'], 400);
        }

        $fotoPath = null;
        if ($request->filled('foto')) {
            $image    = preg_replace('/^data:image\/\w+;base64,/', '', $request->foto);
            $image    = base64_decode($image);
            $fotoPath = 'absensi/keluar/' . $user->id . '_' . time() . '.jpg';
            Storage::disk('public')->put($fotoPath, $image);
        }

        $jamSekarang = Carbon::now();
        $absensi->update([
            'jam_keluar'  => $jamSekarang->format('H:i:s'),
            'foto_keluar' => $fotoPath,
            'lat_keluar'  => $request->lat,
            'lng_keluar'  => $request->lng,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil! Jam: ' . $jamSekarang->format('H:i'),
        ]);
    }

    public function riwayat(Request $request)
    {
        $riwayat = Absensi::where('user_id', $request->user()->id)
            ->orderBy('tanggal', 'desc')
            ->take(30)
            ->get()
            ->map(fn($a) => [
                'tanggal'      => $a->tanggal->format('d M Y'),
                'jam_masuk'    => $a->jam_masuk ?? '-',
                'jam_keluar'   => $a->jam_keluar ?? '-',
                'status'       => $a->status,
                'lokasi_valid' => $a->lokasi_valid,
            ]);

        return response()->json(['success' => true, 'data' => $riwayat]);
    }
}