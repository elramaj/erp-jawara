<?php

namespace App\Http\Controllers;

use App\Models\BebanOperasional;
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

        $beban = BebanOperasional::with(['creator', 'company'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalBulanIni = $beban->sum('nominal');

        // Breakdown per kategori, buat rekap kecil di atas tabel.
        $perKategori = $beban->groupBy('kategori')->map(function ($items) {
            return $items->sum('nominal');
        });

        return view('keuangan.pengeluaran.index', compact(
            'beban', 'bulan', 'tahun', 'totalBulanIni', 'perKategori'
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
        ]);

        BebanOperasional::create([
            'company_id'  => auth()->user()->company_id,
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
        $beban->delete();

        return redirect()->route('pengeluaran.index', compact('bulan', 'tahun'))
            ->with('success', 'Beban operasional berhasil dihapus.');
    }
}