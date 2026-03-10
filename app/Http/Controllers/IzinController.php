<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzin;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    // Daftar pengajuan izin milik user yang login
    public function index()
    {
        $pengajuan = PengajuanIzin::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('izin.index', compact('pengajuan'));
    }

    // Form pengajuan baru
    public function create()
    {
        return view('izin.create');
    }

    // Simpan pengajuan
    public function store(Request $request)
    {
        $request->validate([
            'jenis'            => 'required|in:izin,sakit,cuti,dinas_luar',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'           => 'required|string|max:500',
        ]);

        PengajuanIzin::create([
            'user_id'          => auth()->id(),
            'jenis'            => $request->jenis,
            'tanggal_mulai'    => $request->tanggal_mulai,
            'tanggal_selesai'  => $request->tanggal_selesai,
            'alasan'           => $request->alasan,
            'status'           => 'pending',
        ]);

        return redirect()->route('izin.index')
            ->with('success', 'Pengajuan izin berhasil dikirim, menunggu persetujuan!');
    }

    // Halaman review untuk admin/bos
    public function review()
    {
        $pengajuan = PengajuanIzin::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('izin.review', compact('pengajuan'));
    }

    // Setujui atau tolak pengajuan
    public function updateStatus(Request $request, PengajuanIzin $izin)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_review' => 'nullable|string|max:255',
        ]);

        $izin->update([
            'status'         => $request->status,
            'reviewed_by'    => auth()->id(),
            'reviewed_at'    => now(),
            'catatan_review' => $request->catatan_review,
        ]);

        return redirect()->route('izin.review')
            ->with('success', 'Status pengajuan berhasil diupdate!');
    }
}