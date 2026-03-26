@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">📊 Rekap Absensi Karyawan</h1>
</div>

{{-- Filter Bulan & Tahun --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="{{ route('rekap.index') }}" class="flex gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
            <select name="bulan" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                @foreach(range(1,12) as $b)
                <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                    {{ Carbon\Carbon::createFromDate(2024, $b, 1)->translatedFormat('F') }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
            <select name="tahun" class="border border-gray-300 rounded-lg px-3 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                @foreach(range(2024, 2027) as $t)
                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            Tampilkan
        </button>
    </form>
</div>

{{-- Export Excel --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <p class="text-sm font-semibold text-gray-700 mb-3">📥 Export Rekap Absensi</p>
    <form method="POST" action="{{ route('rekap.export') }}" class="flex gap-3 items-end">
        @csrf
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" value="{{ date('Y-m-01') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" value="{{ date('Y-m-d') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <button type="submit"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            📥 Download Excel
        </button>
    </form>
</div>

{{-- Tabel Rekap --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Karyawan</th>
                <th class="px-4 py-3 text-center">Hari Kerja</th>
                <th class="px-4 py-3 text-center">Hadir</th>
                <th class="px-4 py-3 text-center">Terlambat</th>
                <th class="px-4 py-3 text-center">Izin/Sakit</th>
                <th class="px-4 py-3 text-center">Alfa</th>
                <th class="px-4 py-3 text-center">% Kehadiran</th>
                <th class="px-4 py-3 text-center">Detail</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($karyawan as $k)
            @php
                $hadir     = $k->absensi->whereIn('status', ['hadir'])->count();
                $terlambat = $k->absensi->where('status', 'terlambat')->count();
                $izin      = $k->absensi->whereIn('status', ['izin', 'sakit', 'cuti'])->count();
                $alfa      = $k->absensi->where('status', 'alfa')->count();
                $totalHadir = $hadir + $terlambat;
                $persen    = $totalHariKerja > 0 ? round(($totalHadir / $totalHariKerja) * 100) : 0;
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(substr($k->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $k->name }}</p>
                            <p class="text-xs text-gray-400 capitalize">{{ $k->role->name ?? '-' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-center text-gray-600">{{ $totalHariKerja }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $hadir }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $terlambat }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $izin }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $alfa }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <div class="w-16 bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $persen >= 80 ? 'bg-green-500' : ($persen >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                 style="width: {{ $persen }}%"></div>
                        </div>
                        <span class="text-xs font-semibold {{ $persen >= 80 ? 'text-green-600' : ($persen >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $persen }}%
                        </span>
                    </div>
                </td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('rekap.detail', [$k, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                       class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-3 py-1 rounded text-xs font-semibold transition">
                        Lihat
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-400">Belum ada data karyawan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection