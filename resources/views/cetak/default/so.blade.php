@extends($layoutCetak)
@section('judul', 'Sales Order')
@section('nomor', $so->no_so)
@section('isi')
@php $c = $so->customer; @endphp
<table class="meta">
    <tr>
        <td class="k">Pelanggan</td><td class="t">:</td><td><strong>{{ $c->nama ?? '-' }}</strong></td>
        <td class="k">Tanggal</td><td class="t">:</td><td>{{ $so->tanggal->format('d M Y') }}</td>
    </tr>
    <tr>
        <td class="k">Alamat</td><td class="t">:</td><td>{{ $c->alamat ?? ($c->alamat1 ?? '-') }}{{ !empty($c->kota) ? ', ' . $c->kota : '' }}</td>
        <td class="k">Proyek</td><td class="t">:</td><td>{{ $so->proyek->nama_proyek ?? '-' }}</td>
    </tr>
    <tr>
        <td class="k">Telepon / PIC</td><td class="t">:</td><td>{{ $c->telepon ?? ($c->phone1 ?? '-') }} {{ !empty($c->pic) ? '/ ' . $c->pic : '' }}</td>
        <td class="k">Dibuat oleh</td><td class="t">:</td><td>{{ $so->creator->name ?? '-' }}</td>
    </tr>
</table>

<table class="data">
    <thead>
        <tr><th style="width:30px">No</th><th>Nama Barang</th><th style="width:70px">Qty</th><th style="width:110px">Harga Satuan</th><th style="width:120px">Jumlah</th></tr>
    </thead>
    <tbody>
        @foreach($so->detail as $i => $d)
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
            <td class="r"><strong>{{ number_format($so->total, 0, ',', '.') }}</strong></td>
        </tr>
    </tbody>
</table>
<div class="terbilang">Terbilang: {{ ucfirst(\App\Support\Terbilang::rupiah($so->total)) }}</div>

@if($so->catatan)
<div class="catatan"><strong>Catatan:</strong> {{ $so->catatan }}</div>
@endif

@include('cetak._ttd', ['kolom' => [['Pelanggan', ''], ['Sales', $so->creator->name ?? ''], ['Mengetahui', '']]])
@endsection
