@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">📝 Ajukan Izin / Cuti</h1>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('izin.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis *</label>
                <select name="jenis"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="izin" {{ old('jenis') == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ old('jenis') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="cuti" {{ old('jenis') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="dinas_luar" {{ old('jenis') == 'dinas_luar' ? 'selected' : '' }}>Dinas Luar</option>
                </select>
                @error('jenis')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai *</label>
                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    required>
                @error('tanggal_mulai')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai *</label>
                <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    required>
                @error('tanggal_selesai')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan *</label>
                <textarea name="alasan" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    required>{{ old('alasan') }}</textarea>
                @error('alasan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition">
                Kirim Pengajuan
            </button>
            <a href="{{ route('izin.index') }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-semibold transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection