@extends('layouts.app')

@section('content')

{{-- Greeting --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        👋 Halo, {{ auth()->user()->name }}!
    </h1>
    <p class="text-gray-500 text-sm mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
</div>

{{-- Status Absensi Hari Ini --}}
<div class="bg-white rounded-xl shadow p-5 mb-6 flex items-center justify-between">
    <div>
        <p class="text-sm text-gray-500">Status Absensi Hari Ini</p>
        @if($absensiHariIni)
            <p class="text-lg font-bold mt-1
                {{ $absensiHariIni->status == 'hadir' ? 'text-green-600' : '' }}
                {{ $absensiHariIni->status == 'terlambat' ? 'text-yellow-600' : '' }}">
                {{ ucfirst($absensiHariIni->status) }}
                — Masuk {{ $absensiHariIni->jam_masuk }}
                @if($absensiHariIni->jam_keluar)
                    | Keluar {{ $absensiHariIni->jam_keluar }}
                @endif
            </p>
        @else
            <p class="text-lg font-bold text-red-500 mt-1">⚠️ Belum Absen!</p>
        @endif
    </div>
    <a href="{{ route('absensi.index') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
        {{ $absensiHariIni ? 'Lihat Absensi' : 'Absen Sekarang' }}
    </a>
</div>

{{-- Statistik Pribadi Bulan Ini --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <p class="text-gray-500 text-xs">Hadir Bulan Ini</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ $totalHadir }}</p>
        <p class="text-xs text-gray-400 mt-1">hari</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-500">
        <p class="text-gray-500 text-xs">Terlambat</p>
        <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $totalTerlambat }}</p>
        <p class="text-xs text-gray-400 mt-1">kali</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
        <p class="text-gray-500 text-xs">Izin/Sakit</p>
        <p class="text-3xl font-bold text-blue-600 mt-1">{{ $totalIzin }}</p>
        <p class="text-xs text-gray-400 mt-1">hari</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-500">
        <p class="text-gray-500 text-xs">Izin Pending</p>
        <p class="text-3xl font-bold text-purple-600 mt-1">{{ $izinPending }}</p>
        <p class="text-xs text-gray-400 mt-1">pengajuan</p>
    </div>
</div>

{{-- Statistik Kantor (khusus admin) --}}
@if(auth()->user()->role_id == 11)
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-indigo-600 rounded-xl shadow p-5 text-white">
        <p class="text-indigo-200 text-xs">Total Karyawan Aktif</p>
        <p class="text-3xl font-bold mt-1">{{ $totalKaryawan }}</p>
        <p class="text-indigo-200 text-xs mt-1">orang</p>
    </div>
    <div class="bg-green-600 rounded-xl shadow p-5 text-white">
        <p class="text-green-200 text-xs">Hadir Hari Ini</p>
        <p class="text-3xl font-bold mt-1">{{ $hadirHariIni }}</p>
        <p class="text-green-200 text-xs mt-1">karyawan</p>
    </div>
    <div class="bg-orange-500 rounded-xl shadow p-5 text-white flex items-center justify-between">
        <div>
            <p class="text-orange-100 text-xs">Izin Menunggu Review</p>
            <p class="text-3xl font-bold mt-1">{{ $izinPendingAdmin }}</p>
            <p class="text-orange-100 text-xs mt-1">pengajuan</p>
        </div>
        @if($izinPendingAdmin > 0)
        <a href="{{ route('izin.review') }}"
           class="bg-white text-orange-500 hover:bg-orange-50 px-3 py-1.5 rounded-lg text-xs font-bold transition">
            Review
        </a>
        @endif
    </div>
</div>
@endif

{{-- Grafik Kehadiran 7 Hari Terakhir --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-base font-semibold text-gray-700 mb-4">📅 Kehadiran 7 Hari Terakhir</h2>
    <div class="flex items-end gap-3 h-28">
        @foreach($grafik as $g)
        <div class="flex-1 flex flex-col items-center gap-1">
            <div class="w-full rounded-t-lg
                {{ $g['status'] == 'hadir' ? 'bg-green-400' : '' }}
                {{ $g['status'] == 'terlambat' ? 'bg-yellow-400' : '' }}
                {{ $g['status'] == 'alfa' ? 'bg-gray-200' : '' }}
                {{ $g['status'] == 'izin' || $g['status'] == 'sakit' ? 'bg-blue-400' : '' }}"
                style="height: {{ $g['status'] == 'alfa' ? '20%' : '100%' }}">
            </div>
            <p class="text-xs text-gray-500">{{ $g['hari'] }}</p>
        </div>
        @endforeach
    </div>
    <div class="flex gap-4 mt-4 text-xs text-gray-500">
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-400 inline-block"></span> Hadir</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-yellow-400 inline-block"></span> Terlambat</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-400 inline-block"></span> Izin/Sakit</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-gray-200 inline-block"></span> Alfa</span>
    </div>
</div>

@endsection