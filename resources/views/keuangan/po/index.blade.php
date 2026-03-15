@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">🛍️ Purchase Order</h1>
    <a href="{{ route('po.create') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
        + Buat PO
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">✅ {{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">No PO</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Supplier</th>
                <th class="px-4 py-3 text-left">Proyek</th>
                <th class="px-4 py-3 text-right">Total</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($po as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs font-semibold text-indigo-600">{{ $p->no_po }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $p->tanggal->format('d M Y') }}</td>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $p->supplier->nama ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $p->proyek->nama_proyek ?? '-' }}</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-700">
                    Rp {{ number_format($p->total, 0, ',', '.') }}
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $p->status == 'confirmed' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $p->status == 'partial' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $p->status == 'selesai' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $p->status == 'batal' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $p->status == 'draft' ? 'bg-gray-100 text-gray-600' : '' }}">
                        {{ ucfirst($p->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('po.show', $p) }}"
                       class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-3 py-1 rounded text-xs font-semibold transition">
                        Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada Purchase Order.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection