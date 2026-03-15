@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">🛒 Sales Order</h1>
    <a href="{{ route('so.create') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
        + Buat SO
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">✅ {{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">No SO</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Customer</th>
                <th class="px-4 py-3 text-left">Proyek</th>
                <th class="px-4 py-3 text-right">Total</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($so as $s)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs font-semibold text-indigo-600">{{ $s->no_so }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $s->tanggal->format('d M Y') }}</td>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $s->customer->nama ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $s->proyek->nama_proyek ?? '-' }}</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-700">
                    Rp {{ number_format($s->total, 0, ',', '.') }}
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $s->status == 'confirmed' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $s->status == 'partial' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $s->status == 'selesai' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $s->status == 'batal' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $s->status == 'draft' ? 'bg-gray-100 text-gray-600' : '' }}">
                        {{ ucfirst($s->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('so.show', $s) }}"
                       class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-3 py-1 rounded text-xs font-semibold transition">
                        Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada Sales Order.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection