<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\ProyekAnggota;
use App\Models\ProyekMilestone;
use App\Models\ProyekDokumen;
use App\Models\User;
use Illuminate\Http\Request;

class ProyekController extends Controller
{
    // Daftar proyek
    public function index()
    {
        $user = auth()->user();

        // Bos & admin lihat semua, lainnya hanya yang ditugaskan
        if (in_array($user->role_id, [1, 10, 11])) {
            $proyek = Proyek::with(['creator', 'anggota'])->orderBy('created_at', 'desc')->get();
        } else {
            $proyek = Proyek::with(['creator', 'anggota'])
                ->whereHas('anggota', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('proyek.index', compact('proyek'));
    }

    // Form tambah proyek
    public function create()
    {
        if (!in_array(auth()->user()->role_id, [1, 10, 11])) {
    abort(403, 'Akses ditolak.');
        }

        $karyawan = User::where('is_active', 1)->orderBy('name')->get();
        return view('proyek.create', compact('karyawan'));
    }

    // Simpan proyek baru
    public function store(Request $request)
    {
        $request->validate([
            'kode_proyek'  => 'required|unique:proyek,kode_proyek',
            'nama_proyek'  => 'required|string|max:255',
            'klien'        => 'required|string|max:150',
            'nilai_kontrak'=> 'nullable|numeric',
            'tanggal_mulai'=> 'nullable|date',
            'deadline'     => 'nullable|date',
            'status'       => 'required|in:draft,aktif,selesai,dibatalkan',
            'deskripsi'    => 'nullable|string',
            'anggota'      => 'nullable|array',
        ]);

        $proyek = Proyek::create([
            'kode_proyek'   => $request->kode_proyek,
            'nama_proyek'   => $request->nama_proyek,
            'klien'         => $request->klien,
            'nilai_kontrak' => $request->nilai_kontrak,
            'tanggal_mulai' => $request->tanggal_mulai,
            'deadline'      => $request->deadline,
            'status'        => $request->status,
            'progress'      => 0,
            'deskripsi'     => $request->deskripsi,
            'created_by'    => auth()->id(),
        ]);

        // Tambah anggota
        if ($request->anggota) {
            foreach ($request->anggota as $userId) {
                ProyekAnggota::create([
                    'proyek_id' => $proyek->id,
                    'user_id'   => $userId,
                    'peran'     => $request->peran[$userId] ?? null,
                ]);
            }
        }

        return redirect()->route('proyek.show', $proyek)
            ->with('success', 'Proyek berhasil dibuat!');
    }

    // Detail proyek
    public function show(Proyek $proyek)
    {
        $user = auth()->user();

        // Cek akses
        if (!in_array($user->role_id, [1, 10, 11])) {
            $isAnggota = ProyekAnggota::where('proyek_id', $proyek->id)
                ->where('user_id', $user->id)->exists();
            if (!$isAnggota) abort(403, 'Akses ditolak.');
        }

        $proyek->load(['creator', 'anggota.user', 'milestone', 'dokumen.uploader']);
        $karyawan = User::where('is_active', 1)->orderBy('name')->get();

        return view('proyek.show', compact('proyek', 'karyawan'));
    }

    // Update progress & status
    public function updateProgress(Request $request, Proyek $proyek)
    {
        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'status'   => 'required|in:draft,aktif,selesai,dibatalkan',
        ]);

        $proyek->update([
            'progress' => $request->progress,
            'status'   => $request->status,
        ]);

        return back()->with('success', 'Progress berhasil diupdate!');
    }

    // Tambah milestone
    public function storeMilestone(Request $request, Proyek $proyek)
    {
        $request->validate([
            'judul'          => 'required|string|max:255',
            'tanggal_target' => 'required|date',
            'deskripsi'      => 'nullable|string',
        ]);

        $urutan = ProyekMilestone::where('proyek_id', $proyek->id)->max('urutan') + 1;

        ProyekMilestone::create([
            'proyek_id'      => $proyek->id,
            'judul'          => $request->judul,
            'deskripsi'      => $request->deskripsi,
            'tanggal_target' => $request->tanggal_target,
            'status'         => 'belum',
            'urutan'         => $urutan,
        ]);

        return back()->with('success', 'Milestone berhasil ditambahkan!');
    }

    // Update status milestone
    public function updateMilestone(Request $request, ProyekMilestone $milestone)
    {
        $request->validate([
            'status' => 'required|in:belum,proses,selesai',
        ]);

        $milestone->update([
            'status'         => $request->status,
            'tanggal_selesai'=> $request->status == 'selesai' ? now() : null,
        ]);

        return back()->with('success', 'Status milestone diupdate!');
    }

    // Upload dokumen
    public function uploadDokumen(Request $request, Proyek $proyek)
    {
        $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'jenis'        => 'nullable|string|max:100',
            'file'         => 'required|file|max:10240', // max 10MB
        ]);

        $path = $request->file('file')->store('dokumen-proyek', 'public');

        ProyekDokumen::create([
            'proyek_id'    => $proyek->id,
            'nama_dokumen' => $request->nama_dokumen,
            'file_path'    => $path,
            'jenis'        => $request->jenis,
            'uploaded_by'  => auth()->id(),
        ]);

        return back()->with('success', 'Dokumen berhasil diupload!');
    }

    // Hapus proyek
    public function destroy(Proyek $proyek)
    {
        $proyek->delete();
        return redirect()->route('proyek.index')
            ->with('success', 'Proyek berhasil dihapus!');
    }
}