<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Dokumen') {{ trim($__env->yieldContent('nomor')) }}</title>
    <style>
        /* ======= TEMPLATE CETAK "default" (dipakai semua PT yang belum memilih template) =======
           Salin folder ini (resources/views/cetak/default) ke nama baru untuk membuat template PT lain. */
        @page { size: A4; margin: 14mm 14mm 16mm 14mm; }
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #111; margin: 0; background: #e5e7eb; }
        .toolbar { position: sticky; top: 0; background: #1f2937; color: #fff; padding: 10px 16px; display: flex; gap: 10px; align-items: center; z-index: 5; }
        .toolbar button, .toolbar a { background: #dc2626; color: #fff; border: 0; border-radius: 6px; padding: 7px 16px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .toolbar a.sekunder { background: #4b5563; }
        .kertas { width: 210mm; min-height: 297mm; margin: 14px auto; background: #fff; padding: 14mm; box-shadow: 0 2px 10px rgba(0,0,0,.2); }
        .kop { display: block; text-align: center; border-bottom: 2px solid #1d4ed8; padding-bottom: 8px; margin-bottom: 12px; }
        .kop > div { margin-top: 4px; }
        .kop img { height: 46px; width: auto; }
        .kop .nama { font-size: 16px; font-weight: 700; text-transform: uppercase; color: #1d4ed8; }
        .kop .info { font-size: 10.5px; color: #333; line-height: 1.4; }
        h1.judul { text-align: center; font-size: 16px; letter-spacing: 1px; margin: 6px 0 2px; text-transform: uppercase; }
        .nomor { text-align: center; font-size: 12px; margin-bottom: 12px; }
        table.meta { width: 100%; margin-bottom: 10px; border-collapse: collapse; }
        table.meta td { padding: 2px 4px; vertical-align: top; }
        table.meta td.k { width: 120px; color: #444; }
        table.meta td.t { width: 10px; }
        table.data { width: 100%; border-collapse: collapse; margin: 8px 0; }
        table.data th, table.data td { border: 1px solid #111; padding: 5px 6px; }
        table.data th { background: #dbeafe; text-transform: uppercase; font-size: 10.5px; }
        .c { text-align: center; } .r { text-align: right; }
        .kotak { border: 1px solid #111; padding: 6px 8px; margin: 8px 0; min-height: 44px; white-space: pre-line; }
        .garis { border-bottom: 1px dotted #111; height: 22px; }
        .terbilang { margin: 6px 0; font-style: italic; }
        .ttd { width: 100%; margin-top: 26px; border-collapse: collapse; page-break-inside: avoid; }
        .ttd td { text-align: center; vertical-align: top; width: 33.33%; padding: 0 6px; }
        .ttd .ruang { height: 62px; }
        .ttd .nama { border-top: 1px solid #111; display: inline-block; min-width: 130px; padding-top: 3px; }
        .catatan { margin-top: 10px; font-size: 10.5px; color: #333; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .kertas { margin: 0; box-shadow: none; width: auto; min-height: 0; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="window.print()">Cetak / Simpan PDF</button>
        <a href="javascript:history.length > 1 ? history.back() : window.close()" class="sekunder">Kembali</a>
        <span style="font-size:12px;opacity:.7;">Pratinjau — pada dialog cetak pilih A4, matikan "Headers and footers".</span>
    </div>

    <div class="kertas">
        {{-- KOP SURAT --}}
        <div class="kop">
            <img src="{{ $perusahaan->logo_url ?? asset('images/logo.png') }}" alt="" onerror="this.style.display='none'">
            <div>
                <div class="nama">{{ $perusahaan->nama ?? config('app.name') }}</div>
                <div class="info">
                    {{ $perusahaan->alamat ?? '' }}<br>
                    @if(!empty($perusahaan->telepon))Telp. {{ $perusahaan->telepon }}@endif
                    @if(!empty($perusahaan->email)) &nbsp;|&nbsp; {{ $perusahaan->email }}@endif
                </div>
            </div>
        </div>

        <h1 class="judul">@yield('judul')</h1>
        <div class="nomor">No. @yield('nomor')</div>

        @yield('isi')
    </div>
</body>
</html>
