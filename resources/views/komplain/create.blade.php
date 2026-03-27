@extends('layouts.app')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">➕ Buat Komplain Baru</h1>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('komplain.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Komplain *</label>
                <select name="jenis"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="barang" {{ old('jenis') == 'barang' ? 'selected' : '' }}>📦 Komplain Barang</option>
                    <option value="dokumen" {{ old('jenis') == 'dokumen' ? 'selected' : '' }}>📄 Komplain Dokumen</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas *</label>
                <select name="prioritas"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400" required>
                    <option value="low" {{ old('prioritas') == 'low' ? 'selected' : '' }}>🟢 Low</option>
                    <option value="medium" {{ old('prioritas', 'medium') == 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                    <option value="high" {{ old('prioritas') == 'high' ? 'selected' : '' }}>🟠 High</option>
                    <option value="critical" {{ old('prioritas') == 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Komplain *</label>
                <input type="text" name="judul" value="{{ old('judul') }}"
                    placeholder="Contoh: Monitor Dell 24 tidak menyala"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400" required>
                @error('judul')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Proyek Terkait</label>
                <select name="proyek_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
                    <option value="">-- Pilih Proyek (opsional) --</option>
                    @foreach($proyek as $p)
                    <option value="{{ $p->id }}" {{ old('proyek_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->kode_proyek }} - {{ $p->nama_proyek }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="4"
                    placeholder="Jelaskan detail masalah yang terjadi..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="masih_garansi" value="1" {{ old('masih_garansi') ? 'checked' : '' }}
                        class="w-4 h-4 text-red-500 rounded">
                    <span class="text-sm font-medium text-gray-700">🛡️ Barang masih dalam masa garansi</span>
                </label>
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button type="submit"
                class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg text-sm font-semibold transition">
                Kirim Komplain
            </button>
            <a href="{{ route('komplain.index') }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-semibold transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection