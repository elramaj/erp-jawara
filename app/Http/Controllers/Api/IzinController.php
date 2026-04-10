<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanIzin;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    // List izin milik user
    public function index(Request $request)
    {
        $izin = PengajuanIzin::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(fn($i) => [
                'id'              => $i->id,
                'jenis'           => $i->jenis,
                'tanggal_mulai'   => $i->tanggal_mulai->format('d M Y'),
                'tanggal_selesai' => $i->tanggal_selesai->format('d M Y'),
                'alasan'          => $i->alasan,
                'status'          => $i->status,
                'catatan_review'  => $i->catatan_review,
            ]);

        return response()->json(['success' => true, 'data' => $izin]);
    }

    // Ajukan izin baru
    public function store(Request $request)
    {
        $request->validate([
            'jenis'           => 'required|in:izin,sakit,cuti,dinas_luar',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string',
        ]);

        PengajuanIzin::create([
            'user_id'         => $request->user()->id,
            'jenis'           => $request->jenis,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan'          => $request->alasan,
            'status'          => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan izin berhasil dikirim!',
        ]);
    }
}