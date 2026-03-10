@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">🕐 Absensi</h1>
    <p class="text-gray-500 text-sm mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
</div>

{{-- Alert sukses/error --}}
@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">
    ✅ {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300">
    ❌ {{ session('error') }}
</div>
@endif

{{-- Card Check-in / Check-out --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">

    {{-- Status Hari Ini --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Status Hari Ini</h2>
        @if($absensiHariIni)
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Status</span>
                    <span class="font-semibold capitalize
                        {{ $absensiHariIni->status == 'hadir' ? 'text-green-600' : '' }}
                        {{ $absensiHariIni->status == 'terlambat' ? 'text-yellow-600' : '' }}
                        {{ $absensiHariIni->status == 'alfa' ? 'text-red-600' : '' }}">
                        {{ ucfirst($absensiHariIni->status) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Jam Masuk</span>
                    <span class="font-semibold text-indigo-600">
                        {{ $absensiHariIni->jam_masuk ?? '-' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Jam Keluar</span>
                    <span class="font-semibold text-indigo-600">
                        {{ $absensiHariIni->jam_keluar ?? '-' }}
                    </span>
                </div>
            </div>
        @else
            <p class="text-gray-400 text-sm">Belum absen hari ini.</p>
        @endif
    </div>

    {{-- Tombol Check-in / Check-out --}}
    <div class="bg-white rounded-xl shadow p-6 flex flex-col justify-center items-center gap-4">
        @if(!$absensiHariIni)
            <form method="POST" action="{{ route('absensi.checkin') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-xl text-lg transition">
                    ✅ Check-in Sekarang
                </button>
            </form>
        @elseif(!$absensiHariIni->jam_keluar)
            <p class="text-green-600 font-semibold">✅ Sudah Check-in jam {{ $absensiHariIni->jam_masuk }}</p>
            <form method="POST" action="{{ route('absensi.checkout') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-4 rounded-xl text-lg transition">
                    🔴 Check-out Sekarang
                </button>
            </form>
        @else
            <div class="text-center">
                <p class="text-green-600 font-semibold text-lg">✅ Absensi Selesai</p>
                <p class="text-gray-500 text-sm mt-1">
                    {{ $absensiHariIni->jam_masuk }} — {{ $absensiHariIni->jam_keluar }}
                </p>
            </div>
        @endif
    </div>
</div>

{{-- Riwayat Absensi --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">📋 Riwayat 7 Hari Terakhir</h2>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-500 border-b">
                <th class="pb-2">Tanggal</th>
                <th class="pb-2">Jam Masuk</th>
                <th class="pb-2">Jam Keluar</th>
                <th class="pb-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat as $item)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-2">{{ $item->tanggal->translatedFormat('d M Y') }}</td>
                <td class="py-2">{{ $item->jam_masuk ?? '-' }}</td>
                <td class="py-2">{{ $item->jam_keluar ?? '-' }}</td>
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
            <tr>
                <td colspan="4" class="py-4 text-center text-gray-400">Belum ada riwayat absensi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection