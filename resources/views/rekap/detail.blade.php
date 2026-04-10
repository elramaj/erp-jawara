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
                            'masuk_kantor' => ['🏢', 'Kantor', 'gray'],
                            'visit'        => ['🚗', 'Visit', 'purple'],
                            'wfh'          => ['🏠', 'WFH', 'blue'],
                            'sakit'        => ['🏥', 'Sakit', 'red'],
                            'izin'         => ['📋', 'Izin', 'orange'],
                            default        => ['📅', $a->tipe, 'gray'],
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $tipeLabel[2] }}-100 text-{{ $tipeLabel[2] }}-700">
                        {{ $tipeLabel[0] }} {{ $tipeLabel[1] }}
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
                        📍 {{ $a->lokasi_valid ? 'Dalam area' : 'Luar area' }}
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
            <h3 class="text-lg font-bold text-gray-800">📋 Detail Absensi</h3>
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
        masuk_kantor: '🏢 Masuk Kantor',
        visit: '🚗 Visit',
        wfh: '🏠 WFH',
        sakit: '🏥 Sakit',
        izin: '📋 Izin',
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
                    ${a.lokasi_valid ? '📍 Dalam area kantor' : '⚠️ Di luar area kantor'}
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
            <p class="text-xs text-gray-400 mb-3 font-semibold uppercase tracking-wider">📸 Foto Selfie</p>
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
            <p class="text-xs text-gray-400 mb-3 font-semibold uppercase tracking-wider">🗺️ Lokasi Absensi</p>
            <div class="rounded-xl overflow-hidden border border-gray-200 mb-3">
                <iframe
                    src="https://maps.google.com/maps?q=${a.lat_masuk},${a.lng_masuk}&z=16&output=embed"
                    width="100%" height="220" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="https://www.google.com/maps?q=${a.lat_masuk},${a.lng_masuk}" target="_blank"
                    class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                    📍 Buka Lokasi Masuk
                </a>`;
        if (a.lat_keluar && a.lng_keluar) {
            html += `<a href="https://www.google.com/maps?q=${a.lat_keluar},${a.lng_keluar}" target="_blank"
                    class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                    📍 Buka Lokasi Keluar
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