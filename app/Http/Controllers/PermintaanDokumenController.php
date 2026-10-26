<?php

namespace App\Http\Controllers;

use App\Models\DokumenArsip;
use App\Models\PermintaanDokumen;
use App\Models\PermintaanDokumenFile;
use App\Models\Proyek;
use App\Services\DokumenService as Akses;
use App\Support\BerkasPrivat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermintaanDokumenController extends Controller
{
    private const FILE_RULE = 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,zip|max:10240'; // 10 MB

    private function cekLihat(): void
    {
        abort_unless(Akses::lihatPermintaan(auth()->user()), 403, 'Akses ditolak.');
    }

    private function cekAksesPermintaan(PermintaanDokumen $dok): void
    {
        $this->cekLihat();
        abort_unless(Akses::bisaLihatPermintaan(auth()->user(), $dok), 403, 'Akses ditolak.');
    }

    private function cekSiapkan(): void
    {
        abort_unless(Akses::siapkan(auth()->user()), 403, 'Akses ditolak.');
    }

    public function index(Request $request)
    {
        $this->cekLihat();
        $user = auth()->user();
        $filter = $request->query('tampil', 'proses');

        $q = PermintaanDokumen::with(['creator', 'penyiap', 'proyek'])
            ->orderByRaw('deadline is null')->orderBy('deadline')->orderBy('created_at', 'desc');
        if (!Akses::lihatSemuaPermintaan($user)) {
            $q->where('created_by', $user->id);
        }

        match ($filter) {
            'proses'  => $q->whereIn('status', ['baru', 'diproses']),
            'selesai' => $q->where('status', 'selesai'),
            'batal'   => $q->where('status', 'dibatalkan'),
            default   => null,
        };

        return view('dokumen.index', [
            'daftar'   => $q->get(),
            'filter'   => $filter,
            'bisaBuat' => Akses::bisaMeminta($user),
        ]);
    }

    public function create()
    {
        abort_unless(Akses::bisaMeminta(auth()->user()), 403, 'Akses ditolak.');
        return view('dokumen.create', ['proyek' => $this->daftarProyek()]);
    }

    private function daftarProyek()
    {
        $user = auth()->user();
        $q = Proyek::whereNotIn('status', ['selesai', 'dibatalkan'])->orderBy('nama_proyek');
        if ((int) $user->role_id === 5 && !$user->isSuperAdmin()) {
            $q->whereHas('anggota', fn($a) => $a->where('user_id', $user->id));
        }
        return $q->get(['id', 'kode_proyek', 'nama_proyek']);
    }

    public function store(Request $request)
    {
        abort_unless(Akses::bisaMeminta(auth()->user()), 403, 'Akses ditolak.');

        $data = $request->validate([
            'jenis_dokumen' => 'required|string|max:150',
            'keperluan'     => 'required|string|max:2000',
            'deadline'      => 'nullable|date|after_or_equal:today',
            'proyek_id'     => 'nullable|integer',
        ]);

        if (!empty($data['proyek_id']) && !Proyek::whereKey($data['proyek_id'])->exists()) {
            return back()->withInput()->withErrors(['proyek_id' => 'Proyek tidak ditemukan.']);
        }

        $user = auth()->user();
        $companyId = !empty($data['proyek_id']) ? Proyek::whereKey($data['proyek_id'])->value('company_id') : $user->company_id;
        if (!$companyId) {
            return back()->withInput()->withErrors(['jenis_dokumen' => 'Akun Anda belum terhubung ke PT. Pilih proyek atau hubungi Admin.']);
        }

        $dok = DB::transaction(fn() => PermintaanDokumen::create([
            'company_id'    => $companyId,
            'no_permintaan' => Akses::nomorBaru((int) $companyId),
            'jenis_dokumen' => $data['jenis_dokumen'],
            'keperluan'     => $data['keperluan'],
            'deadline'      => $data['deadline'] ?? null,
            'proyek_id'     => $data['proyek_id'] ?? null,
            'status'        => 'baru',
            'created_by'    => $user->id,
        ]));

        return redirect()->route('dokumen.show', $dok)->with('success', 'Permintaan dokumen dikirim.');
    }

    public function show(PermintaanDokumen $dokumen)
    {
        $this->cekAksesPermintaan($dokumen);
        $user = auth()->user();

        // Peminta membuka hasil -> notifikasi "dokumen siap" hilang
        if ($dokumen->status === 'selesai' && !$dokumen->dilihat_peminta_at && (int) $dokumen->created_by === (int) $user->id) {
            $dokumen->update(['dilihat_peminta_at' => now()]);
        }

        $dokumen->load(['creator', 'penyiap', 'proyek', 'file', 'arsip']);

        $siapkan = Akses::siapkan($user);
        return view('dokumen.show', [
            'd'         => $dokumen,
            'siapkan'   => $siapkan,
            'bisaBatal' => Akses::bisaBatalkan($user, $dokumen),
            'lihatArsip' => Akses::lihatArsip($user),
            // pilihan arsip untuk memenuhi permintaan tanpa unggah ulang
            'daftarArsip' => ($siapkan && in_array($dokumen->status, ['baru', 'diproses'], true))
                ? DokumenArsip::orderBy('jenis')->orderBy('nama')->get(['id', 'jenis', 'nama', 'nomor', 'tanggal_kedaluwarsa'])
                : collect(),
        ]);
    }

    public function ambil(PermintaanDokumen $dokumen)
    {
        $this->cekSiapkan();
        if ($dokumen->status !== 'baru') {
            return back()->with('error', 'Permintaan ini sudah diambil.');
        }
        $dokumen->update(['status' => 'diproses', 'penyiap_id' => auth()->id(), 'diambil_at' => now()]);
        return back()->with('success', 'Permintaan diambil. Silakan siapkan dokumennya.');
    }

    public function selesaikan(Request $request, PermintaanDokumen $dokumen)
    {
        $this->cekSiapkan();
        if (!in_array($dokumen->status, ['baru', 'diproses'], true)) {
            return back()->with('error', 'Permintaan ini sudah selesai atau dibatalkan.');
        }

        $request->validate([
            'file'            => 'nullable|array|max:10',
            'file.*'          => self::FILE_RULE,
            'arsip_id'        => 'nullable|integer',
            'catatan_penyiap' => 'nullable|string|max:2000',
        ]);

        $arsipId = $request->filled('arsip_id') ? (int) $request->arsip_id : null;
        if ($arsipId && !DokumenArsip::whereKey($arsipId)->exists()) {
            return back()->withErrors(['arsip_id' => 'Dokumen arsip tidak ditemukan.'])->withInput();
        }
        if (!$arsipId && empty($request->file('file'))) {
            return back()->withErrors(['file' => 'Unggah minimal satu file, atau pilih dokumen dari arsip legalitas.'])->withInput();
        }

        DB::transaction(function () use ($request, $dokumen, $arsipId) {
            foreach ((array) $request->file('file', []) as $f) {
                if (!$f) continue;
                $info = BerkasPrivat::simpan($f, 'permintaan-dokumen/' . $dokumen->company_id . '/' . $dokumen->id);
                PermintaanDokumenFile::create($info + ['permintaan_id' => $dokumen->id, 'uploaded_by' => auth()->id()]);
            }
            $dokumen->update([
                'status'          => 'selesai',
                'arsip_id'        => $arsipId,
                'catatan_penyiap' => $request->input('catatan_penyiap'),
                'penyiap_id'      => $dokumen->penyiap_id ?: auth()->id(),
                'selesai_at'      => now(),
            ]);
        });

        return back()->with('success', 'Dokumen disiapkan. Peminta akan mendapat notifikasi.');
    }

    public function batalkan(Request $request, PermintaanDokumen $dokumen)
    {
        $this->cekAksesPermintaan($dokumen);
        abort_unless(Akses::bisaBatalkan(auth()->user(), $dokumen), 403, 'Akses ditolak.');
        if (!in_array($dokumen->status, ['baru', 'diproses'], true)) {
            return back()->with('error', 'Permintaan ini sudah selesai atau dibatalkan.');
        }
        $data = $request->validate(['alasan_batal' => 'required|string|max:500']);
        $dokumen->update(['status' => 'dibatalkan', 'alasan_batal' => $data['alasan_batal']]);
        return back()->with('success', 'Permintaan dibatalkan.');
    }

    public function file(PermintaanDokumen $dokumen, PermintaanDokumenFile $file)
    {
        $this->cekAksesPermintaan($dokumen);
        abort_unless((int) $file->permintaan_id === (int) $dokumen->id, 404);

        return BerkasPrivat::kirim($file->path, $file->nama_asli, $file->mime, request()->boolean('unduh'));
    }
}
