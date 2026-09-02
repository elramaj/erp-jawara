@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Edit Aset</h1>
    <p class="text-sm text-gray-400">{{ $aset->kode_aset }} — {{ $aset->nama_aset }}</p>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('aset.update', $aset) }}">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Aset *</label>
                <input type="text" name="kode_aset" value="{{ old('kode_aset', $aset->kode_aset) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    required>
                @error('kode_aset')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="kategori_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 pr-8">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategori as $k)
                    <option value="{{ $k->id }}" {{ old('kategori_id', $aset->kategori_id) == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Aset *</label>
                <input type="text" name="nama_aset" value="{{ old('nama_aset', $aset->nama_aset) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    required>
                @error('nama_aset')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Merk</label>
                <input type="text" name="merk" value="{{ old('merk', $aset->merk) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Model / Tipe</label>
                <input type="text" name="model" value="{{ old('model', $aset->model) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
                <input type="text" name="serial_number" value="{{ old('serial_number', $aset->serial_number) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Spesifikasi</label>
                <textarea name="spesifikasi" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('spesifikasi', $aset->spesifikasi) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Beli</label>
                <input type="date" name="tanggal_beli" value="{{ old('tanggal_beli', $aset->tanggal_beli?->format('Y-m-d')) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli (Rp)</label>
                <input type="number" name="harga_beli" value="{{ old('harga_beli', $aset->harga_beli) }}" min="0" step="1000"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi *</label>
                <select name="kondisi" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 pr-8" required>
                    <option value="baik" {{ old('kondisi', $aset->kondisi) == 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak_ringan" {{ old('kondisi', $aset->kondisi) == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="rusak_berat" {{ old('kondisi', $aset->kondisi) == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                    <option value="hilang" {{ old('kondisi', $aset->kondisi) == 'hilang' ? 'selected' : '' }}>Hilang</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Buat ganti siapa yang pegang aset ini, pakai tombol "Pindah Tangan" di halaman detail, bukan di sini.</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea name="catatan" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('catatan', $aset->catatan) }}</textarea>
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('aset.show', $aset) }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-semibold transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection