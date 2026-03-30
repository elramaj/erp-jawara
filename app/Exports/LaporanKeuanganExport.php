<?php

namespace App\Exports;

use App\Models\FjBayar;
use App\Models\FbBayar;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanKeuanganExport implements FromArray, WithEvents, WithTitle
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function title(): string
    {
        return 'Laporan Keuangan';
    }

    public function array(): array
    {
        $bulan = $this->bulan;
        $tahun = $this->tahun;

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');

        $pemasukan  = FjBayar::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->sum('jumlah');
        $pengeluaran = FbBayar::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->sum('jumlah');
        $labaRugi   = $pemasukan - $pengeluaran;

        $riwayatMasuk  = FjBayar::with(['fj.so.customer'])->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->orderBy('tanggal')->get();
        $riwayatKeluar = FbBayar::with(['fb.po.supplier'])->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->orderBy('tanggal')->get();

        $rows = [];

        // Header
        $rows[] = ['LAPORAN KEUANGAN - ' . strtoupper($namaBulan)];
        $rows[] = ['Dicetak pada: ' . now()->format('d M Y H:i')];
        $rows[] = [];

        // Summary
        $rows[] = ['RINGKASAN'];
        $rows[] = ['Total Pemasukan', 'Rp ' . number_format($pemasukan, 0, ',', '.')];
        $rows[] = ['Total Pengeluaran', 'Rp ' . number_format($pengeluaran, 0, ',', '.')];
        $rows[] = [($labaRugi >= 0 ? 'Laba' : 'Rugi'), 'Rp ' . number_format(abs($labaRugi), 0, ',', '.')];
        $rows[] = [];

        // Pemasukan
        $rows[] = ['RINCIAN PEMASUKAN'];
        $rows[] = ['No', 'Tanggal', 'Customer', 'No. Faktur', 'Jumlah'];
        foreach ($riwayatMasuk as $i => $r) {
            $rows[] = [
                $i + 1,
                $r->tanggal->format('d M Y'),
                $r->fj->so->customer->nama ?? '-',
                $r->fj->no_fj ?? '-',
                'Rp ' . number_format($r->jumlah, 0, ',', '.'),
            ];
        }
        $rows[] = ['', '', '', 'Total', 'Rp ' . number_format($pemasukan, 0, ',', '.')];
        $rows[] = [];

        // Pengeluaran
        $rows[] = ['RINCIAN PENGELUARAN'];
        $rows[] = ['No', 'Tanggal', 'Supplier', 'No. Faktur', 'Jumlah'];
        foreach ($riwayatKeluar as $i => $r) {
            $rows[] = [
                $i + 1,
                $r->tanggal->format('d M Y'),
                $r->fb->po->supplier->nama ?? '-',
                $r->fb->no_fb ?? '-',
                'Rp ' . number_format($r->jumlah, 0, ',', '.'),
            ];
        }
        $rows[] = ['', '', '', 'Total', 'Rp ' . number_format($pengeluaran, 0, ',', '.')];

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Style judul
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);

                // Auto width
                foreach (range('A', 'E') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}