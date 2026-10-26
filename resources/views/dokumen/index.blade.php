@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Permintaan Dokumen</h1>
    @if($bisaBuat)
    <a href="{{ route('dokumen.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">+ Minta Dokumen</a>
    @endif
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300">{{ session('error') }}</div>
@endif

<div class="flex gap-2 mb-4 flex-wrap">
    @foreach(['proses' => 'Sedang Berjalan', 'selesai' => 'Selesai', 'batal' => 'Dibatalkan', 'semua' => 'Semua'] as $val => $label)
    <a href="{{ route('dokumen.index', ['tampil' => $val]) }}"
       class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition {{ $filter == $val ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Dokumen</th>
                <th class="px-4 py-3 text-left">Peminta</th>
                <th class="px-4 py-3 text-left">Penyiap</th>
                <th class="px-4 py-3 text-left">Deadline</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($daftar as $d)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $d->jenis_dokumen }}</p>
                    <p class="text-xs text-gray-400">{{ $d->no_permintaan }}{{ $d->proyek ? ' · ' . $d->proyek->nama_proyek : '' }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $d->creator->name ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $d->penyiap->name ?? '-' }}</td>
                <td class="px-4 py-3 {{ $d->deadline && $d->deadline->isPast() && in_array($d->status, ['baru','diproses']) ? 'text-red-600 font-semibold' : 'text-gray-600' }}">{{ $d->deadline ? $d->deadline->format('d M Y') : '-' }}</td>
                <td class="px-4 py-3">@include('dokumen._badge', ['status' => $d->status])</td>
                <td class="px-4 py-3"><a href="{{ route('dokumen.show', $d) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition">Detail</a></td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Tidak ada permintaan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
