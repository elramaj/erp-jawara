<?php

namespace App\Http\Controllers;

use App\Models\BebanOperasional;
use App\Models\Reimburse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    // Sama kayak akses Laporan Keuangan (admin/finance/bos).
    private function cekAkses()
    {
        if (!in_array(auth()->user()->role_id, [1, 2, 11])) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index(Request $request)
    {
        $this->cekAkses();
        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        $beban = BebanOperasional::with(['creator', 'company', 'karyawan'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalBulanIni = $beban->sum('nominal');

        // Breakdown per kategori, buat rekap kecil di atas tabel.
        $perKategori = $beban->groupBy('kategori')->map(function ($items) {
            return $items->sum('nominal');
        });

        // Buat dropdown pilih karyawan pas kategori = reimburse.
        $karyawan = User::where('is_active', 1)->forCurrentCompany()->orderBy('name')->get();

        return view('keuangan.pengeluaran.index', compact(
            'beban', 'bulan', 'tahun', 'totalBulanIni', 'perKategori', 'karyawan'
        ));
    }

    public function store(Request $request)
    {
        $this->cekAkses();
        $request->validate([
            'kategori'    => 'required|in:' . implode(',', array_keys(BebanOperasional::KATEGORI)),
            'tanggal'     => 'required|date',
            'nominal'     => 'required|numeric|min:1',
            'keterangan'  => 'nullable|string|max:255',
            // Wajib pilih karyawan cuma kalau kategorinya reimburse.
            'user_id'     => 'required_if:kategori,reimburse|nullable|exists:users,id',
        ]);

        BebanOperasional::create([
            'company_id'  => auth()->user()->company_id,
            'user_id'     => $request->kategori === 'reimburse' ? $request->user_id : null,
            'kategori'    => $request->kategori,
            'tanggal'     => $request->tanggal,
            'nominal'     => $request->nominal,
            'keterangan'  => $request->keterangan,
            'created_by'  => auth()->id(),
        ]);

        return redirect()->route('pengeluaran.index', [
            'bulan' => Carbon::parse($request->tanggal)->month,
            'tahun' => Carbon::parse($request->tanggal)->year,
        ])->with('success', 'Beban operasional berhasil dicatat.');
    }

    public function destroy(BebanOperasional $beban)
    {
        $this->cekAkses();
        $bulan = $beban->tanggal->month;
        $tahun = $beban->tanggal->year;

        // Beban yang otomatis dibuat dari klaim reimburse yang disetujui
        // tidak boleh dihapus langsung dari sini. FK beban_operasional_id di
        // reimburse pakai onDelete('set null'), jadi kalau dipaksa hapus,
        // baris reimburse-nya TIDAK ikut terhapus -- cuma jadi "menggantung"
        // (status tetap disetujui, tapi tidak lagi kehitung di Laporan
        // Keuangan, tanpa jejak kenapa). Pembatalan harus lewat menu Review
        // Reimburse (tombol Batalkan), yang menghapus baris ini SEKALIGUS
        // mengubah status klaim jadi dibatalkan.
        $terkaitReimburse = Reimburse::withoutCompanyScope()
            ->where('beban_operasional_id', $beban->id)
            ->exists();

        if ($terkaitReimburse) {
            return redirect()->route('pengeluaran.index', compact('bulan', 'tahun'))
                ->with('error', 'Beban ini berasal dari klaim reimburse yang sudah disetujui. Batalkan lewat menu Review Reimburse, bukan dari sini.');
        }

        $beban->delete();

        return redirect()->route('pengeluaran.index', compact('bulan', 'tahun'))
            ->with('success', 'Beban operasional berhasil dihapus.');
    }
}