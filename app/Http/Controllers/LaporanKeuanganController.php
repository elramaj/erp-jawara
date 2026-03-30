<?php

namespace App\Http\Controllers;

use App\Models\Fj;
use App\Models\FjBayar;
use App\Models\Fb;
use App\Models\FbBayar;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\LaporanKeuanganExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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
    public function exportExcel(Request $request)
{
    $this->cekAkses();
    $bulan = $request->bulan ?? Carbon::now()->month;
    $tahun = $request->tahun ?? Carbon::now()->year;
    $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F-Y');

    return Excel::download(
        new LaporanKeuanganExport($bulan, $tahun),
        'laporan-keuangan-' . $namaBulan . '.xlsx'
    );
}
    public function exportPdf(Request $request)
    {
        $this->cekAkses();
        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        $pemasukan   = FjBayar::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->sum('jumlah');
        $pengeluaran = FbBayar::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->sum('jumlah');
        $labaRugi    = $pemasukan - $pengeluaran;
        $piutang     = \App\Models\Fj::whereIn('status', ['unpaid','partial'])->sum('total')
                    - \App\Models\Fj::whereIn('status', ['unpaid','partial'])->sum('terbayar');

        $riwayatMasuk  = FjBayar::with(['fj.so.customer'])->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->orderBy('tanggal')->get();
        $riwayatKeluar = FbBayar::with(['fb.po.supplier'])->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->orderBy('tanggal')->get();

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F-Y');

        $pdf = Pdf::loadView('laporan.keuangan-pdf', compact(
            'bulan', 'tahun', 'pemasukan', 'pengeluaran',
            'labaRugi', 'piutang', 'riwayatMasuk', 'riwayatKeluar'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-keuangan-' . $namaBulan . '.pdf');
    }
}