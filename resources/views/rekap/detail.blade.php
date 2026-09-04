@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>Detail Absensi</h1>
        <p class="text-gray-500 text-sm mt-1">
            {{ $user->name }} —
            {{ Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F') }} {{ $tahun }}
        </p>
    </div>
    <a href="{{ route('rekap.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5 w-fit">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg> Kembali
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
                <th class="px-4 py-3 text-left">Tipe</th>
                <th class="px-4 py-3 text-center">Jam Masuk</th>
                <th class="px-4 py-3 text-center">Jam Keluar</th>
                <th class="px-4 py-3 text-center">Foto</th>
                <th class="px-4 py-3 text-center">Lokasi</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Detail</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($absensi as $a)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-700">{{ $a->tanggal->format('d M Y') }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $a->tanggal->translatedFormat('l') }}</td>
                <td class="px-4 py-3">
                    @php
                        $tipeLabel = match($a->tipe ?? 'masuk_kantor') {
                            'masuk_kantor' => ['<svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>', 'Kantor', 'gray'],
                            'visit'        => ['<svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>', 'Visit', 'purple'],
                            'wfh'          => ['<svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>', 'WFH', 'blue'],
                            'sakit'        => ['<svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0-13.5V9M15 12H9m4.06-7.19-6 3.75a2.25 2.25 0 0 0-1.06 1.91v9.53h16.5v-9.53a2.25 2.25 0 0 0-1.06-1.91l-6-3.75a2.25 2.25 0 0 0-2.38 0Z" /></svg>', 'Sakit', 'red'],
                            'izin'         => ['<svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>', 'Izin', 'orange'],
                            default        => ['<svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>', $a->tipe, 'gray'],
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $tipeLabel[2] }}-100 text-{{ $tipeLabel[2] }}-700">
                        {!! $tipeLabel[0] !!} {{ $tipeLabel[1] }}
                    </span>
                    @if($a->nama_tujuan)
                    <p class="text-xs text-gray-400 mt-0.5">{{ $a->nama_tujuan }}</p>
                    @endif
                </td>
                <td class="px-4 py-3 text-center text-indigo-600 font-medium">{{ $a->jam_masuk ?? '-' }}</td>
                <td class="px-4 py-3 text-center text-indigo-600 font-medium">{{ $a->jam_keluar ?? '-' }}</td>

                {{-- Foto --}}
                <td class="px-4 py-3 text-center">
                    <div class="flex gap-1 justify-center">
                        @if($a->foto_masuk)
                        <img src="{{ Storage::url($a->foto_masuk) }}" alt="Masuk"
                            class="w-8 h-8 rounded-full object-cover border-2 border-green-300 cursor-pointer hover:opacity-80"
                            onclick="bukaFoto('{{ Storage::url($a->foto_masuk) }}', 'Foto Masuk - {{ $a->tanggal->format('d M Y') }}')"
                            title="Foto Masuk">
                        @else
                        <span class="text-gray-300 text-xs">-</span>
                        @endif
                        @if($a->foto_keluar)
                        <img src="{{ Storage::url($a->foto_keluar) }}" alt="Keluar"
                            class="w-8 h-8 rounded-full object-cover border-2 border-red-300 cursor-pointer hover:opacity-80"
                            onclick="bukaFoto('{{ Storage::url($a->foto_keluar) }}', 'Foto Keluar - {{ $a->tanggal->format('d M Y') }}')"
                            title="Foto Keluar">
                        @endif
                    </div>
                </td>

                {{-- Lokasi --}}
                <td class="px-4 py-3 text-center">
                    @if($a->lat_masuk && $a->lng_masuk)
                    <a href="https://www.google.com/maps?q={{ $a->lat_masuk }},{{ $a->lng_masuk }}"
                        target="_blank"
                        class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-lg
                        {{ $a->lokasi_valid ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}
                        hover:opacity-80 transition">
                        <svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg> {{ $a->lokasi_valid ? 'Dalam area' : 'Luar area' }}
                    </a>
                    @else
                    <span class="text-gray-300 text-xs">-</span>
                    @endif
                </td>

                {{-- Status --}}
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

                {{-- Tombol Detail --}}
                <td class="px-4 py-3 text-center">
                    <button onclick="bukaDetail({{ $a->id }})"
                        class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-2 py-1 rounded text-xs font-semibold transition">
                        Detail
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-4 py-8 text-center text-gray-400">Belum ada data absensi bulan ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Foto --}}
<div id="modal-foto" class="fixed inset-0 bg-black bg-opacity-80 z-50 hidden flex items-center justify-center p-4"
    onclick="tutupFoto()">
    <div class="max-w-lg w-full" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center mb-3">
            <p id="modal-foto-title" class="text-white font-semibold text-sm"></p>
            <button onclick="tutupFoto()" class="text-white hover:text-gray-300 text-xl">✕</button>
        </div>
        <img id="modal-foto-img" src="" class="w-full rounded-xl object-cover max-h-96">
    </div>
</div>

{{-- Modal Detail Absensi --}}
<div id="modal-detail" class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden flex items-center justify-center p-4"
    onclick="tutupDetail()">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto"
        onclick="event.stopPropagation()">
        <div class="flex justify-between items-center p-6 border-b">
            <h3 class="text-lg font-bold text-gray-800"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>Detail Absensi</h3>
            <button onclick="tutupDetail()" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
        </div>
        <div id="modal-detail-content" class="p-6">
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>
        </div>
    </div>
</div>

{{-- Data absensi untuk JS --}}
<script>
const absensiData = @json($absensiJson);

function bukaFoto(url, title) {
    document.getElementById('modal-foto-img').src = url;
    document.getElementById('modal-foto-title').textContent = title;
    document.getElementById('modal-foto').classList.remove('hidden');
}

function tutupFoto() {
    document.getElementById('modal-foto').classList.add('hidden');
}

function bukaDetail(id) {
    const a = absensiData[id];
    if (!a) return;

    const tipeLabel = {
        masuk_kantor: '<svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg> Masuk Kantor',
        visit: '<svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg> Visit',
        wfh: '<svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg> WFH',
        sakit: '<svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0-13.5V9M15 12H9m4.06-7.19-6 3.75a2.25 2.25 0 0 0-1.06 1.91v9.53h16.5v-9.53a2.25 2.25 0 0 0-1.06-1.91l-6-3.75a2.25 2.25 0 0 0-2.38 0Z" /></svg> Sakit',
        izin: '<svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg> Izin',
    };

    const statusColor = {
        hadir: 'bg-green-100 text-green-700',
        terlambat: 'bg-yellow-100 text-yellow-700',
        alfa: 'bg-red-100 text-red-700',
        izin: 'bg-blue-100 text-blue-700',
        sakit: 'bg-purple-100 text-purple-700',
    };

    let html = `
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-xs text-gray-400 mb-1">Tanggal</p>
                <p class="font-semibold text-gray-800">${a.hari}, ${a.tanggal}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Tipe</p>
                <p class="font-semibold text-gray-800">${tipeLabel[a.tipe] ?? a.tipe}</p>
            </div>
            ${a.nama_tujuan ? `
            <div class="col-span-2">
                <p class="text-xs text-gray-400 mb-1">Tujuan</p>
                <p class="font-semibold text-gray-800">${a.nama_tujuan}</p>
            </div>` : ''}
            <div>
                <p class="text-xs text-gray-400 mb-1">Jam Masuk</p>
                <p class="font-semibold text-indigo-600">${a.jam_masuk ?? '-'}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Jam Keluar</p>
                <p class="font-semibold text-indigo-600">${a.jam_keluar ?? '-'}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Status</p>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold ${statusColor[a.status] ?? 'bg-gray-100 text-gray-700'}">
                    ${a.status ? a.status.charAt(0).toUpperCase() + a.status.slice(1) : '-'}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Lokasi</p>
                <span class="text-sm font-semibold ${a.lokasi_valid ? 'text-green-600' : 'text-orange-500'}">
                    ${a.lokasi_valid ? '<svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg> Dalam area kantor' : '<svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg> Di luar area kantor'}
                </span>
            </div>
            ${a.catatan ? `
            <div class="col-span-2">
                <p class="text-xs text-gray-400 mb-1">Catatan</p>
                <p class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3">${a.catatan}</p>
            </div>` : ''}
        </div>`;

    // Foto
    if (a.foto_masuk || a.foto_keluar) {
        html += `<div class="border-t pt-4 mb-4">
            <p class="text-xs text-gray-400 mb-3 font-semibold uppercase tracking-wider"><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.174C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.174 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" /></svg> Foto Selfie</p>
            <div class="grid grid-cols-2 gap-4">`;
        if (a.foto_masuk) {
            html += `<div>
                <p class="text-xs text-gray-400 mb-1">Foto Masuk</p>
                <img src="${a.foto_masuk}" class="w-full rounded-xl object-cover h-48 cursor-pointer hover:opacity-90 border-2 border-green-200"
                    onclick="bukaFoto('${a.foto_masuk}', 'Foto Masuk - ${a.tanggal}')">
            </div>`;
        }
        if (a.foto_keluar) {
            html += `<div>
                <p class="text-xs text-gray-400 mb-1">Foto Keluar</p>
                <img src="${a.foto_keluar}" class="w-full rounded-xl object-cover h-48 cursor-pointer hover:opacity-90 border-2 border-red-200"
                    onclick="bukaFoto('${a.foto_keluar}', 'Foto Keluar - ${a.tanggal}')">
            </div>`;
        }
        html += `</div></div>`;
    }

    // Peta
    if (a.lat_masuk && a.lng_masuk) {
        html += `<div class="border-t pt-4">
            <p class="text-xs text-gray-400 mb-3 font-semibold uppercase tracking-wider"><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.752a1.125 1.125 0 0 0-1.006 0L3.622 6.189C3.24 6.38 3 6.77 3 7.195v10.486c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" /></svg> Lokasi Absensi</p>
            <div class="rounded-xl overflow-hidden border border-gray-200 mb-3">
                <iframe
                    src="https://maps.google.com/maps?q=${a.lat_masuk},${a.lng_masuk}&z=16&output=embed"
                    width="100%" height="220" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="https://www.google.com/maps?q=${a.lat_masuk},${a.lng_masuk}" target="_blank"
                    class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                    <svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg> Buka Lokasi Masuk
                </a>`;
        if (a.lat_keluar && a.lng_keluar) {
            html += `<a href="https://www.google.com/maps?q=${a.lat_keluar},${a.lng_keluar}" target="_blank"
                    class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                    <svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg> Buka Lokasi Keluar
                </a>`;
        }
        html += `</div>
            <p class="text-xs text-gray-400 mt-2">
                Koordinat: ${a.lat_masuk}, ${a.lng_masuk}
            </p>
        </div>`;
    }

    document.getElementById('modal-detail-content').innerHTML = html;
    document.getElementById('modal-detail').classList.remove('hidden');
}

function tutupDetail() {
    document.getElementById('modal-detail').classList.add('hidden');
}

// Tutup modal dengan ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        tutupFoto();
        tutupDetail();
    }
});
</script>
@endsection