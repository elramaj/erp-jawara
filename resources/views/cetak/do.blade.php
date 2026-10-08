@extends('layouts.print')
@section('judul', 'Delivery Order')
@section('nomor', $pengiriman->no_pengiriman)
@section('isi')
@php $p = $pengiriman->proyek; @endphp
<table class="meta">
    <tr>
        <td class="k">Kepada</td><td class="t">:</td><td><strong>{{ $pengiriman->penerima_nama ?: ($p->klien ?? '-') }}</strong></td>
        <td class="k">Tanggal Kirim</td><td class="t">:</td><td>{{ $pengiriman->tanggal_kirim->format('d M Y') }}{{ $pengiriman->jam ? ' · ' . $pengiriman->jam : '' }}</td>
    </tr>
    <tr>
        <td class="k">Alamat Tujuan</td><td class="t">:</td><td>{{ $pengiriman->alamat_tujuan }}</td>
        <td class="k">Proyek</td><td class="t">:</td><td>{{ $p->kode_proyek }} — {{ $p->nama_proyek }}</td>
    </tr>
    <tr>
        <td class="k">Kontak Penerima</td><td class="t">:</td><td>{{ $pengiriman->penerima_kontak ?: '-' }}</td>
        <td class="k">Ref. SO</td><td class="t">:</td><td>{{ $daftarSo->pluck('no_so')->join(', ') ?: '-' }}</td>
    </tr>
    <tr>
        <td class="k">Kendaraan</td><td class="t">:</td><td>{{ $pengiriman->kendaraan ?: '-' }}</td>
        <td class="k">Driver / Kurir</td><td class="t">:</td><td>{{ $pengiriman->driver ?: '-' }}</td>
    </tr>
</table>

<table class="data">
    <thead>
        <tr><th style="width:30px">No</th><th>Nama Barang</th><th style="width:90px">Qty</th><th style="width:160px">Keterangan</th></tr>
    </thead>
    <tbody>
        @forelse($barang as $i => $b)
        <tr>
            <td class="c">{{ $i + 1 }}</td>
            <td>{{ $b['nama'] }}</td>
            <td class="c">{{ $b['jumlah'] }} {{ $b['satuan'] }}</td>
            <td></td>
        </tr>
        @empty
        @for($i = 1; $i <= 6; $i++)
        <tr><td class="c">{{ $i }}</td><td>&nbsp;</td><td></td><td></td></tr>
        @endfor
        @endforelse
    </tbody>
</table>

@if($pengiriman->catatan)
<div class="catatan"><strong>Catatan:</strong> {{ $pengiriman->catatan }}</div>
@endif
<div class="catatan">Barang telah diterima dalam keadaan baik dan lengkap sesuai daftar di atas.</div>

@include('cetak._ttd', ['kolom' => [['Dikirim oleh (Gudang)', ''], ['Driver / Kurir', $pengiriman->driver ?? ''], ['Diterima oleh', $pengiriman->penerima_nama ?? '']]])
@endsection
