@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        Tambah Aset Baru
    </h1>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('aset.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Aset *</label>
                <input type="text" name="kode_aset" value="{{ old('kode_aset') }}"
                    placeholder="AST-001"
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
                    <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Aset *</label>
                <input type="text" name="nama_aset" value="{{ old('nama_aset') }}"
                    placeholder="Contoh: Laptop Dell Latitude 5420"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    required>
                @error('nama_aset')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Merk</label>
                <input type="text" name="merk" value="{{ old('merk') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Model / Tipe</label>
                <input type="text" name="model" value="{{ old('model') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
                <input type="text" name="serial_number" value="{{ old('serial_number') }}"
                    placeholder="Nomor seri unik dari perangkat"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Spesifikasi</label>
                <textarea name="spesifikasi" rows="3"
                    placeholder="Contoh: Intel i5-1135G7, RAM 8GB, SSD 512GB"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('spesifikasi') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Beli</label>
                <input type="date" name="tanggal_beli" value="{{ old('tanggal_beli') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli (Rp)</label>
                <input type="number" name="harga_beli" value="{{ old('harga_beli') }}" min="0" step="1000"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi *</label>
                <select name="kondisi" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 pr-8" required>
                    <option value="baik" {{ old('kondisi', 'baik') == 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak_ringan" {{ old('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="rusak_berat" {{ old('kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dipegang Oleh</label>
                <select name="dipegang_oleh" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 pr-8">
                    <option value="">-- Belum dipegang (di gudang) --</option>
                    @foreach($karyawan as $kry)
                    <option value="{{ $kry->id }}" {{ old('dipegang_oleh') == $kry->id ? 'selected' : '' }}>{{ $kry->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea name="catatan" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('catatan') }}</textarea>
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition">
                Simpan Aset
            </button>
            <a href="{{ route('aset.index') }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-semibold transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection