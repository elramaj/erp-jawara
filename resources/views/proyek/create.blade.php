@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">➕ Tambah Proyek Baru</h1>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-3xl">
    <form method="POST" action="{{ route('proyek.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Proyek *</label>
                <input type="text" name="kode_proyek" value="{{ old('kode_proyek') }}"
                    placeholder="PRJ-2026-001"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    required>
                @error('kode_proyek')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select name="status"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ old('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Proyek *</label>
                <input type="text" name="nama_proyek" value="{{ old('nama_proyek') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    required>
                @error('nama_proyek')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Klien / Instansi *</label>
                <input type="text" name="klien" value="{{ old('klien') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Kontrak (Rp)</label>
                <input type="number" name="nilai_kontrak" value="{{ old('nilai_kontrak') }}"
                    placeholder="0"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                <input type="date" name="deadline" value="{{ old('deadline') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('deskripsi') }}</textarea>
            </div>

            {{-- Pilih Anggota Tim --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">👥 Anggota Tim</label>
                <div class="border border-gray-200 rounded-lg p-3 max-h-48 overflow-y-auto space-y-2">
                    @foreach($karyawan as $k)
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="anggota[]" value="{{ $k->id }}"
                            id="anggota_{{ $k->id }}"
                            {{ in_array($k->id, old('anggota', [])) ? 'checked' : '' }}>
                        <label for="anggota_{{ $k->id }}" class="text-sm text-gray-700 flex-1">
                            {{ $k->name }}
                            <span class="text-xs text-gray-400 capitalize">({{ $k->role->name ?? '-' }})</span>
                        </label>
                        <input type="text" name="peran[{{ $k->id }}]"
                            placeholder="Peran (opsional)"
                            class="border border-gray-200 rounded px-2 py-1 text-xs w-36 focus:outline-none focus:ring-1 focus:ring-indigo-400">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition">
                Simpan Proyek
            </button>
            <a href="{{ route('proyek.index') }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-semibold transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection