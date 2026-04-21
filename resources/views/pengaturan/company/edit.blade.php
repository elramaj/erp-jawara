@extends('layouts.app')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">✏️ Edit PT</h1>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Form Edit PT --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4">Info PT</h2>
        <form method="POST" action="{{ route('company.update', $company) }}">
            @csrf @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode PT *</label>
                    <input type="text" name="kode" value="{{ old('kode', $company->kode) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama PT *</label>
                    <input type="text" name="nama" value="{{ old('nama', $company->nama) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                    <input type="text" name="telepon" value="{{ old('telepon', $company->telepon) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $company->email) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('alamat', $company->alamat) }}</textarea>
                </div>

                {{-- Lokasi GPS --}}
                <div class="border-t pt-3 mt-3">
                    <p class="text-sm font-semibold text-gray-600 mb-2">📍 Lokasi GPS (untuk absensi)</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Latitude</label>
                            <input type="text" name="latitude" value="{{ old('latitude', $company->latitude) }}"
                                placeholder="-7.2575"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Longitude</label>
                            <input type="text" name="longitude" value="{{ old('longitude', $company->longitude) }}"
                                placeholder="112.7521"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Radius (meter)</label>
                        <input type="number" name="radius_meter" value="{{ old('radius_meter', $company->radius_meter ?? 100) }}"
                            min="10" max="5000"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    @if($company->latitude && $company->longitude)
                    <a href="https://www.google.com/maps?q={{ $company->latitude }},{{ $company->longitude }}"
                        target="_blank"
                        class="inline-flex items-center gap-1 mt-2 text-xs text-indigo-600 hover:underline">
                        🗺️ Lihat di Google Maps
                    </a>
                    @endif
                </div>

                {{-- Status Aktif --}}
                <div class="border-t pt-3">
                    {{-- Hidden input sebagai fallback kalau checkbox tidak dicentang --}}
                    <input type="hidden" name="is_active" value="0">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $company->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 rounded">
                        <span class="text-sm font-medium text-gray-700">PT Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 mt-5">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition">
                    Update
                </button>
                <a href="{{ route('pengaturan.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-semibold transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Daftar Karyawan di PT ini --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4">👥 Karyawan di PT ini ({{ $company->users->count() }})</h2>
        <div class="space-y-2 max-h-80 overflow-y-auto">
            @forelse($company->users as $u)
            <div class="flex items-center gap-3 p-2 border border-gray-100 rounded-lg">
                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr($u->name, 0, 1)) }}
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700">{{ $u->name }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ $u->role->name ?? '-' }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Belum ada karyawan di PT ini.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection