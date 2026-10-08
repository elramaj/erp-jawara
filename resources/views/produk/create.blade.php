@extends('layouts.app')

@section('content')
@php
    $barisAwal = old('item')
        ? array_values(old('item'))
        : [['nama_kebutuhan' => '', 'spesifikasi' => '', 'jumlah' => 1, 'satuan' => '']];
@endphp
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Permintaan Produk Baru</h1>
    <p class="text-sm text-gray-500 mt-1">Masukkan kebutuhan dan spesifikasi dari customer. Tim Produk akan mencarikan barangnya.</p>
</div>

@if($errors->any())
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300 text-sm">
    <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('produk.store') }}" class="space-y-6 max-w-4xl"
      x-data='{ rows: @json($barisAwal) }'>
    @csrf

    <div class="bg-white rounded-xl shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Permintaan *</label>
            <input type="text" name="judul" value="{{ old('judul') }}" required placeholder="Mis. Pengadaan laptop dinas — Dinas X"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Proyek</label>
            <select name="proyek_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm pr-8">
                <option value="">— Belum ada proyek —</option>
                @foreach($proyek as $p)
                <option value="{{ $p->id }}" {{ old('proyek_id') == $p->id ? 'selected' : '' }}>{{ $p->kode_proyek }} — {{ $p->nama_proyek }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
            <select name="customer_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm pr-8">
                <option value="">— Pilih customer —</option>
                @foreach($customers as $c)
                <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sumber / Jenis Pengadaan</label>
            <input type="text" name="sumber" list="daftar-sumber" value="{{ old('sumber') }}" placeholder="Mis. e-catalog, tender, penunjukan langsung"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <datalist id="daftar-sumber"><option value="E-Catalog"><option value="Tender"><option value="Penunjukan Langsung"><option value="Pengadaan Langsung"><option value="Swasta"></datalist>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Opsi dibutuhkan paling lambat</label>
            <input type="date" name="target_tanggal" min="{{ date('Y-m-d') }}" value="{{ old('target_tanggal') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan untuk tim Produk</label>
            <textarea name="catatan" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('catatan') }}</textarea>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-3">
            <h2 class="font-semibold text-gray-800">Daftar Kebutuhan</h2>
            <button type="button" @click="rows.push({nama_kebutuhan:'', spesifikasi:'', jumlah:1, satuan:''})"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">+ Tambah Item</button>
        </div>
        <div class="space-y-4">
            <template x-for="(row, i) in rows" :key="i">
                <div class="border rounded-lg p-4 grid grid-cols-1 md:grid-cols-6 gap-3">
                    <div class="md:col-span-3">
                        <label class="block text-xs text-gray-500 mb-1">Nama kebutuhan *</label>
                        <input type="text" required x-model="row.nama_kebutuhan" :name="'item[' + i + '][nama_kebutuhan]'"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Jumlah *</label>
                        <input type="number" min="1" required x-model="row.jumlah" :name="'item[' + i + '][jumlah]'"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Satuan</label>
                        <input type="text" x-model="row.satuan" :name="'item[' + i + '][satuan]'" placeholder="unit / pcs"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div class="flex items-end">
                        <button type="button" x-show="rows.length > 1" @click="rows.splice(i, 1)" class="text-red-500 hover:text-red-700 text-xs font-semibold">Hapus</button>
                    </div>
                    <div class="md:col-span-6">
                        <label class="block text-xs text-gray-500 mb-1">Spesifikasi dari customer</label>
                        <textarea rows="3" x-model="row.spesifikasi" :name="'item[' + i + '][spesifikasi]'" placeholder="Tempel spesifikasi yang diminta (prosesor, RAM, ukuran, TKDN, dll)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div class="flex gap-3">
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition">Kirim ke Tim Produk</button>
        <a href="{{ route('produk.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
    </div>
</form>
@endsection
