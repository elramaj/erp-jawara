@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">🏭 Manajemen Gudang</h1>
    <div class="flex gap-2">
        <a href="{{ route('gudang.opname') }}"
           class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            📋 Stok Opname
        </a>
        <a href="{{ route('gudang.barang.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            + Tambah Barang
        </a>
    </div>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">
    ✅ {{ session('success') }}
</div>
@endif

{{-- Alert Stok Minimum --}}
@if($alertStok->count() > 0)
<div class="bg-red-50 border border-red-300 rounded-xl p-4 mb-6">
    <p class="text-red-700 font-semibold mb-2">⚠️ Stok Menipis! ({{ $alertStok->count() }} barang)</p>
    <div class="flex flex-wrap gap-2">
        @foreach($alertStok as $a)
        <a href="{{ route('gudang.barang.show', $a) }}"
           class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold hover:bg-red-200 transition">
            {{ $a->nama_barang }} (sisa {{ $a->total_stok }} {{ $a->satuan }})
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- Tabel Barang --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Kode</th>
                <th class="px-4 py-3 text-left">Nama Barang</th>
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-center">Stok</th>
                <th class="px-4 py-3 text-center">Min. Stok</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($barang as $b)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ $b->kode_barang }}</td>
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $b->nama_barang }}</p>
                    <p class="text-xs text-gray-400">{{ $b->satuan }}</p>
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $b->kategori->nama ?? '-' }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="text-lg font-bold {{ $b->total_stok <= $b->stok_minimum ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $b->total_stok }}
                    </span>
                    <span class="text-xs text-gray-400">{{ $b->satuan }}</span>
                </td>
                <td class="px-4 py-3 text-center text-gray-500">{{ $b->stok_minimum }}</td>
                <td class="px-4 py-3 text-center">
                    @if($b->total_stok <= 0)
                        <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-semibold">Habis</span>
                    @elseif($b->total_stok <= $b->stok_minimum)
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs font-semibold">Menipis</span>
                    @else
                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-semibold">Aman</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex gap-2 justify-center">
                        <a href="{{ route('gudang.barang.show', $b) }}"
                           class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-3 py-1 rounded text-xs font-semibold transition">
                            Detail
                        </a>
                        <form method="POST" action="{{ route('gudang.barang.destroy', $b) }}"
                            onsubmit="return confirm('Yakin hapus barang ini? Semua data stok dan SN akan ikut terhapus!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded text-xs font-semibold transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada barang.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection