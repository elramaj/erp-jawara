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

        // Absensi utama (masuk kantor/wfh)
        $absensiUtama = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->whereIn('tipe', ['masuk_kantor', 'wfh'])
            ->whereNull('parent_id')
            ->first();

        // Semua visit hari ini
        $visitHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('tipe', 'visit')
            ->orderBy('urutan_visit')
            ->get()
            ->map(fn($a) => [
                'id'           => $a->id,
                'urutan'       => $a->urutan_visit,
                'nama_tujuan'  => $a->nama_tujuan,
                'jam_masuk'    => $a->jam_masuk,
                'jam_keluar'   => $a->jam_keluar,
                'catatan'      => $a->catatan,
                'lokasi_valid' => $a->lokasi_valid,
            ]);

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
            'success'        => true,
            'absensi'        => $absensiUtama ? [
                'id'           => $absensiUtama->id,
                'tanggal'      => $absensiUtama->tanggal,
                'jam_masuk'    => $absensiUtama->jam_masuk,
                'jam_keluar'   => $absensiUtama->jam_keluar,
                'status'       => $absensiUtama->status,
                'tipe'         => $absensiUtama->tipe,
                'catatan'      => $absensiUtama->catatan,
                'nama_tujuan'  => $absensiUtama->nama_tujuan,
                'lokasi_valid' => $absensiUtama->lokasi_valid,
            ] : null,
            'visit_hari_ini' => $visitHariIni,
            'total_visit'    => $visitHariIni->count(),
            'lokasi_kantor'  => $lokasiKantor,
        ]);
    }

    public function checkIn(Request $request)
    {
        $user  = $request->user();
        $today = Carbon::today();
        $tipe  = $request->tipe ?? 'masuk_kantor';

        // Cek duplikat untuk non-visit
        if (in_array($tipe, ['masuk_kantor', 'wfh'])) {
            if (Absensi::where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->whereIn('tipe', ['masuk_kantor', 'wfh'])
                ->whereNull('parent_id')
                ->exists()) {
                return response()->json(['success' => false,
                    'message' => 'Sudah check-in hari ini!'], 400);
            }
        }

        // Hitung urutan visit
        $urutanVisit = 1;
        if ($tipe === 'visit') {
            $urutanVisit = Absensi::where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->where('tipe', 'visit')
                ->count() + 1;
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
        $status      = ($tipe === 'masuk_kantor' && $jamSekarang->gt($toleransi)) ? 'terlambat' : 'hadir';

        Absensi::create([
            'user_id'      => $user->id,
            'tanggal'      => $today,
            'jam_masuk'    => $jamSekarang->format('H:i:s'),
            'status'       => $status,
            'tipe'         => $tipe,
            'catatan'      => $request->catatan,
            'nama_tujuan'  => $request->nama_tujuan,
            'urutan_visit' => $urutanVisit,
            'foto_masuk'   => $fotoPath,
            'lat_masuk'    => $request->lat,
            'lng_masuk'    => $request->lng,
            'lokasi_valid' => $request->lokasi_valid ?? 0,
        ]);

        $pesan = $tipe === 'visit'
            ? "Check-in Visit #{$urutanVisit} berhasil! Jam: " . $jamSekarang->format('H:i')
            : "Check-in berhasil! Jam: " . $jamSekarang->format('H:i');

        return response()->json([
            'success'      => true,
            'message'      => $pesan,
            'status'       => $status,
            'tipe'         => $tipe,
            'urutan_visit' => $urutanVisit,
        ]);
    }

    public function checkOut(Request $request)
    {
        $user    = $request->user();
        $today   = Carbon::today();
        $tipe    = $request->tipe ?? 'masuk_kantor';

        if ($tipe === 'visit') {
            // Checkout visit — berdasarkan ID visit
            $visitId = $request->visit_id;
            $absensi = Absensi::where('id', $visitId)
                ->where('user_id', $user->id)
                ->where('tipe', 'visit')
                ->whereNull('jam_keluar')
                ->first();

            if (!$absensi) {
                return response()->json(['success' => false,
                    'message' => 'Visit tidak ditemukan atau sudah checkout!'], 400);
            }
        } else {
            $absensi = Absensi::where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->whereIn('tipe', ['masuk_kantor', 'wfh'])
                ->whereNull('parent_id')
                ->first();

            if (!$absensi) {
                return response()->json(['success' => false,
                    'message' => 'Belum check-in hari ini!'], 400);
            }

            if ($absensi->jam_keluar) {
                return response()->json(['success' => false,
                    'message' => 'Sudah check-out hari ini!'], 400);
            }
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

        $pesan = $tipe === 'visit'
            ? "Checkout Visit #{$absensi->urutan_visit} berhasil! Jam: " . $jamSekarang->format('H:i')
            : "Check-out berhasil! Jam: " . $jamSekarang->format('H:i');

        return response()->json(['success' => true, 'message' => $pesan]);
    }

    public function riwayat(Request $request)
    {
        $riwayat = Absensi::where('user_id', $request->user()->id)
            ->orderBy('tanggal', 'desc')->orderBy('urutan_visit')
            ->take(30)->get()
            ->map(fn($a) => [
                'tanggal'      => $a->tanggal->format('d M Y'),
                'jam_masuk'    => $a->jam_masuk ?? '-',
                'jam_keluar'   => $a->jam_keluar ?? '-',
                'status'       => $a->status,
                'tipe'         => $a->tipe,
                'nama_tujuan'  => $a->nama_tujuan,
                'urutan_visit' => $a->urutan_visit,
                'lokasi_valid' => $a->lokasi_valid,
            ]);

        return response()->json(['success' => true, 'data' => $riwayat]);
    }
}