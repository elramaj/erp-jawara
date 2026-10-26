<?php

namespace App\Http\Controllers;

use App\Models\DokumenArsip;
use App\Services\DokumenService as Akses;
use App\Support\BerkasPrivat;
use Illuminate\Http\Request;

class DokumenArsipController extends Controller
{
    private const FILE_RULE = 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,zip|max:10240'; // 10 MB

    private function cekLihat(): void
    {
        abort_unless(Akses::lihatArsip(auth()->user()), 403, 'Akses ditolak.');
    }

    private function cekKelola(): void
    {
        abort_unless(Akses::kelola(auth()->user()), 403, 'Akses ditolak.');
    }

    public function index(Request $request)
    {
        $this->cekLihat();
        $filter = $request->query('tampil', 'semua');
        $cari = trim((string) $request->query('q', ''));

        $q = DokumenArsip::orderByRaw('tanggal_kedaluwarsa is null')->orderBy('tanggal_kedaluwarsa')->orderBy('jenis');
        if ($cari !== '') {
            $q->where(function ($w) use ($cari) {
                $w->where('nama', 'like', "%{$cari}%")->orWhere('jenis', 'like', "%{$cari}%")->orWhere('nomor', 'like', "%{$cari}%");
            });
        }

        $hariIni = now()->toDateString();
        $batas = now()->addDays(DokumenArsip::HARI_PERINGATAN)->toDateString();
        match ($filter) {
            'segera'      => $q->whereDate('tanggal_kedaluwarsa', '>=', $hariIni)->whereDate('tanggal_kedaluwarsa', '<=', $batas),
            'kedaluwarsa' => $q->whereDate('tanggal_kedaluwarsa', '<', $hariIni),
            'aktif'       => $q->where(fn($w) => $w->whereNull('tanggal_kedaluwarsa')->orWhereDate('tanggal_kedaluwarsa', '>', $batas)),
            default       => null,
        };

        return view('arsip.index', [
            'daftar' => $q->get(),
            'filter' => $filter,
            'cari'   => $cari,
            'kelola' => Akses::kelola(auth()->user()),
        ]);
    }

    private function aturan(bool $fileWajib): array
    {
        return [
            'jenis'               => 'required|string|max:100',
            'nama'                => 'required|string|max:200',
            'nomor'               => 'nullable|string|max:100',
            'penerbit'            => 'nullable|string|max:150',
            'tanggal_terbit'      => 'nullable|date',
            'tanggal_kedaluwarsa' => request()->filled('tanggal_terbit') ? 'nullable|date|after_or_equal:tanggal_terbit' : 'nullable|date',
            'catatan'             => 'nullable|string|max:2000',
            'file'                => [$fileWajib ? 'required' : 'nullable', ...explode('|', self::FILE_RULE)],
        ];
    }

    public function create()
    {
        $this->cekKelola();
        return view('arsip.form', ['arsip' => null, 'jenisSaran' => DokumenArsip::JENIS_SARAN]);
    }

    public function store(Request $request)
    {
        $this->cekKelola();
        $data = $request->validate($this->aturan(true));

        $companyId = auth()->user()->company_id;
        if (!$companyId) {
            return back()->withInput()->withErrors(['jenis' => 'Akun Anda belum terhubung ke PT, sehingga arsip tidak bisa disimpan. Login dengan akun PT yang bersangkutan.']);
        }

        $info = BerkasPrivat::simpan($request->file('file'), 'arsip-legalitas/' . $companyId);
        DokumenArsip::create([
            'company_id' => $companyId,
            'file_path'  => $info['path'],
            'nama_asli'  => $info['nama_asli'],
            'mime'       => $info['mime'],
            'created_by' => auth()->id(),
        ] + collect($data)->except('file')->all());

        return redirect()->route('arsip.index')->with('success', 'Dokumen arsip disimpan.');
    }

    public function edit(DokumenArsip $arsip)
    {
        $this->cekKelola();
        return view('arsip.form', ['arsip' => $arsip, 'jenisSaran' => DokumenArsip::JENIS_SARAN]);
    }

    public function update(Request $request, DokumenArsip $arsip)
    {
        $this->cekKelola();
        $data = collect($request->validate($this->aturan(false)))->except('file')->all();
        $data['updated_by'] = auth()->id();

        if ($request->hasFile('file')) {
            $info = BerkasPrivat::simpan($request->file('file'), 'arsip-legalitas/' . $arsip->company_id);
            BerkasPrivat::hapus($arsip->file_path);
            $data += ['file_path' => $info['path'], 'nama_asli' => $info['nama_asli'], 'mime' => $info['mime']];
        }

        $arsip->update($data);
        return redirect()->route('arsip.index')->with('success', 'Dokumen arsip diperbarui.');
    }

    public function destroy(DokumenArsip $arsip)
    {
        $this->cekKelola();
        BerkasPrivat::hapus($arsip->file_path);
        $arsip->delete();
        return redirect()->route('arsip.index')->with('success', 'Dokumen arsip dihapus.');
    }

    public function file(DokumenArsip $arsip)
    {
        // Pemenuhan permintaan dokumen dari arsip: penyiap & peminta yang berhak melihat arsip sudah tercakup lihatArsip().
        $this->cekLihat();
        return BerkasPrivat::kirim($arsip->file_path, $arsip->nama_asli, $arsip->mime, request()->boolean('unduh'));
    }
}
