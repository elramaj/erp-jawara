@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Permintaan Produk</h1>
    @if($bisaBuat)
    <a href="{{ route('produk.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">+ Permintaan Baru</a>
    @endif
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300">{{ session('error') }}</div>
@endif

<div class="flex gap-2 mb-4">
    @foreach(['berjalan' => 'Berjalan', 'selesai' => 'Selesai / Batal', 'semua' => 'Semua'] as $val => $label)
    <a href="{{ route('produk.index', ['tampil' => $val]) }}"
       class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition {{ $filter == $val ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">No. Permintaan</th>
                <th class="px-4 py-3 text-left">Judul</th>
                <th class="px-4 py-3 text-left">Sales</th>
                <th class="px-4 py-3 text-center">Item</th>
                <th class="px-4 py-3 text-left">Dibutuhkan</th>
                <th class="px-4 py-3 text-left">PIC Produk</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($permintaan as $r)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $r->no_permintaan }}</td>
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $r->judul }}</p>
                    <p class="text-xs text-gray-400">{{ $r->proyek->nama_proyek ?? 'Tanpa proyek' }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $r->creator->name ?? '-' }}</td>
                <td class="px-4 py-3 text-center">{{ $r->item_count }}</td>
                <td class="px-4 py-3 text-gray-600 text-xs">{{ $r->target_tanggal ? $r->target_tanggal->format('d M Y') : '-' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $r->picProduk->name ?? '-' }}</td>
                <td class="px-4 py-3">@include('produk._badge', ['status' => $r->status])</td>
                <td class="px-4 py-3"><a href="{{ route('produk.show', $r) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition">Detail</a></td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-10 text-center text-gray-400">Belum ada permintaan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
