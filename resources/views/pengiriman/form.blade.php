@extends('layouts.app')

@section('content')
@php $edit = $pengiriman !== null; @endphp
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">{{ $edit ? 'Ubah Jadwal Pengiriman' : 'Atur Jadwal Pengiriman' }}</h1>
    <p class="text-sm text-gray-500 mt-1">{{ $proyek->kode_proyek }} — {{ $proyek->nama_proyek }} · {{ $proyek->klien }}</p>
</div>

@if($errors->any())
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300 text-sm">
    <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ $edit ? route('pengiriman.update', $pengiriman) : route('pengiriman.store', $proyek) }}">
            @csrf
            @if($edit) @method('PUT') @endif

            @if($edit && $pengiriman->status === 'revisi' && $pengiriman->alasan_revisi)
            <div class="bg-orange-50 border border-orange-300 text-orange-800 rounded-lg p-3 mb-4 text-sm">
                <p class="font-semibold">Permintaan revisi dari Sales:</p>
                <p>{{ $pengiriman->alasan_revisi }}</p>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kirim *</label>
                    <input type="date" name="tanggal_kirim" min="{{ date('Y-m-d') }}"
                        value="{{ old('tanggal_kirim', $edit ? $pengiriman->tanggal_kirim->format('Y-m-d') : '') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam (opsional)</label>
                    <input type="time" name="jam" value="{{ old('jam', $edit ? $pengiriman->jam : '') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Tujuan *</label>
                    <textarea name="alamat_tujuan" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>{{ old('alamat_tujuan', $edit ? $pengiriman->alamat_tujuan : '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima</label>
                    <input type="text" name="penerima_nama" value="{{ old('penerima_nama', $edit ? $pengiriman->penerima_nama : $proyek->klien) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Penerima</label>
                    <input type="text" name="penerima_kontak" value="{{ old('penerima_kontak', $edit ? $pengiriman->penerima_kontak : '') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kendaraan</label>
                    <input type="text" name="kendaraan" value="{{ old('kendaraan', $edit ? $pengiriman->kendaraan : '') }}" placeholder="Mis. L300 / Ekspedisi X"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Driver / Kurir</label>
                    <input type="text" name="driver" value="{{ old('driver', $edit ? $pengiriman->driver : '') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="catatan" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('catatan', $edit ? $pengiriman->catatan : '') }}</textarea>
                </div>
            </div>

            <p class="text-xs text-gray-500 mt-4">Setelah disimpan, jadwal diajukan ke Sales pemilik paket atau Gudang untuk dikonfirmasi.</p>

            <div class="flex gap-3 mt-4">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition">{{ $edit ? 'Simpan & Ajukan Ulang' : 'Simpan & Ajukan Konfirmasi' }}</button>
                <a href="{{ $edit ? route('pengiriman.show', $pengiriman) : route('pengiriman.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
            </div>
        </form>
    </div>

    {{-- Panel kelengkapan barang --}}
    <div class="bg-white rounded-xl shadow p-5 h-fit">
        <h3 class="font-semibold text-gray-800 mb-3">Kelengkapan Barang</h3>
        @include('pengiriman._barang', ['proyek' => $proyek, 'kel' => $kel])
        <p class="text-xs text-gray-500 mt-3">Sales pemilik paket: {{ $proyek->salesAnggota()->pluck('name')->join(', ') ?: 'belum ada anggota Sales di proyek ini' }}</p>
    </div>
</div>
@endsection
