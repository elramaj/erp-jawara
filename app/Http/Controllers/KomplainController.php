<?php

namespace App\Http\Controllers;

use App\Models\Komplain;
use App\Models\KomplainTimeline;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Http\Request;

class KomplainController extends Controller
{
    private function cekAkses()
    {
        if (!in_array(auth()->user()->role_id, [1, 4, 5, 7, 11])) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index()
    {
        $this->cekAkses();
        $user = auth()->user();

        // Admin & bos lihat semua, lainnya hanya yang dibuat sendiri atau di-handle
        if (in_array($user->role_id, [1, 11])) {
            $komplain = Komplain::with(['proyek', 'creator', 'handler'])
                ->orderBy('created_at', 'desc')->get();
        } else {
            $komplain = Komplain::with(['proyek', 'creator', 'handler'])
                ->where(function($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('handled_by', $user->id);
                })
                ->orderBy('created_at', 'desc')->get();
        }

        $totalOpen       = $komplain->where('status', 'open')->count();
        $totalInProgress = $komplain->where('status', 'in_progress')->count();
        $totalResolved   = $komplain->where('status', 'resolved')->count();

        return view('komplain.index', compact('komplain', 'totalOpen', 'totalInProgress', 'totalResolved'));
    }

    public function create()
    {
        $this->cekAkses();
        $proyek = Proyek::orderBy('nama_proyek')->get();
        $no_komplain = 'CMP-' . date('Ymd') . '-' . str_pad(
            Komplain::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT
        );
        return view('komplain.create', compact('proyek', 'no_komplain'));
    }

    public function store(Request $request)
    {
        $this->cekAkses();
        $request->validate([
            'judul'      => 'required|string|max:255',
            'jenis'      => 'required|in:barang,dokumen',
            'prioritas'  => 'required|in:low,medium,high,critical',
            'proyek_id'  => 'nullable|exists:proyek,id',
            'deskripsi'  => 'nullable|string',
        ]);

        $no_komplain = 'CMP-' . date('Ymd') . '-' . str_pad(
            Komplain::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT
        );

        $komplain = Komplain::create([
            'no_komplain'   => $no_komplain,
            'proyek_id'     => $request->proyek_id,
            'jenis'         => $request->jenis,
            'prioritas'     => $request->prioritas,
            'judul'         => $request->judul,
            'deskripsi'     => $request->deskripsi,
            'status'        => 'open',
            'masih_garansi' => $request->has('masih_garansi') ? 1 : 0,
            'created_by'    => auth()->id(),
        ]);

        KomplainTimeline::create([
            'komplain_id' => $komplain->id,
            'keterangan'  => 'Komplain dibuat oleh ' . auth()->user()->name,
            'status_baru' => 'open',
            'created_by'  => auth()->id(),
        ]);

        return redirect()->route('komplain.show', $komplain)
            ->with('success', 'Komplain berhasil dibuat!');
    }

    public function show(Komplain $komplain)
    {
        $this->cekAkses();
        $komplain->load(['proyek', 'creator', 'handler', 'timeline.creator']);
        $users = User::where('is_active', 1)->orderBy('name')->get();
        return view('komplain.show', compact('komplain', 'users'));
    }

    public function updateStatus(Request $request, Komplain $komplain)
    {
        $this->cekAkses();
        $request->validate([
            'status'     => 'required|in:open,in_progress,resolved',
            'keterangan' => 'required|string',
            'handled_by' => 'nullable|exists:users,id',
        ]);

        $data = ['status' => $request->status];
        if ($request->handled_by) $data['handled_by'] = $request->handled_by;
        if ($request->status == 'resolved') $data['resolved_at'] = now();

        $komplain->update($data);

        KomplainTimeline::create([
            'komplain_id' => $komplain->id,
            'keterangan'  => $request->keterangan,
            'status_baru' => $request->status,
            'created_by'  => auth()->id(),
        ]);

        return back()->with('success', 'Status komplain berhasil diupdate!');
    }
}