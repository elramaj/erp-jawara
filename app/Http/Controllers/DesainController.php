<?php

namespace App\Http\Controllers;

use App\Models\DesainFile;
use App\Models\PermintaanDesain;
use App\Models\Proyek;
use App\Services\DesainService as Akses;
use App\Support\BerkasPrivat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DesainController extends Controller
{
    private const MAKS_KB = 20480; // 20 MB per file

    private function cekLihat(): void
    {
        abort_unless(Akses::lihat(auth()->user()), 403, 'Akses ditolak.');
    }

    private function cekKerjakan(): void
    {
        abort_unless(Akses::kerjakan(auth()->user()), 403, 'Akses ditolak.');
    }

    private function cekAksesPermintaan(PermintaanDesain $desain): void
    {
        $this->cekLihat();
        abort_unless(Akses::bisaLihatPermintaan(auth()->user(), $desain), 403, 'Akses ditolak.');
    }

    private function cekPutuskan(PermintaanDesain $desain): void
    {
        abort_unless(Akses::bisaPutuskan(auth()->user(), $desain), 403, 'Akses ditolak.');
    }

    private function aturanFile(bool $wajib, string $nama = 'file'): array
    {
        return [
            $nama               => [$wajib ? 'required' : 'nullable', 'array', 'max:10'],
            $nama . '.*'        => ['file', 'max:' . self::MAKS_KB],
        ];
    }

    /** @return string|null pesan error bila ada file dengan ekstensi terlarang */
    private function cekEkstensiSemua(Request $request, string $nama): ?string
    {
        foreach ((array) $request->file($nama, []) as $f) {
            if ($f && ($pesan = BerkasPrivat::cekEkstensi($f))) {
                return $pesan;
            }
        }
        return null;
    }

    public function index(Request $request)
    {
        $this->cekLihat();
        $user = auth()->user();
        $filter = $request->query('tampil', $user->role_id == 12 ? 'tugas' : 'proses');

        $q = PermintaanDesain::with(['creator', 'desainer', 'proyek'])->orderByRaw('deadline is null')->orderBy('deadline')->orderBy('created_at', 'desc');
        if (!Akses::lihatSemua($user)) {
            $q->where('created_by', $user->id);
        }

        match ($filter) {
            'tugas'   => $q->whereIn('status', ['baru', 'revisi', 'dikerjakan']),
            'proses'  => $q->whereIn('status', PermintaanDesain::STATUS_AKTIF),
            'review'  => $q->where('status', 'review'),
            'selesai' => $q->where('status', 'selesai'),
            'batal'   => $q->where('status', 'dibatalkan'),
            default   => null,
        };

        return view('desain.index', [
            'daftar'  => $q->get(),
            'filter'  => $filter,
            'bisaBuat' => Akses::bisaMeminta($user),
            'kerjakan' => Akses::kerjakan($user),
        ]);
    }

    public function create()
    {
        abort_unless(Akses::bisaMeminta(auth()->user()), 403, 'Akses ditolak.');
        return view('desain.create', ['proyek' => $this->daftarProyek()]);
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
            'judul'         => 'required|string|max:200',
            'brief'         => 'required|string|max:3000',
            'ukuran_format' => 'nullable|string|max:150',
            'deadline'      => 'nullable|date|after_or_equal:today',
            'proyek_id'     => 'nullable|integer',
        ] + $this->aturanFile(false, 'referensi'));

        if ($pesan = $this->cekEkstensiSemua($request, 'referensi')) {
            return back()->withInput()->withErrors(['referensi' => $pesan]);
        }
        if (!empty($data['proyek_id']) && !Proyek::whereKey($data['proyek_id'])->exists()) {
            return back()->withInput()->withErrors(['proyek_id' => 'Proyek tidak ditemukan.']);
        }

        $user = auth()->user();
        $companyId = !empty($data['proyek_id']) ? Proyek::whereKey($data['proyek_id'])->value('company_id') : $user->company_id;
        if (!$companyId) {
            return back()->withInput()->withErrors(['judul' => 'Akun Anda belum terhubung ke PT. Pilih proyek atau hubungi Admin.']);
        }

        $desain = DB::transaction(function () use ($data, $companyId, $request, $user) {
            $d = PermintaanDesain::create([
                'company_id'    => $companyId,
                'no_desain'     => Akses::nomorBaru((int) $companyId),
                'judul'         => $data['judul'],
                'brief'         => $data['brief'],
                'ukuran_format' => $data['ukuran_format'] ?? null,
                'deadline'      => $data['deadline'] ?? null,
                'proyek_id'     => $data['proyek_id'] ?? null,
                'status'        => 'baru',
                'created_by'    => $user->id,
            ]);
            $this->simpanFile($d, $request, 'referensi', 'referensi', 1);
            return $d;
        });

        return redirect()->route('desain.show', $desain)->with('success', 'Permintaan desain dikirim ke tim Desain.');
    }

    private function simpanFile(PermintaanDesain $d, Request $request, string $input, string $jenis, int $versi): int
    {
        $n = 0;
        foreach ((array) $request->file($input, []) as $f) {
            if (!$f) continue;
            $info = BerkasPrivat::simpan($f, 'desain/' . $d->company_id . '/' . $d->id);
            DesainFile::create($info + [
                'permintaan_id' => $d->id,
                'jenis'         => $jenis,
                'versi'         => $versi,
                'uploaded_by'   => auth()->id(),
            ]);
            $n++;
        }
        return $n;
    }

    public function show(PermintaanDesain $desain)
    {
        $this->cekAksesPermintaan($desain);
        $desain->load(['creator', 'desainer', 'proyek', 'tayang', 'file.uploader']);
        $user = auth()->user();

        return view('desain.show', [
            'd'         => $desain,
            'referensi' => $desain->file->where('jenis', 'referensi')->values(),
            'hasil'     => $desain->file->where('jenis', 'hasil')->sortByDesc('versi')->groupBy('versi'),
            'kerjakan'  => Akses::kerjakan($user),
            'putuskan'  => Akses::bisaPutuskan($user, $desain),
        ]);
    }

    public function ambil(PermintaanDesain $desain)
    {
        $this->cekKerjakan();
        if ($desain->status !== 'baru') {
            return back()->with('error', 'Permintaan ini sudah diambil.');
        }
        $desain->update(['status' => 'dikerjakan', 'desainer_id' => auth()->id(), 'diambil_at' => now()]);
        return back()->with('success', 'Permintaan diambil. Selamat mengerjakan!');
    }

    public function kirimHasil(Request $request, PermintaanDesain $desain)
    {
        $this->cekKerjakan();
        if (!in_array($desain->status, ['dikerjakan', 'revisi'], true)) {
            return back()->with('error', 'Hasil hanya bisa dikirim saat status Dikerjakan atau Perlu Revisi.');
        }

        $request->validate($this->aturanFile(true, 'hasil'), ['hasil.required' => 'Unggah minimal satu file hasil.']);
        if ($pesan = $this->cekEkstensiSemua($request, 'hasil')) {
            return back()->withErrors(['hasil' => $pesan]);
        }

        DB::transaction(function () use ($request, $desain) {
            $versi = (int) DesainFile::where('permintaan_id', $desain->id)->where('jenis', 'hasil')->max('versi') + 1;
            $this->simpanFile($desain, $request, 'hasil', 'hasil', $versi);
            $desain->update([
                'status'         => 'review',
                'dikirim_at'     => now(),
                'desainer_id'    => $desain->desainer_id ?: auth()->id(),
                'catatan_revisi' => null,
            ]);
        });

        return back()->with('success', 'Hasil desain dikirim ke peminta untuk direview.');
    }

    public function setujui(PermintaanDesain $desain)
    {
        $this->cekAksesPermintaan($desain);
        $this->cekPutuskan($desain);
        if ($desain->status !== 'review') {
            return back()->with('error', 'Hanya desain yang menunggu review yang bisa disetujui.');
        }
        $desain->update(['status' => 'selesai', 'selesai_at' => now()]);
        return back()->with('success', 'Desain disetujui. Permintaan selesai.');
    }

    public function mintaRevisi(Request $request, PermintaanDesain $desain)
    {
        $this->cekAksesPermintaan($desain);
        $this->cekPutuskan($desain);
        if ($desain->status !== 'review') {
            return back()->with('error', 'Revisi hanya bisa diminta saat desain menunggu review.');
        }
        $data = $request->validate(['catatan_revisi' => 'required|string|max:2000']);
        $desain->update([
            'status'         => 'revisi',
            'catatan_revisi' => $data['catatan_revisi'],
            'jumlah_revisi'  => $desain->jumlah_revisi + 1,
        ]);
        return back()->with('success', 'Permintaan revisi dikirim ke tim Desain.');
    }

    public function batalkan(Request $request, PermintaanDesain $desain)
    {
        $this->cekAksesPermintaan($desain);
        $this->cekPutuskan($desain);
        if (!$desain->isAktif()) {
            return back()->with('error', 'Permintaan ini sudah selesai atau dibatalkan.');
        }
        $data = $request->validate(['alasan_batal' => 'required|string|max:500']);
        $desain->update(['status' => 'dibatalkan', 'alasan_batal' => $data['alasan_batal']]);
        return back()->with('success', 'Permintaan desain dibatalkan.');
    }

    /** Unduh / pratinjau file. Hak akses mengikuti permintaannya. */
    public function file(PermintaanDesain $desain, DesainFile $file)
    {
        $this->cekAksesPermintaan($desain);
        abort_unless((int) $file->permintaan_id === (int) $desain->id, 404);

        return BerkasPrivat::kirim($file->path, $file->nama_asli, $file->mime, request()->boolean('unduh'));
    }
}
