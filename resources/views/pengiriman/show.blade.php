@extends('layouts.app')

@section('content')
@php
    $p = $pengiriman->proyek;
    $s = $pengiriman->status;
@endphp

<div class="flex justify-between items-start mb-6">
    <div>
        <a href="{{ route('pengiriman.index') }}" class="text-xs text-gray-400 hover:text-gray-600">&larr; Kembali ke Pengiriman</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ $pengiriman->no_pengiriman }}</h1>
        <p class="text-sm text-gray-500 mt-1">
            <a href="{{ route('proyek.show', $p) }}" class="hover:underline">{{ $p->kode_proyek }} — {{ $p->nama_proyek }}</a> · {{ $p->klien }}
        </p>
    </div>
    <div class="flex items-center gap-3">
        @include('pengiriman._badge', ['status' => $s])
        <a href="{{ route('pengiriman.cetak', $pengiriman) }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5 w-fit"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.056 48.056 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" /></svg> Cetak DO</a>
    </div>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300 text-sm">
    <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

@if($s === 'revisi')
<div class="bg-orange-50 border border-orange-300 text-orange-800 rounded-xl p-4 mb-4">
    <p class="font-semibold">Jadwal diminta diubah (Sales/Gudang)</p>
    <p class="text-sm mt-1">{{ $pengiriman->alasan_revisi }}</p>
</div>
@endif
@if($s === 'dibatalkan')
<div class="bg-gray-100 border border-gray-300 text-gray-700 rounded-xl p-4 mb-4">
    <p class="font-semibold">Jadwal dibatalkan</p>
    <p class="text-sm mt-1">{{ $pengiriman->alasan_batal }}</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Detail Jadwal</h2>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div><dt class="text-xs text-gray-400">Tanggal Kirim</dt><dd class="font-medium text-gray-800">{{ $pengiriman->tanggal_kirim->format('d M Y') }}{{ $pengiriman->jam ? ' · ' . $pengiriman->jam : '' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Deadline Proyek</dt><dd class="font-medium text-gray-800">{{ $p->deadline ? $p->deadline->format('d M Y') : '-' }}</dd></div>
                <div class="md:col-span-2"><dt class="text-xs text-gray-400">Alamat Tujuan</dt><dd class="font-medium text-gray-800 whitespace-pre-line">{{ $pengiriman->alamat_tujuan }}</dd></div>
                <div><dt class="text-xs text-gray-400">Penerima</dt><dd class="font-medium text-gray-800">{{ $pengiriman->penerima_nama ?: '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Kontak Penerima</dt><dd class="font-medium text-gray-800">{{ $pengiriman->penerima_kontak ?: '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Kendaraan</dt><dd class="font-medium text-gray-800">{{ $pengiriman->kendaraan ?: '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Driver / Kurir</dt><dd class="font-medium text-gray-800">{{ $pengiriman->driver ?: '-' }}</dd></div>
                @if($pengiriman->catatan)
                <div class="md:col-span-2"><dt class="text-xs text-gray-400">Catatan</dt><dd class="text-gray-800 whitespace-pre-line">{{ $pengiriman->catatan }}</dd></div>
                @endif
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Riwayat</h2>
            <ul class="text-sm space-y-2">
                <li>Diajukan oleh <strong>{{ $pengiriman->creator->name ?? '-' }}</strong> · {{ $pengiriman->created_at->format('d M Y H:i') }}</li>
                @if($pengiriman->dikonfirmasi_at)
                <li>Dikonfirmasi oleh <strong>{{ $pengiriman->konfirmator->name ?? '-' }}</strong> · {{ $pengiriman->dikonfirmasi_at->format('d M Y H:i') }}</li>
                @endif
                @if($pengiriman->dikirim_at)
                <li>Barang berangkat · {{ $pengiriman->dikirim_at->format('d M Y H:i') }}</li>
                @endif
                @if($pengiriman->diterima_at)
                <li>Diterima customer · {{ $pengiriman->diterima_at->format('d M Y H:i') }}</li>
                @endif
            </ul>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Aksi --}}
        <div class="bg-white rounded-xl shadow p-5 space-y-3">
            <h3 class="font-semibold text-gray-800">Aksi</h3>

            @if($s === 'diusulkan' && $bisaKonfirmasi)
            <form method="POST" action="{{ route('pengiriman.konfirmasi', $pengiriman) }}">
                @csrf
                <button class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Konfirmasi Jadwal</button>
            </form>
            <form method="POST" action="{{ route('pengiriman.revisi', $pengiriman) }}" class="space-y-2">
                @csrf
                <textarea name="alasan_revisi" rows="2" required placeholder="Alasan / usulan perubahan jadwal..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"></textarea>
                <button class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Minta Ubah Jadwal</button>
            </form>
            @elseif($s === 'diusulkan')
            <p class="text-sm text-gray-500">Menunggu konfirmasi dari Sales pemilik paket atau tim Gudang.</p>
            @endif

            @if($kelola)
                @if(in_array($s, ['diusulkan', 'revisi', 'terjadwal']))
                <a href="{{ route('pengiriman.edit', $pengiriman) }}" class="block text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">Ubah Jadwal</a>
                @endif

                @if($s === 'terjadwal')
                <form method="POST" action="{{ route('pengiriman.kirim', $pengiriman) }}" onsubmit="return confirm('Tandai barang sudah berangkat?')">
                    @csrf
                    <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Barang Berangkat</button>
                </form>
                @endif

                @if($s === 'dikirim')
                <form method="POST" action="{{ route('pengiriman.terima', $pengiriman) }}" onsubmit="return confirm('Tandai barang sudah diterima customer?')">
                    @csrf
                    <button class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Sudah Diterima Customer</button>
                </form>
                @endif

                @if(in_array($s, ['diusulkan', 'revisi', 'terjadwal']))
                <form method="POST" action="{{ route('pengiriman.batalkan', $pengiriman) }}" class="space-y-2 pt-2 border-t" onsubmit="return confirm('Batalkan jadwal ini?')">
                    @csrf
                    <input type="text" name="alasan_batal" required placeholder="Alasan pembatalan"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
                    <button class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-semibold transition">Batalkan Jadwal</button>
                </form>
                @endif
            @endif

            @if(in_array($s, ['diterima', 'dibatalkan']) || (!$kelola && !($s === 'diusulkan' && $bisaKonfirmasi) && $s !== 'diusulkan'))
            <p class="text-sm text-gray-400">Tidak ada aksi tersedia.</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Kelengkapan Barang</h3>
            @include('pengiriman._barang', ['proyek' => $p, 'kel' => $kel])
            <p class="text-xs text-gray-500 mt-3">Sales pemilik paket: {{ $p->salesAnggota()->pluck('name')->join(', ') ?: '-' }}</p>
        </div>
    </div>
</div>
@endsection
