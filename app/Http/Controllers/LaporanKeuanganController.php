<?php

namespace App\Http\Controllers;

use App\Models\Fj;
use App\Models\FjBayar;
use App\Models\Fb;
use App\Models\FbBayar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanKeuanganController extends Controller
{
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

        // Total pemasukan (pembayaran FJ bulan ini)
        $pemasukan = FjBayar::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('jumlah');

        // Total pengeluaran (pembayaran FB bulan ini)
        $pengeluaran = FbBayar::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('jumlah');

        $labaRugi = $pemasukan - $pengeluaran;

        // Piutang (FJ belum lunas)
        $piutang = Fj::whereIn('status', ['unpaid', 'partial'])->sum('total')
            - Fj::whereIn('status', ['unpaid', 'partial'])->sum('terbayar');

        // Hutang (FB belum lunas)
        $hutang = Fb::whereIn('status', ['unpaid', 'partial'])->sum('total')
            - Fb::whereIn('status', ['unpaid', 'partial'])->sum('terbayar');

        // Riwayat transaksi bulan ini
        $riwayatMasuk = FjBayar::with(['fj.so.customer'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')->get();

        $riwayatKeluar = FbBayar::with(['fb.po.supplier'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')->get();

        return view('keuangan.laporan.index', compact(
            'bulan', 'tahun', 'pemasukan', 'pengeluaran',
            'labaRugi', 'piutang', 'hutang',
            'riwayatMasuk', 'riwayatKeluar'
        ));
    }
}