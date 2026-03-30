<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; text-align: center; margin-bottom: 4px; }
        .subtitle { text-align: center; color: #666; font-size: 11px; margin-bottom: 20px; }
        .summary { display: flex; gap: 10px; margin-bottom: 20px; }
        .card { border: 1px solid #ddd; border-radius: 6px; padding: 10px 16px; flex: 1; }
        .card-label { font-size: 10px; color: #888; }
        .card-value { font-size: 16px; font-weight: bold; margin-top: 4px; }
        .green { color: #16a34a; }
        .red { color: #dc2626; }
        .indigo { color: #4f46e5; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead { background-color: #4f46e5; color: white; }
        th { padding: 8px 10px; text-align: left; font-size: 11px; }
        td { padding: 7px 10px; font-size: 11px; border-bottom: 1px solid #f0f0f0; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .section-title { font-size: 13px; font-weight: bold; margin: 16px 0 8px; border-left: 4px solid #4f46e5; padding-left: 8px; }
        .total-row { font-weight: bold; background-color: #eff6ff !important; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #888; }
    </style>
</head>
<body>
    <h1>LAPORAN KEUANGAN</h1>
    <p class="subtitle">
        Periode: {{ Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}<br>
        Dicetak: {{ now()->format('d M Y H:i') }}
    </p>

    {{-- Summary Cards --}}
    <table style="margin-bottom: 20px;">
        <tr>
            <td style="border: 1px solid #ddd; border-radius: 6px; padding: 10px 16px; width: 25%;">
                <div style="font-size: 10px; color: #888;">Total Pemasukan</div>
                <div style="font-size: 15px; font-weight: bold; color: #16a34a;">Rp {{ number_format($pemasukan, 0, ',', '.') }}</div>
            </td>
            <td style="width: 2%;"></td>
            <td style="border: 1px solid #ddd; border-radius: 6px; padding: 10px 16px; width: 25%;">
                <div style="font-size: 10px; color: #888;">Total Pengeluaran</div>
                <div style="font-size: 15px; font-weight: bold; color: #dc2626;">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</div>
            </td>
            <td style="width: 2%;"></td>
            <td style="border: 1px solid #ddd; border-radius: 6px; padding: 10px 16px; width: 25%;">
                <div style="font-size: 10px; color: #888;">{{ $labaRugi >= 0 ? 'Laba' : 'Rugi' }}</div>
                <div style="font-size: 15px; font-weight: bold; color: {{ $labaRugi >= 0 ? '#4f46e5' : '#ea580c' }};">Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}</div>
            </td>
            <td style="width: 2%;"></td>
            <td style="border: 1px solid #ddd; border-radius: 6px; padding: 10px 16px; width: 25%;">
                <div style="font-size: 10px; color: #888;">Piutang Belum Lunas</div>
                <div style="font-size: 15px; font-weight: bold; color: #d97706;">Rp {{ number_format($piutang, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    {{-- Rincian Pemasukan --}}
    <div class="section-title">💰 Rincian Pemasukan</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>No. Faktur</th>
                <th style="text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayatMasuk as $i => $r)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $r->tanggal->format('d M Y') }}</td>
                <td>{{ $r->fj->so->customer->nama ?? '-' }}</td>
                <td>{{ $r->fj->no_fj ?? '-' }}</td>
                <td style="text-align: right;">Rp {{ number_format($r->jumlah, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align: center; color: #999;">Tidak ada data</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">Total Pemasukan</td>
                <td style="text-align: right; color: #16a34a;">Rp {{ number_format($pemasukan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Rincian Pengeluaran --}}
    <div class="section-title">💸 Rincian Pengeluaran</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>No. Faktur</th>
                <th style="text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayatKeluar as $i => $r)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $r->tanggal->format('d M Y') }}</td>
                <td>{{ $r->fb->po->supplier->nama ?? '-' }}</td>
                <td>{{ $r->fb->no_fb ?? '-' }}</td>
                <td style="text-align: right;">Rp {{ number_format($r->jumlah, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align: center; color: #999;">Tidak ada data</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">Total Pengeluaran</td>
                <td style="text-align: right; color: #dc2626;">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        ERP Kantor &mdash; {{ config('app.name') }}
    </div>
</body>
</html>