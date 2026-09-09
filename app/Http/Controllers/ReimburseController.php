<?php

namespace App\Http\Controllers;

use App\Models\BebanOperasional;
use App\Models\Reimburse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReimburseController extends Controller
{
    // Sama kayak akses Laporan Keuangan & Pengeluaran (admin/finance/bos).
    private function cekAksesKeuangan()
    {
        if (!in_array(auth()->user()->role_id, [1, 2, 11])) {
            abort(403, 'Akses ditolak.');
        }
    }

    // Daftar klaim reimburse milik user yang login
    public function index()
    {
        $reimburse = Reimburse::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('reimburse.index', compact('reimburse'));
    }

    // Form pengajuan baru
    public function create()
    {
        return view('reimburse.create');
    }

    // Simpan pengajuan
    public function store(Request $request)
    {
        $request->validate([
            'kategori'            => 'required|in:' . implode(',', array_keys(Reimburse::KATEGORI)),
            'tanggal_pengeluaran' => 'required|date',
            'nominal'             => 'required|numeric|min:1',
            'keterangan'          => 'required|string|max:255',
            'bukti'               => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('bukti')) {
            $path = $request->file('bukti')->store('reimburse', 'public');
        }

        Reimburse::create([
            'company_id'          => auth()->user()->company_id,
            'user_id'             => auth()->id(),
            'kategori'            => $request->kategori,
            'tanggal_pengeluaran' => $request->tanggal_pengeluaran,
            'nominal'             => $request->nominal,
            'keterangan'          => $request->keterangan,
            'bukti'               => $path,
            'status'              => 'pending',
        ]);

        return redirect()->route('reimburse.index')
            ->with('success', 'Pengajuan reimburse berhasil dikirim, menunggu persetujuan Keuangan!');
    }

    // Halaman review untuk Keuangan
    public function review()
    {
        $this->cekAksesKeuangan();
        $reimburse = Reimburse::with(['user', 'approver'])
            ->orderByRaw("FIELD(status, 'pending', 'disetujui', 'ditolak')")
            ->orderBy('created_at', 'desc')
            ->get();
        return view('reimburse.review', compact('reimburse'));
    }

    // Setujui atau tolak klaim
    public function updateStatus(Request $request, Reimburse $reimburse)
    {
        $this->cekAksesKeuangan();
        $request->validate([
            'status'           => 'required|in:disetujui,ditolak',
            'catatan_approval' => 'nullable|string|max:255',
        ]);

        if ($reimburse->status !== 'pending') {
            return redirect()->route('reimburse.review')
                ->with('error', 'Klaim ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($request, $reimburse) {
            $bebanId = null;

            // Kalau disetujui, otomatis catat sebagai Beban Operasional
            // (kategori reimburse) biar kehitung di Laporan Laba Rugi.
            if ($request->status === 'disetujui') {
                $beban = BebanOperasional::create([
                    'company_id'  => $reimburse->company_id,
                    'user_id'     => $reimburse->user_id,
                    'kategori'    => 'reimburse',
                    'tanggal'     => $reimburse->tanggal_pengeluaran,
                    'nominal'     => $reimburse->nominal,
                    'keterangan'  => $reimburse->keterangan,
                    'created_by'  => auth()->id(),
                ]);
                $bebanId = $beban->id;
            }

            $reimburse->update([
                'status'                => $request->status,
                'approved_by'           => auth()->id(),
                'approved_at'           => now(),
                'catatan_approval'      => $request->catatan_approval,
                'beban_operasional_id'  => $bebanId,
            ]);
        });

        return redirect()->route('reimburse.review')
            ->with('success', 'Status klaim reimburse berhasil diupdate!');
    }
}
