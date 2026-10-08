<?php

namespace App\Http\Controllers;

use App\Models\Tayang;
use App\Services\ProdukTayangService as Akses;
use Illuminate\Http\Request;

class TayangController extends Controller
{
    private function cekLihat(): void
    {
        if (!Akses::lihatTayang(auth()->user())) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function cekKelola(): void
    {
        if (!Akses::kelolaTayang(auth()->user())) {
            abort(403, 'Akses ditolak.');
        }
    }

    /** Sales hanya boleh membuka tayang dari permintaannya sendiri. */
    private function cekAksesTayang(Tayang $tayang): void
    {
        $this->cekLihat();
        $user = auth()->user();
        if ((int) $user->role_id === 5 && !$user->isSuperAdmin() && !Akses::kelolaTayang($user)) {
            if ((int) optional($tayang->permintaan)->created_by !== (int) $user->id) {
                abort(403, 'Akses ditolak.');
            }
        }
    }

    public function index(Request $request)
    {
        $this->cekLihat();
        $user = auth()->user();
        $filter = $request->query('tampil', 'proses');

        $q = Tayang::with(['permintaan.creator', 'proyek', 'penangan'])->orderBy('created_at', 'desc');
        if ((int) $user->role_id === 5 && !$user->isSuperAdmin()) {
            $q->whereHas('permintaan', fn($p) => $p->where('created_by', $user->id));
        }
        if ((int) $user->role_id === 12 && !$user->isSuperAdmin()) {
            $filter = $request->query('tampil', 'desain');
        }

        match ($filter) {
            'proses' => $q->whereIn('status', ['antrian', 'disiapkan']),
            'tayang' => $q->where('status', 'tayang'),
            'turun'  => $q->where('status', 'turun'),
            'desain' => $q->where('perlu_desain', true)->whereIn('status', ['antrian', 'disiapkan', 'tayang']),
            default  => null,
        };

        return view('tayang.index', [
            'tayang' => $q->get(),
            'filter' => $filter,
            'kelola' => Akses::kelolaTayang($user),
        ]);
    }

    public function show(Tayang $tayang)
    {
        $tayang->load(['permintaan.item.opsiTerpilih', 'permintaan.creator', 'permintaan.proyek', 'penangan', 'proyek']);
        $this->cekAksesTayang($tayang);

        $user = auth()->user();
        return view('tayang.show', [
            't'              => $tayang,
            'kelola'         => Akses::kelolaTayang($user),
            'lihatHargaBeli' => Akses::lihatHargaBeli($user),
            'bisaLihatPermintaan' => Akses::bisaLihatPermintaan($user, $tayang->permintaan),
        ]);
    }

    public function mulai(Tayang $tayang)
    {
        $this->cekKelola();
        if ($tayang->status !== 'antrian') {
            return back()->with('error', 'Hanya yang berstatus Antrian yang bisa dimulai.');
        }
        $tayang->update(['status' => 'disiapkan', 'ditangani_oleh' => auth()->id()]);
        return back()->with('success', 'Mulai disiapkan.');
    }

    // Simpan platform, link, kebutuhan desain, catatan (tanpa mengubah status)
    public function update(Request $request, Tayang $tayang)
    {
        $this->cekKelola();
        if (!in_array($tayang->status, ['antrian', 'disiapkan', 'tayang'], true)) {
            return back()->with('error', 'Tayang yang sudah diturunkan tidak bisa diubah.');
        }

        $data = $request->validate([
            'platform'       => 'nullable|string|max:100',
            'link_tayang'    => 'nullable|url|max:500',
            'catatan_desain' => 'nullable|string|max:2000',
            'catatan_tayang' => 'nullable|string|max:2000',
        ]);
        $data['perlu_desain'] = $request->boolean('perlu_desain');

        $tayang->update($data);
        return back()->with('success', 'Data tayang disimpan.');
    }

    public function tayangkan(Request $request, Tayang $tayang)
    {
        $this->cekKelola();
        if (!in_array($tayang->status, ['antrian', 'disiapkan'], true)) {
            return back()->with('error', 'Status tidak sesuai untuk ditayangkan.');
        }

        $request->validate([
            'platform'    => 'nullable|string|max:100',
            'link_tayang' => 'nullable|url|max:500',
        ]);
        $link = $request->input('link_tayang', $tayang->link_tayang);
        if (!$link) {
            return back()->withErrors(['link_tayang' => 'Isi link tayang terlebih dahulu.']);
        }

        $tayang->update([
            'platform'       => $request->input('platform', $tayang->platform),
            'link_tayang'    => $link,
            'status'         => 'tayang',
            'tayang_at'      => now(),
            'ditangani_oleh' => $tayang->ditangani_oleh ?: auth()->id(),
        ]);

        return back()->with('success', 'Ditandai sudah tayang.');
    }

    public function turunkan(Tayang $tayang)
    {
        $this->cekKelola();
        if ($tayang->status !== 'tayang') {
            return back()->with('error', 'Hanya yang sedang tayang yang bisa diturunkan.');
        }
        $tayang->update(['status' => 'turun', 'turun_at' => now()]);
        return back()->with('success', 'Tayang diturunkan.');
    }
}
