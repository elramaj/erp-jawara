@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="text-xl font-bold text-gray-800"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>Absensi</h1>
    <p class="text-gray-500 text-sm mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg> {{ session('error') }}</div>
@endif

{{-- Shortcut Mobile --}}
<div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 mb-4 flex items-center justify-between">
    <div>
        <p class="text-sm font-semibold text-indigo-700"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>Absensi via HP</p>
        <p class="text-xs text-indigo-500 mt-0.5">Dengan GPS + Foto Selfie</p>
    </div>
    <a href="{{ route('absensi.mobile') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
        Buka
    </a>
</div>

{{-- Card Check-in / Check-out --}}
<div class="grid grid-cols-1 gap-4 mb-4" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">

    {{-- Status Hari Ini --}}
    <div class="bg-white rounded-xl shadow p-5">
        <h2 class="text-base font-semibold text-gray-700 mb-4">Status Hari Ini</h2>
        @if($absensiHariIni)
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Status</span>
                    <span class="font-semibold capitalize text-sm
                        {{ $absensiHariIni->status == 'hadir' ? 'text-green-600' : '' }}
                        {{ $absensiHariIni->status == 'terlambat' ? 'text-yellow-600' : '' }}
                        {{ $absensiHariIni->status == 'alfa' ? 'text-red-600' : '' }}">
                        {{ ucfirst($absensiHariIni->status) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Jam Masuk</span>
                    <span class="font-semibold text-indigo-600 text-sm">{{ $absensiHariIni->jam_masuk ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Jam Keluar</span>
                    <span class="font-semibold text-indigo-600 text-sm">{{ $absensiHariIni->jam_keluar ?? '-' }}</span>
                </div>
                @if($absensiHariIni->foto_masuk)
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 text-sm">Foto Masuk</span>
                    <img src="{{ Storage::url($absensiHariIni->foto_masuk) }}"
                        class="w-10 h-10 rounded-full object-cover border-2 border-indigo-300">
                </div>
                @endif
                @if($absensiHariIni->lokasi_valid === 1)
                <p class="text-xs text-green-600"><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>Lokasi: Dalam area kantor</p>
                @elseif($absensiHariIni->lokasi_valid === 0)
                <p class="text-xs text-orange-500"><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>Lokasi: Di luar area kantor</p>
                @endif
            </div>
        @else
            <p class="text-gray-400 text-sm">Belum absen hari ini.</p>
        @endif
    </div>

    {{-- Tombol Check-in / Check-out --}}
    <div class="bg-white rounded-xl shadow p-5 flex flex-col justify-center items-center gap-4">
        @if(!$absensiHariIni)
            <form method="POST" action="{{ route('absensi.checkin') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-xl text-base transition">
                    <svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>Check-in Sekarang
                </button>
            </form>
        @elseif(!$absensiHariIni->jam_keluar)
            <p class="text-green-600 font-semibold text-sm"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>Sudah Check-in jam {{ $absensiHariIni->jam_masuk }}</p>
            <form method="POST" action="{{ route('absensi.checkout') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-4 rounded-xl text-base transition">
                    <svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" /></svg>Check-out Sekarang
                </button>
            </form>
        @else
            <div class="text-center">
                <p class="text-green-600 font-semibold text-base"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>Absensi Selesai</p>
                <p class="text-gray-500 text-sm mt-1">{{ $absensiHariIni->jam_masuk }} — {{ $absensiHariIni->jam_keluar }}</p>
            </div>
        @endif
    </div>
</div>

{{-- Riwayat Absensi --}}
<div class="bg-white rounded-xl shadow p-5">
    <h2 class="text-base font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H5.25a2.25 2.25 0 0 1-2.25-2.25V5.25A2.25 2.25 0 0 1 5.25 3h7.5a2.25 2.25 0 0 1 2.25 2.25v13.5a2.25 2.25 0 0 1-2.25 2.25Zm0 0h3.75a2.25 2.25 0 0 0 2.25-2.25V9a2.25 2.25 0 0 0-2.25-2.25h-1.5" /></svg>Riwayat 7 Hari Terakhir</h2>

    {{-- Desktop --}}
    <div class="hidden md:block">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b text-xs uppercase">
                    <th class="pb-2">Tanggal</th>
                    <th class="pb-2">Jam Masuk</th>
                    <th class="pb-2">Jam Keluar</th>
                    <th class="pb-2">Lokasi</th>
                    <th class="pb-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 text-sm">{{ $item->tanggal->translatedFormat('d M Y') }}</td>
                    <td class="py-2 text-sm">{{ $item->jam_masuk ?? '-' }}</td>
                    <td class="py-2 text-sm">{{ $item->jam_keluar ?? '-' }}</td>
                    <td class="py-2 text-xs">
                        @if($item->lokasi_valid === 1)
                        <span class="text-green-600"><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>Dalam area</span>
                        @elseif($item->lokasi_valid === 0)
                        <span class="text-orange-500"><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>Luar area</span>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="py-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $item->status == 'hadir' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $item->status == 'terlambat' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $item->status == 'alfa' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $item->status == 'izin' ? 'bg-blue-100 text-blue-700' : '' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-4 text-center text-gray-400">Belum ada riwayat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile --}}
    <div class="md:hidden space-y-2">
        @forelse($riwayat as $item)
        <div class="border border-gray-100 rounded-lg p-3">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-700">{{ $item->tanggal->translatedFormat('d M Y') }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                    {{ $item->status == 'hadir' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $item->status == 'terlambat' ? 'bg-yellow-100 text-yellow-700' : '' }}
                    {{ $item->status == 'alfa' ? 'bg-red-100 text-red-700' : '' }}
                    {{ $item->status == 'izin' ? 'bg-blue-100 text-blue-700' : '' }}">
                    {{ ucfirst($item->status) }}
                </span>
            </div>
            <div class="flex gap-4 mt-1 text-xs text-gray-400">
                <span>Masuk: {{ $item->jam_masuk ?? '-' }}</span>
                <span>Keluar: {{ $item->jam_keluar ?? '-' }}</span>
                @if($item->lokasi_valid === 1)
                <span class="text-green-500"><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>Dalam area</span>
                @elseif($item->lokasi_valid === 0)
                <span class="text-orange-400"><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>Luar area</span>
                @endif
            </div>
        </div>
        @empty
        <p class="text-center text-gray-400 text-sm py-3">Belum ada riwayat.</p>
        @endforelse
    </div>
</div>

@endsection