@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tayang E-Catalog</h1>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">{{ session('success') }}</div>
@endif

<div class="flex gap-2 mb-4 flex-wrap">
    @foreach(['proses' => 'Perlu Diproses', 'tayang' => 'Sudah Tayang', 'turun' => 'Diturunkan', 'desain' => 'Butuh Desain', 'semua' => 'Semua'] as $val => $label)
    <a href="{{ route('tayang.index', ['tampil' => $val]) }}"
       class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition {{ $filter == $val ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Judul</th>
                <th class="px-4 py-3 text-left">Sales</th>
                <th class="px-4 py-3 text-left">Platform</th>
                <th class="px-4 py-3 text-left">Link</th>
                <th class="px-4 py-3 text-center">Desain</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tayang as $t)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $t->judul }}</p>
                    <p class="text-xs text-gray-400">{{ $t->permintaan->no_permintaan ?? '' }}{{ $t->proyek ? ' · ' . $t->proyek->nama_proyek : '' }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $t->permintaan->creator->name ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $t->platform ?: '-' }}</td>
                <td class="px-4 py-3 max-w-xs truncate">
                    @if($t->link_tayang)<a href="{{ $t->link_tayang }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">{{ $t->link_tayang }}</a>@else <span class="text-gray-300">-</span>@endif
                </td>
                <td class="px-4 py-3 text-center">@if($t->perlu_desain)<span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-xs font-semibold">Butuh</span>@else <span class="text-gray-300">-</span>@endif</td>
                <td class="px-4 py-3">@include('tayang._badge', ['status' => $t->status])</td>
                <td class="px-4 py-3"><a href="{{ route('tayang.show', $t) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition">Detail</a></td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
