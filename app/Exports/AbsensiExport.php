<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class AbsensiExport implements FromArray, WithEvents, WithTitle
{
    protected $tanggalMulai;
    protected $tanggalSelesai;

    public function __construct($tanggalMulai, $tanggalSelesai)
    {
        $this->tanggalMulai  = Carbon::parse($tanggalMulai);
        $this->tanggalSelesai = Carbon::parse($tanggalSelesai);
    }

    public function title(): string
    {
        return 'Laporan Kehadiran';
    }

    public function array(): array
    {
        $mulai   = $this->tanggalMulai;
        $selesai = $this->tanggalSelesai;

        // Generate daftar tanggal
        $tanggals = [];
        $current = $mulai->copy();
        while ($current->lte($selesai)) {
            $tanggals[] = $current->copy();
            $current->addDay();
        }

        // Ambil semua karyawan aktif
        $karyawan = User::where('is_active', 1)->orderBy('name')->get();

        // Ambil semua absensi dalam periode
        $semuaAbsensi = Absensi::whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
            ->get()
            ->groupBy('user_id');

        $rows = [];

        // Row 1: Header info
        $rows[] = ['Kantor', 'Office'];
        $rows[] = ['Periode Absensi', $mulai->format('Y-m-d') . ' s/d ' . $selesai->format('Y-m-d')];
        $rows[] = [];

        // Row 4: Header kolom
        $headerRow = ['Nama Karyawan', 'ID Karyawan', 'Jabatan Karyawan', 'Periode Absen Karyawan'];
        foreach ($tanggals as $tgl) {
            $headerRow[] = $tgl->format('d');
        }
        $headerRow[] = 'Total';
        $headerRow[] = 'A';
        $headerRow[] = 'S';
        $headerRow[] = 'I';
        $rows[] = $headerRow;

        // Data karyawan
        foreach ($karyawan as $k) {
            $absensiKaryawan = $semuaAbsensi[$k->id] ?? collect();
            $absensiByTanggal = $absensiKaryawan->keyBy(fn($a) => Carbon::parse($a->tanggal)->format('Y-m-d'));

            $row = [
                $k->name,
                $k->employee_id ?? $k->id,
                $k->role->name ?? '-',
                $mulai->format('d M') . ' - ' . $selesai->format('d M Y'),
            ];

            $totalHadir = 0;
            $totalAlfa  = 0;
            $totalSakit = 0;
            $totalIzin  = 0;

            foreach ($tanggals as $tgl) {
                $key    = $tgl->format('Y-m-d');
                $absen  = $absensiByTanggal[$key] ?? null;

                if ($tgl->isWeekend()) {
                    $row[] = 'L';
                } elseif ($absen) {
                    $status = strtoupper(substr($absen->status, 0, 1));
                    if ($absen->status == 'hadir' || $absen->status == 'terlambat') {
                        $status = 'H';
                        $totalHadir++;
                    } elseif ($absen->status == 'alfa') {
                        $status = 'A';
                        $totalAlfa++;
                    } elseif ($absen->status == 'sakit') {
                        $status = 'S';
                        $totalSakit++;
                    } elseif ($absen->status == 'izin') {
                        $status = 'I';
                        $totalIzin++;
                    }
                    $row[] = $status;
                } else {
                    $row[] = 'A';
                    $totalAlfa++;
                }
            }

            $row[] = $totalHadir;
            $row[] = $totalAlfa;
            $row[] = $totalSakit;
            $row[] = $totalIzin;

            $rows[] = $row;
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = $sheet->getHighestColumn();
                $lastRow = $sheet->getHighestRow();

                // Style header info (row 1-2)
                $sheet->getStyle('A1:B2')->getFont()->setBold(true);

                // Style header kolom (row 4)
                $sheet->getStyle('A4:' . $lastCol . '4')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Style data rows
                $sheet->getStyle('A5:' . $lastCol . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Nama & jabatan align left
                $sheet->getStyle('A5:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Auto width kolom
                foreach (range('A', $lastCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Warnai H = hijau, A = merah, L = abu
                for ($row = 5; $row <= $lastRow; $row++) {
                    for ($col = 'E'; $col != $lastCol; $col++) {
                        $cell = $sheet->getCell($col . $row);
                        $val  = $cell->getValue();
                        if ($val == 'H') {
                            $sheet->getStyle($col . $row)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('D1FAE5');
                        } elseif ($val == 'A') {
                            $sheet->getStyle($col . $row)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('FEE2E2');
                        } elseif ($val == 'L') {
                            $sheet->getStyle($col . $row)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('F3F4F6');
                        } elseif ($val == 'S') {
                            $sheet->getStyle($col . $row)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('DBEAFE');
                        } elseif ($val == 'I') {
                            $sheet->getStyle($col . $row)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('FEF3C7');
                        }
                    }
                }
            },
        ];
    }
}