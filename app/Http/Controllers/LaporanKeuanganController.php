<?php

namespace App\Http\Controllers;

use App\Models\Fj;
use App\Models\FjBayar;
use App\Models\Fb;
use App\Models\FbBayar;
use App\Models\BebanOperasional;
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
        $isSuperAdmin = auth()->user()->isSuperAdmin();
        $companyId = auth()->user()->company_id;

        // Total pemasukan (pembayaran FJ bulan ini)
        // FjBayar tidak punya company_id sendiri, jadi discope lewat relasi fj->company_id.
        $pemasukan = FjBayar::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->when(!$isSuperAdmin, fn($q) => $q->whereHas('fj', fn($q2) => $q2->where('company_id', $companyId)))
            ->sum('jumlah');

        // Total pengeluaran (pembayaran FB bulan ini)
        $pengeluaranPembelian = FbBayar::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->when(!$isSuperAdmin, fn($q) => $q->whereHas('fb', fn($q2) => $q2->where('company_id', $companyId)))
            ->sum('jumlah');

        // Beban operasional bulan ini (gaji, sewa, listrik, transport, REIMBURSE karyawan, dll)
        // -- sebelumnya modul ini gak pernah dihitung di Laporan Keuangan sama sekali,
        // jadi reimburse yang disetujui gak pernah kelihatan kehitung di laba-rugi.
        // BebanOperasional sudah otomatis discope company via trait BelongsToCompany.
        $pengeluaranOperasional = BebanOperasional::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('nominal');

        $pengeluaran = $pengeluaranPembelian + $pengeluaranOperasional;

        $labaRugi = $pemasukan - $pengeluaran;

        // Piutang (FJ belum lunas) -- Fj sudah otomatis discope company via trait
        $piutang = Fj::whereIn('status', ['unpaid', 'partial'])->sum('total')
            - Fj::whereIn('status', ['unpaid', 'partial'])->sum('terbayar');

        // Hutang (FB belum lunas) -- Fb sudah otomatis discope company via trait
        $hutang = Fb::whereIn('status', ['unpaid', 'partial'])->sum('total')
            - Fb::whereIn('status', ['unpaid', 'partial'])->sum('terbayar');

        // Riwayat transaksi bulan ini
        $riwayatMasuk = FjBayar::with(['fj.so.customer', 'fj.company'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->when(!$isSuperAdmin, fn($q) => $q->whereHas('fj', fn($q2) => $q2->where('company_id', $companyId)))
            ->orderBy('tanggal', 'desc')->get();

        $riwayatKeluar = FbBayar::with(['fb.po.supplier', 'fb.company'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->when(!$isSuperAdmin, fn($q) => $q->whereHas('fb', fn($q2) => $q2->where('company_id', $companyId)))
            ->orderBy('tanggal', 'desc')->get();

        $riwayatBeban = BebanOperasional::with(['creator', 'karyawan', 'company'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')->get();

        return view('keuangan.laporan.index', compact(
            'bulan', 'tahun', 'pemasukan', 'pengeluaran',
            'pengeluaranPembelian', 'pengeluaranOperasional',
            'labaRugi', 'piutang', 'hutang',
            'riwayatMasuk', 'riwayatKeluar', 'riwayatBeban'
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
        $isSuperAdmin = auth()->user()->isSuperAdmin();
        $companyId = auth()->user()->company_id;

        $pemasukan   = FjBayar::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
            ->when(!$isSuperAdmin, fn($q) => $q->whereHas('fj', fn($q2) => $q2->where('company_id', $companyId)))
            ->sum('jumlah');
        $pengeluaranPembelian = FbBayar::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
            ->when(!$isSuperAdmin, fn($q) => $q->whereHas('fb', fn($q2) => $q2->where('company_id', $companyId)))
            ->sum('jumlah');
        $pengeluaranOperasional = BebanOperasional::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
            ->sum('nominal');
        $pengeluaran = $pengeluaranPembelian + $pengeluaranOperasional;
        $labaRugi    = $pemasukan - $pengeluaran;
        $piutang     = \App\Models\Fj::whereIn('status', ['unpaid','partial'])->sum('total')
                    - \App\Models\Fj::whereIn('status', ['unpaid','partial'])->sum('terbayar');

        $riwayatMasuk  = FjBayar::with(['fj.so.customer', 'fj.company'])
            ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
            ->when(!$isSuperAdmin, fn($q) => $q->whereHas('fj', fn($q2) => $q2->where('company_id', $companyId)))
            ->orderBy('tanggal')->get();
        $riwayatKeluar = FbBayar::with(['fb.po.supplier', 'fb.company'])
            ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
            ->when(!$isSuperAdmin, fn($q) => $q->whereHas('fb', fn($q2) => $q2->where('company_id', $companyId)))
            ->orderBy('tanggal')->get();
        $riwayatBeban = BebanOperasional::with(['creator', 'karyawan'])
            ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')->get();

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F-Y');

        $pdf = Pdf::loadView('laporan.keuangan-pdf', compact(
            'bulan', 'tahun', 'pemasukan', 'pengeluaran',
            'pengeluaranPembelian', 'pengeluaranOperasional',
            'labaRugi', 'piutang', 'riwayatMasuk', 'riwayatKeluar', 'riwayatBeban'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-keuangan-' . $namaBulan . '.pdf');
    }
}