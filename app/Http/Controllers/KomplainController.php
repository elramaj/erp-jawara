<?php

namespace App\Http\Controllers;

use App\Models\Komplain;
use App\Models\KomplainTimeline;
use App\Models\Proyek;
use App\Models\User;
use App\Support\CetakTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            Komplain::withoutCompanyScope()->whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT
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
            Komplain::withoutCompanyScope()->whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT
        );

        $komplain = Komplain::create([
            'company_id'    => auth()->user()->company_id,
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
        $users = User::where('is_active', 1)->forCurrentCompany()->orderBy('name')->get();
        return view('komplain.show', compact('komplain', 'users'));
    }

    // Cetak Tanda Terima Barang Service (template: resources/views/cetak/<template PT>/tanda-terima-service.blade.php)
    public function cetakTerima(Komplain $komplain)
    {
        $this->cekAkses();
        $komplain->load(['proyek', 'handler', 'company', 'barangService']);
        return CetakTemplate::view('tanda-terima-service', $komplain->company, ['komplain' => $komplain]);
    }

    // Simpan isian Tanda Terima Barang Service (barang, kelengkapan, kondisi, penyerah)
    public function simpanTandaTerima(Request $request, Komplain $komplain)
    {
        $this->cekAkses();

        $data = $request->validate([
            'tanggal_terima_service'  => 'nullable|date',
            'kelengkapan_service'     => 'nullable|string|max:1000',
            'kondisi_fisik'           => 'nullable|string|max:1000',
            'penyerah_nama'           => 'nullable|string|max:255',
            'penyerah_kontak'         => 'nullable|string|max:100',
            'barang'                  => 'nullable|array|max:20',
            'barang.*.nama_barang'    => 'nullable|string|max:255',
            'barang.*.serial_number'  => 'nullable|string|max:255',
            'barang.*.qty'            => 'nullable|integer|min:1|max:100000',
        ]);

        DB::transaction(function () use ($komplain, $data) {
            $komplain->update([
                'tanggal_terima_service' => $data['tanggal_terima_service'] ?? null,
                'kelengkapan_service'    => $data['kelengkapan_service'] ?? null,
                'kondisi_fisik'          => $data['kondisi_fisik'] ?? null,
                'penyerah_nama'          => $data['penyerah_nama'] ?? null,
                'penyerah_kontak'        => $data['penyerah_kontak'] ?? null,
            ]);

            // Ganti seluruh daftar barang; baris tanpa nama barang diabaikan.
            $komplain->barangService()->delete();
            foreach ($data['barang'] ?? [] as $b) {
                if (trim((string) ($b['nama_barang'] ?? '')) === '') {
                    continue;
                }
                $komplain->barangService()->create([
                    'nama_barang'   => trim($b['nama_barang']),
                    'serial_number' => $b['serial_number'] ?? null,
                    'qty'           => $b['qty'] ?? 1,
                ]);
            }

            KomplainTimeline::create([
                'komplain_id' => $komplain->id,
                'keterangan'  => 'Data Tanda Terima Barang Service diperbarui.',
                'status_baru' => $komplain->status,
                'created_by'  => auth()->id(),
            ]);
        });

        return back()->with('success', 'Data tanda terima service disimpan.');
    }

        public function updateStatus(Request $request, Komplain $komplain)
    {
        $this->cekAkses();
        $request->validate([
            'status'      => 'required|in:open,in_progress,resolved',
            'keterangan'  => 'required|string',
            'handled_by'  => 'nullable|exists:users,id',
            'no_servisan' => 'nullable|string|max:100',
        ]);

        $data = ['status' => $request->status];
        if ($request->handled_by) $data['handled_by'] = $request->handled_by;
        if ($request->status == 'resolved') $data['resolved_at'] = now();
        if ($request->filled('no_servisan')) $data['no_servisan'] = $request->no_servisan;

        $komplain->update($data);

        $keterangan = $request->keterangan;
        if ($request->filled('no_servisan')) {
            $keterangan .= " (No. Servisan: {$request->no_servisan})";
        }

        KomplainTimeline::create([
            'komplain_id' => $komplain->id,
            'keterangan'  => $keterangan,
            'status_baru' => $request->status,
            'created_by'  => auth()->id(),
        ]);

        return back()->with('success', 'Status komplain berhasil diperbarui!');
    }
}