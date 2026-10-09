@extends($layoutCetak)
@section('judul', 'Tanda Terima Barang Service')
@section('nomor', $komplain->no_servisan ?: $komplain->no_komplain)
@section('isi')
<table class="meta">
    <tr>
        <td class="k">No. Komplain</td><td class="t">:</td><td>{{ $komplain->no_komplain }}</td>
        <td class="k">Tanggal Terima</td><td class="t">:</td><td>{{ ($komplain->tanggal_terima_service ?? now())->format('d M Y') }}</td>
    </tr>
    <tr>
        <td class="k">Proyek / Pelanggan</td><td class="t">:</td><td>{{ $komplain->proyek->nama_proyek ?? '-' }}{{ !empty($komplain->proyek->klien) ? ' — ' . $komplain->proyek->klien : '' }}</td>
        <td class="k">Status Garansi</td><td class="t">:</td><td>{{ $komplain->masih_garansi ? 'Masih garansi' : 'Di luar garansi' }}</td>
    </tr>
    <tr>
        <td class="k">Judul Keluhan</td><td class="t">:</td><td colspan="4">{{ $komplain->judul }}</td>
    </tr>
</table>

<table class="data">
    <thead>
        <tr><th style="width:30px">No</th><th>Nama / Tipe Barang</th><th style="width:150px">Serial Number</th><th style="width:60px">Qty</th></tr>
    </thead>
    <tbody>
        @forelse($komplain->barangService as $i => $b)
        <tr><td class="c">{{ $i + 1 }}</td><td>{{ $b->nama_barang }}</td><td>{{ $b->serial_number }}</td><td class="c">{{ $b->qty }}</td></tr>
        @empty
        @for($i = 1; $i <= 3; $i++)
        <tr><td class="c">{{ $i }}</td><td>&nbsp;</td><td></td><td></td></tr>
        @endfor
        @endforelse
    </tbody>
</table>

<strong>Keluhan / Kerusakan:</strong>
<div class="kotak">{{ $komplain->deskripsi }}</div>

<strong>Kelengkapan yang diserahkan (adaptor, kabel, dus, dll):</strong>
<div class="kotak">@if($komplain->kelengkapan_service){{ $komplain->kelengkapan_service }}@else<div class="garis"></div><div class="garis"></div>@endif</div>

<strong>Kondisi fisik saat diterima:</strong>
<div class="kotak">@if($komplain->kondisi_fisik){{ $komplain->kondisi_fisik }}@else<div class="garis"></div>@endif</div>

<div class="catatan">
    Barang service yang tidak diambil lebih dari 30 hari setelah pemberitahuan selesai di luar tanggung jawab kami.
    Harap membawa dokumen ini saat pengambilan barang.
</div>

@include('cetak._ttd', ['kolom' => [['Penerima (Petugas)', $komplain->handler->name ?? ''], ['Yang Menyerahkan', $komplain->penyerah_nama ?? ''], ['Mengetahui', '']]])
@endsection
