<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IzinController extends Controller
{
    // List izin milik user
    public function index(Request $request)
    {
        $izin = PengajuanIzin::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(function($i) {
                $attachments = DB::table('izin_attachments')
                    ->where('pengajuan_izin_id', $i->id)
                    ->get()
                    ->map(fn($a) => [
                        'id'        => $a->id,
                        'file_name' => $a->file_name,
                        'file_type' => $a->file_type,
                        'url'       => Storage::url($a->file_path),
                    ]);

                return [
                    'id'              => $i->id,
                    'jenis'           => $i->jenis,
                    'tanggal_mulai'   => $i->tanggal_mulai->format('d M Y'),
                    'tanggal_selesai' => $i->tanggal_selesai->format('d M Y'),
                    'alasan'          => $i->alasan,
                    'status'          => $i->status,
                    'catatan_review'  => $i->catatan_review,
                    'attachments'     => $attachments,
                ];
            });

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
            'attachments.*'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $izin = PengajuanIzin::create([
            'user_id'         => $request->user()->id,
            'jenis'           => $request->jenis,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan'          => $request->alasan,
            'status'          => 'pending',
        ]);

        // Simpan attachments jika ada
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('izin_attachments', 'public');
                $fileType = str_contains($file->getMimeType(), 'pdf') ? 'pdf' : 'image';

                DB::table('izin_attachments')->insert([
                    'pengajuan_izin_id' => $izin->id,
                    'file_path'         => $path,
                    'file_name'         => $file->getClientOriginalName(),
                    'file_type'         => $fileType,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan izin berhasil dikirim!',
        ]);
    }
}