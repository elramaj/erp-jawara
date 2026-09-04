@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" /></svg>
        Aset Perusahaan
    </h1>
    <a href="{{ route('aset.create') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
        + Tambah Aset
    </a>
</div>

@if(auth()->user()->isSuperAdmin())
<div class="bg-purple-50 border border-purple-300 text-purple-700 px-4 py-2 rounded-lg mb-4 text-sm font-semibold">
    <svg class="w-4 h-4 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" /></svg> Mode Super Admin — menampilkan data gabungan dari SEMUA company.
</div>
@endif

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
    {{ session('success') }}
</div>
@endif

{{-- Filter --}}
<form method="GET" class="bg-white rounded-xl shadow p-4 mb-4 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[180px]">
        <label class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama, kode, atau SN..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
    </div>
    <div class="min-w-[160px]">
        <label class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
        <select name="kategori_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 pr-8">
            <option value="">Semua kategori</option>
            @foreach($kategori as $k)
            <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="min-w-[160px]">
        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 pr-8">
            <option value="">Semua status</option>
            <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia (gudang)</option>
            <option value="dipakai" {{ request('status') == 'dipakai' ? 'selected' : '' }}>Dipakai</option>
            <option value="diperbaiki" {{ request('status') == 'diperbaiki' ? 'selected' : '' }}>Diperbaiki</option>
            <option value="hilang" {{ request('status') == 'hilang' ? 'selected' : '' }}>Hilang</option>
        </select>
    </div>
    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Filter</button>
    @if(request()->hasAny(['q','kategori_id','status']))
    <a href="{{ route('aset.index') }}" class="text-gray-500 hover:text-gray-700 text-sm px-2 py-2">Reset</a>
    @endif
</form>

@php
$statusBadge = [
    'tersedia'   => ['bg-blue-100 text-blue-700', 'Tersedia'],
    'dipakai'    => ['bg-green-100 text-green-700', 'Dipakai'],
    'diperbaiki' => ['bg-yellow-100 text-yellow-700', 'Diperbaiki'],
    'hilang'     => ['bg-red-100 text-red-700', 'Hilang'],
];
$kondisiBadge = [
    'baik'         => ['bg-green-100 text-green-700', 'Baik'],
    'rusak_ringan' => ['bg-yellow-100 text-yellow-700', 'Rusak Ringan'],
    'rusak_berat'  => ['bg-red-100 text-red-700', 'Rusak Berat'],
    'hilang'       => ['bg-red-100 text-red-700', 'Hilang'],
];
@endphp

{{-- Desktop: Tabel --}}
<div class="bg-white rounded-xl shadow overflow-hidden hidden md:block">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Kode</th>
                <th class="px-4 py-3 text-left">Nama Aset</th>
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-left">Serial Number</th>
                @if(auth()->user()->isSuperAdmin())
                <th class="px-4 py-3 text-left">Company</th>
                @endif
                <th class="px-4 py-3 text-left">Dipegang Oleh</th>
                <th class="px-4 py-3 text-center">Kondisi</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($aset as $a)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ $a->kode_aset }}</td>
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $a->nama_aset }}</p>
                    <p class="text-xs text-gray-400">{{ $a->merk }} {{ $a->model }}</p>
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $a->kategori->nama ?? '-' }}</td>
                <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $a->serial_number ?? '-' }}</td>
                @if(auth()->user()->isSuperAdmin())
                <td class="px-4 py-3">
                    <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                        {{ $a->company->nama ?? '-' }}
                    </span>
                </td>
                @endif
                <td class="px-4 py-3 text-gray-600">{{ $a->pemegang->name ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="{{ $kondisiBadge[$a->kondisi][0] }} px-2 py-0.5 rounded-full text-xs font-semibold">
                        {{ $kondisiBadge[$a->kondisi][1] }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="{{ $statusBadge[$a->status][0] }} px-2 py-0.5 rounded-full text-xs font-semibold">
                        {{ $statusBadge[$a->status][1] }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('aset.show', $a) }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="{{ auth()->user()->isSuperAdmin() ? 9 : 8 }}" class="px-4 py-8 text-center text-gray-400">Belum ada aset terdaftar.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Mobile: Card List --}}
<div class="space-y-3 md:hidden">
    @forelse($aset as $a)
    <a href="{{ route('aset.show', $a) }}" class="block bg-white rounded-xl shadow p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="font-mono text-xs text-gray-400">{{ $a->kode_aset }}</p>
                <p class="font-semibold text-gray-800 mt-0.5">{{ $a->nama_aset }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $a->kategori->nama ?? '-' }} • {{ $a->merk }} {{ $a->model }}</p>
                @if($a->serial_number)
                <p class="text-xs text-gray-400 font-mono mt-0.5">SN: {{ $a->serial_number }}</p>
                @endif
                @if(auth()->user()->isSuperAdmin())
                <span class="inline-block mt-1 bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-[10px] font-semibold">
                    {{ $a->company->nama ?? '-' }}
                </span>
                @endif
            </div>
            <span class="{{ $statusBadge[$a->status][0] }} px-2 py-0.5 rounded-full text-xs font-semibold flex-shrink-0">
                {{ $statusBadge[$a->status][1] }}
            </span>
        </div>
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
            <p class="text-xs text-gray-500">Dipegang: <span class="font-medium text-gray-700">{{ $a->pemegang->name ?? '—' }}</span></p>
            <span class="{{ $kondisiBadge[$a->kondisi][0] }} px-2 py-0.5 rounded-full text-[10px] font-semibold">
                {{ $kondisiBadge[$a->kondisi][1] }}
            </span>
        </div>
    </a>
    @empty
    <div class="bg-white rounded-xl shadow p-8 text-center text-gray-400">Belum ada aset terdaftar.</div>
    @endforelse
</div>
@endsection