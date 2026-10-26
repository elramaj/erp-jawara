@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Arsip Legalitas PT</h1>
        <p class="text-xs text-gray-400 mt-1">Dokumen tetap perusahaan. Peringatan muncul {{ \App\Models\DokumenArsip::HARI_PERINGATAN }} hari sebelum kedaluwarsa.</p>
    </div>
    @if($kelola)
    <a href="{{ route('arsip.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">+ Tambah Dokumen</a>
    @endif
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">{{ session('success') }}</div>
@endif

<form method="GET" class="flex flex-wrap gap-2 mb-4 items-center">
    @foreach(['semua' => 'Semua', 'segera' => 'Segera Habis', 'kedaluwarsa' => 'Kedaluwarsa', 'aktif' => 'Masih Berlaku'] as $val => $label)
    <a href="{{ route('arsip.index', ['tampil' => $val, 'q' => $cari]) }}"
       class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition {{ $filter == $val ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400' }}">{{ $label }}</a>
    @endforeach
    <input type="hidden" name="tampil" value="{{ $filter }}">
    <input type="text" name="q" value="{{ $cari }}" placeholder="Cari jenis / nama / nomor..." class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm ml-auto">
    <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-semibold">Cari</button>
</form>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Dokumen</th>
                <th class="px-4 py-3 text-left">Nomor</th>
                <th class="px-4 py-3 text-left">Terbit</th>
                <th class="px-4 py-3 text-left">Berlaku s/d</th>
                <th class="px-4 py-3 text-left">Masa</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($daftar as $a)
            @php
                $m = $a->status_masa;
                $label = ['tanpa_batas' => ['Tanpa batas', 'bg-gray-100 text-gray-600'], 'aktif' => ['Berlaku', 'bg-green-100 text-green-700'],
                          'segera' => ['Segera habis (' . $a->sisa_hari . ' hari)', 'bg-yellow-100 text-yellow-700'], 'kedaluwarsa' => ['Kedaluwarsa', 'bg-red-100 text-red-700']][$m];
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $a->nama }}</p>
                    <p class="text-xs text-gray-400">{{ $a->jenis }}{{ $a->penerbit ? ' · ' . $a->penerbit : '' }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $a->nomor ?: '-' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $a->tanggal_terbit ? $a->tanggal_terbit->format('d M Y') : '-' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $a->tanggal_kedaluwarsa ? $a->tanggal_kedaluwarsa->format('d M Y') : '-' }}</td>
                <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $label[1] }}">{{ $label[0] }}</span></td>
                <td class="px-4 py-3">
                    <div class="flex gap-2 items-center">
                        <a href="{{ route('arsip.file', [$a, 'unduh' => 1]) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition">Unduh</a>
                        @if($kelola)
                        <a href="{{ route('arsip.edit', $a) }}" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-1 rounded text-xs font-semibold transition">Edit</a>
                        <form method="POST" action="{{ route('arsip.destroy', $a) }}" onsubmit="return confirm('Hapus dokumen ini dari arsip?')">@csrf @method('DELETE')
                            <button class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded text-xs font-semibold transition">Hapus</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Belum ada dokumen di arsip.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
