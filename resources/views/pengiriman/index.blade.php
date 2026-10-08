@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
        Pengiriman
    </h1>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300">{{ session('error') }}</div>
@endif

@if(auth()->user()->isSuperAdmin())
<div class="bg-purple-50 border border-purple-300 text-purple-700 px-4 py-2 rounded-lg mb-4 text-sm font-semibold">
    Mode Super Admin — menampilkan data gabungan dari SEMUA company.
</div>
@endif

{{-- Untuk Sales / Direktur: jadwal yang menunggu konfirmasi --}}
@if($menunggu->count() > 0)
<div class="bg-yellow-50 border border-yellow-300 rounded-xl p-4 mb-6">
    <p class="font-semibold text-yellow-800 mb-2">{{ $menunggu->count() }} jadwal menunggu konfirmasi</p>
    <div class="divide-y divide-yellow-200">
        @foreach($menunggu as $m)
        <a href="{{ route('pengiriman.show', $m) }}" class="flex justify-between items-center py-2 hover:bg-yellow-100 px-2 rounded">
            <span class="text-sm text-gray-800">{{ $m->proyek->nama_proyek }} <span class="text-gray-500">· {{ $m->no_pengiriman }}</span></span>
            <span class="text-xs text-gray-600">Kirim {{ $m->tanggal_kirim->format('d M Y') }}</span>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- Untuk tim Pengiriman: proyek yang perlu dijadwalkan --}}
@if($kelola)
<div class="mb-8">
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-semibold text-gray-800">Perlu Dijadwalkan <span class="text-sm font-normal text-gray-400">({{ $siap->count() }})</span></h2>
        @if($proyekAktif->count() > 0)
        <form method="GET" onsubmit="if(!this.proyek.value){return false;} window.location = '{{ url('/pengiriman/proyek') }}/' + this.proyek.value + '/buat'; return false;" class="flex gap-2">
            <select name="proyek" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm pr-8">
                <option value="">Jadwal manual untuk proyek lain...</option>
                @foreach($proyekAktif as $pa)
                <option value="{{ $pa->id }}">{{ $pa->kode_proyek }} — {{ $pa->nama_proyek }}</option>
                @endforeach
            </select>
            <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-sm font-semibold transition">Buat</button>
        </form>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($siap as $r)
        @php $p = $r['proyek']; $kel = $r['kel']; $hari = $r['hari']; @endphp
        <div class="bg-white rounded-xl shadow p-5 border-l-4 {{ $hari !== null && $hari < 0 ? 'border-red-500' : ($hari !== null && $hari <= 7 ? 'border-orange-400' : 'border-indigo-400') }}">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-xs font-mono text-gray-400">{{ $p->kode_proyek }}</p>
                    <p class="font-semibold text-gray-800">{{ $p->nama_proyek }}</p>
                    <p class="text-sm text-gray-500">{{ $p->klien }}</p>
                </div>
                @if(auth()->user()->isSuperAdmin())
                <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $p->company->nama ?? '-' }}</span>
                @endif
            </div>
            <p class="text-xs font-semibold {{ $kel['lengkap'] ? 'text-green-600' : 'text-orange-600' }} mb-2">{{ $r['alasan'] }}</p>

            <div class="mb-3">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Barang diterima gudang ({{ $kel['item_lengkap'] }}/{{ $kel['total_item'] }} item lengkap)</span>
                    <span class="font-semibold">{{ $kel['persen'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $kel['lengkap'] ? 'bg-green-500' : 'bg-indigo-500' }}" style="width: {{ $kel['persen'] }}%"></div>
                </div>
            </div>

            <div class="flex justify-between items-center text-xs text-gray-500 mb-3">
                <span>Deadline:
                    @if($p->deadline)
                        <strong class="{{ $hari < 0 ? 'text-red-600' : ($hari <= 7 ? 'text-orange-600' : 'text-gray-700') }}">
                            {{ $p->deadline->format('d M Y') }}
                            ({{ $hari < 0 ? 'terlewat ' . abs($hari) . ' hari' : ($hari == 0 ? 'hari ini' : $hari . ' hari lagi') }})
                        </strong>
                    @else
                        -
                    @endif
                </span>
                <span>Sales: {{ $p->salesAnggota()->pluck('name')->join(', ') ?: 'belum ada' }}</span>
            </div>

            <a href="{{ route('pengiriman.create', $p) }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Atur Jadwal Pengiriman</a>
        </div>
        @empty
        <div class="md:col-span-2 bg-white rounded-xl shadow p-8 text-center text-gray-400">
            Tidak ada proyek yang perlu dijadwalkan saat ini.
        </div>
        @endforelse
    </div>
</div>
@endif

{{-- Daftar jadwal --}}
<div class="flex justify-between items-center mb-3">
    <h2 class="text-lg font-semibold text-gray-800">Jadwal Pengiriman</h2>
    <div class="flex gap-2">
        @foreach(['berjalan' => 'Berjalan', 'selesai' => 'Selesai / Batal', 'semua' => 'Semua'] as $val => $label)
        <a href="{{ route('pengiriman.index', ['tampil' => $val]) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition {{ $filter == $val ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400' }}">{{ $label }}</a>
        @endforeach
    </div>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">No. Pengiriman</th>
                <th class="px-4 py-3 text-left">Proyek</th>
                <th class="px-4 py-3 text-left">Tanggal Kirim</th>
                <th class="px-4 py-3 text-left">Tujuan</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($pengiriman as $g)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $g->no_pengiriman }}</td>
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $g->proyek->nama_proyek ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $g->proyek->klien ?? '' }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $g->tanggal_kirim->format('d M Y') }}{{ $g->jam ? ' · ' . $g->jam : '' }}</td>
                <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $g->alamat_tujuan }}</td>
                <td class="px-4 py-3">@include('pengiriman._badge', ['status' => $g->status])</td>
                <td class="px-4 py-3">
                    <a href="{{ route('pengiriman.show', $g) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Belum ada jadwal pengiriman.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
