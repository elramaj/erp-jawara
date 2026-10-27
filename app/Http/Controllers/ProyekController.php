<?php

namespace App\Http\Controllers;

use App\Support\HakAkses;
use App\Models\Proyek;
use App\Models\ProyekAnggota;
use App\Models\ProyekMilestone;
use App\Models\ProyekDokumen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProyekController extends Controller
{
    /** Yang boleh membuat/mengatur/menghapus proyek (sama dengan tombol yang tampil di halaman). */
    private function cekKelola(): void
    {
        $user = auth()->user();
        if (!HakAkses::bisa('proyek.kelola', $user)) {
            abort(403, 'Akses ditolak.');
        }
    }

    /** Boleh melihat/ikut mengerjakan proyek ini: pengelola, atau anggota proyek. */
    private function cekAksesProyek(Proyek $proyek): void
    {
        $user = auth()->user();
        if (HakAkses::bisa('proyek.kelola', $user)) {
            return;
        }
        $anggota = ProyekAnggota::where('proyek_id', $proyek->id)->where('user_id', $user->id)->exists();
        if (!$anggota) {
            abort(403, 'Akses ditolak.');
        }
    }

    // Daftar proyek
public function index()
{
    $user = auth()->user();

    if (HakAkses::bisa('proyek.kelola', $user)) {
        $proyek = Proyek::with(['creator', 'anggota', 'company'])
            ->orderBy('created_at', 'desc')->get();
    } else {
        $proyek = Proyek::with(['creator', 'anggota', 'company'])
            ->whereHas('anggota', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')->get();
    }

    return view('proyek.index', compact('proyek'));
}

    // Form tambah proyek
    public function create()
    {
        if (!HakAkses::bisa('proyek.kelola')) {
            abort(403, 'Akses ditolak.');
        }

        $karyawan = User::where('is_active', 1)->forCurrentCompany()->orderBy('name')->get();
        return view('proyek.create', compact('karyawan'));
    }

    // Simpan proyek baru
    public function store(Request $request)
    {
        $this->cekKelola();
        $companyId = $this->companyIdUntukSimpan();
        $request->validate([
            'kode_proyek'  => ['required', Rule::unique('proyek', 'kode_proyek')->where('company_id', $companyId)],
            'nama_proyek'  => 'required|string|max:255',
            'klien'        => 'required|string|max:150',
            'nilai_kontrak'=> 'nullable|numeric',
            'tanggal_mulai'=> 'nullable|date',
            'deadline'     => 'nullable|date',
            'status'       => 'required|in:draft,bola_liar,aktif,selesai,dibatalkan',
            'deskripsi'    => 'nullable|string',
            'anggota'      => 'nullable|array',
            'anggota.*'    => [Rule::exists('users', 'id')->where('company_id', $companyId)],
        ]);

        $proyek = Proyek::create([
            'company_id'    => $companyId,
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

        // Buat milestone otomatis sesuai alur kerja
        $milestones = [
            ['judul' => 'Paket Baru Masuk',       'urutan' => 1],
            ['judul' => 'Respon & Koordinasi',     'urutan' => 2],
            ['judul' => 'Purchase Order (PO)',      'urutan' => 3],
            ['judul' => 'Monitor Barang PO',        'urutan' => 4],
            ['judul' => 'Barang Diterima Gudang',   'urutan' => 5],
            ['judul' => 'Uji Fungsi',               'urutan' => 6],
            ['judul' => 'Pengiriman ke Customer',   'urutan' => 7],
            ['judul' => 'Dokumen (BAST & Invoice)', 'urutan' => 8],
            ['judul' => 'Pembayaran',               'urutan' => 9],
            ['judul' => 'Selesai',                  'urutan' => 10],
        ];

        foreach ($milestones as $m) {
            ProyekMilestone::create([
                'proyek_id'      => $proyek->id,
                'judul'          => $m['judul'],
                'deskripsi'      => null,
                'tanggal_target' => $request->deadline ?? now()->addDays(30),
                'status'         => 'belum',
                'urutan'         => $m['urutan'],
            ]);
        }

        return redirect()->route('proyek.show', $proyek)
            ->with('success', 'Proyek berhasil dibuat!');
    }

    // Detail proyek
    public function show(Proyek $proyek)
    {
        $user = auth()->user();

        if (!HakAkses::bisa('proyek.kelola', $user)) {
            $isAnggota = ProyekAnggota::where('proyek_id', $proyek->id)
                ->where('user_id', $user->id)->exists();
            if (!$isAnggota) abort(403, 'Akses ditolak.');
        }

        $proyek->load(['creator', 'anggota.user', 'milestone', 'dokumen.uploader']);
        $karyawan = User::where('is_active', 1)->forCurrentCompany()->orderBy('name')->get();

        return view('proyek.show', compact('proyek', 'karyawan'));
    }

    // Update progress & status
    public function updateProgress(Request $request, Proyek $proyek)
    {
        $this->cekKelola();
        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'status'   => 'required|in:draft,bola_liar,aktif,selesai,dibatalkan',
        ]);

        $proyek->update([
            'progress' => $request->progress,
            'status'   => $request->status,
        ]);

        return back()->with('success', 'Progress berhasil diperbarui!');
    }

    // Tambah milestone
    public function storeMilestone(Request $request, Proyek $proyek)
    {
        $this->cekKelola();
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
        // ProyekMilestone tidak punya filter PT sendiri; lewat proyek-nya (yang terfilter PT)
        // milestone milik PT lain jadi 404, dan role/keanggotaan dicek seperti halaman proyek.
        $proyek = Proyek::findOrFail($milestone->proyek_id);
        $this->cekAksesProyek($proyek);
        $request->validate([
            'status' => 'required|in:belum,proses,selesai',
        ]);

        $milestone->update([
            'status'         => $request->status,
            'tanggal_selesai'=> $request->status == 'selesai' ? now() : null,
        ]);

        return back()->with('success', 'Status milestone diperbarui!');
    }

    // Upload dokumen
    public function uploadDokumen(Request $request, Proyek $proyek)
    {
        $this->cekAksesProyek($proyek);
        $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'jenis'        => 'nullable|string|max:100',
            'file'         => 'required|file|max:10240',
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
        $this->cekKelola();
        $proyek->delete();
        return redirect()->route('proyek.index')
            ->with('success', 'Proyek berhasil dihapus!');
    }
}