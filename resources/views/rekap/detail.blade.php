@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">📋 Detail Absensi</h1>
        <p class="text-gray-500 text-sm mt-1">
            {{ $user->name }} —
            {{ Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F') }} {{ $tahun }}
        </p>
    </div>
    <a href="{{ route('rekap.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
        ← Kembali
    </a>
</div>

{{-- Summary Cards --}}
@php
    $hadir     = $absensi->whereIn('status', ['hadir'])->count();
    $terlambat = $absensi->where('status', 'terlambat')->count();
    $izin      = $absensi->whereIn('status', ['izin', 'sakit', 'cuti'])->count();
    $alfa      = $absensi->where('status', 'alfa')->count();
@endphp
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
        <p class="text-gray-500 text-xs">Hadir</p>
        <p class="text-2xl font-bold text-green-600">{{ $hadir }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-yellow-500">
        <p class="text-gray-500 text-xs">Terlambat</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $terlambat }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-500">
        <p class="text-gray-500 text-xs">Izin/Sakit</p>
        <p class="text-2xl font-bold text-blue-600">{{ $izin }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-red-500">
        <p class="text-gray-500 text-xs">Alfa</p>
        <p class="text-2xl font-bold text-red-600">{{ $alfa }}</p>
    </div>
</div>

{{-- Tabel Detail --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Hari</th>
                <th class="px-4 py-3 text-center">Jam Masuk</th>
                <th class="px-4 py-3 text-center">Jam Keluar</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-left">Keterangan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($absensi as $a)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-700">{{ $a->tanggal->format('d M Y') }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $a->tanggal->translatedFormat('l') }}</td>
                <td class="px-4 py-3 text-center text-indigo-600 font-medium">{{ $a->jam_masuk ?? '-' }}</td>
                <td class="px-4 py-3 text-center text-indigo-600 font-medium">{{ $a->jam_keluar ?? '-' }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $a->status == 'hadir' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $a->status == 'terlambat' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $a->status == 'alfa' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $a->status == 'izin' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $a->status == 'sakit' ? 'bg-purple-100 text-purple-700' : '' }}">
                        {{ ucfirst($a->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $a->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada data absensi bulan ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection