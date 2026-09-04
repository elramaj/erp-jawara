@extends('layouts.app')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><svg class="w-6 h-6 inline-block -mt-1 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" /></svg>Edit PT</h1>
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
                    <p class="text-sm font-semibold text-gray-600 mb-2"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>Lokasi GPS (untuk absensi)</p>
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
                        <svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.752a1.125 1.125 0 0 0-1.006 0L3.622 6.189C3.24 6.38 3 6.77 3 7.195v10.486c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" /></svg>Lihat di Google Maps
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
        <h2 class="font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>Karyawan di PT ini ({{ $company->users->count() }})</h2>
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