@extends('layouts.print')
@section('judul', 'Purchase Order')
@section('nomor', $po->no_po)
@section('isi')
@php $s = $po->supplier; @endphp
<table class="meta">
    <tr>
        <td class="k">Kepada (Supplier)</td><td class="t">:</td><td><strong>{{ $s->nama ?? '-' }}</strong></td>
        <td class="k">Tanggal</td><td class="t">:</td><td>{{ $po->tanggal->format('d M Y') }}</td>
    </tr>
    <tr>
        <td class="k">Alamat</td><td class="t">:</td><td>{{ $s->alamat ?? ($s->alamat1 ?? '-') }}{{ !empty($s->kota) ? ', ' . $s->kota : '' }}</td>
        <td class="k">Proyek</td><td class="t">:</td><td>{{ $po->proyek->nama_proyek ?? '-' }}</td>
    </tr>
    <tr>
        <td class="k">Telepon / PIC</td><td class="t">:</td><td>{{ $s->telepon ?? ($s->phone1 ?? '-') }} {{ !empty($s->pic) ? '/ ' . $s->pic : '' }}</td>
        <td class="k">Dibuat oleh</td><td class="t">:</td><td>{{ $po->creator->name ?? '-' }}</td>
    </tr>
</table>

<table class="data">
    <thead>
        <tr><th style="width:30px">No</th><th>Nama Barang</th><th style="width:70px">Qty</th><th style="width:110px">Harga Satuan</th><th style="width:120px">Jumlah</th></tr>
    </thead>
    <tbody>
        @foreach($po->detail as $i => $d)
        <tr>
            <td class="c">{{ $i + 1 }}</td>
            <td>{{ $d->barang->nama_barang ?? '-' }}</td>
            <td class="c">{{ $d->jumlah }} {{ $d->barang->satuan ?? '' }}</td>
            <td class="r">{{ number_format($d->harga, 0, ',', '.') }}</td>
            <td class="r">{{ number_format($d->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="4" class="r"><strong>TOTAL (Rp)</strong></td>
            <td class="r"><strong>{{ number_format($po->total, 0, ',', '.') }}</strong></td>
        </tr>
    </tbody>
</table>
<div class="terbilang">Terbilang: {{ ucfirst(\App\Support\Terbilang::rupiah($po->total)) }}</div>

@if($po->catatan)
<div class="catatan"><strong>Catatan:</strong> {{ $po->catatan }}</div>
@endif

@include('cetak._ttd', ['kolom' => [['Dibuat oleh', $po->creator->name ?? ''], ['Disetujui', ''], ['Supplier', '']]])
@endsection
