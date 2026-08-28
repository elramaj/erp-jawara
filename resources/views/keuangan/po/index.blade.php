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

@if(auth()->user()->isSuperAdmin())
<div class="bg-purple-50 border border-purple-300 text-purple-700 px-4 py-2 rounded-lg mb-4 text-sm font-semibold">
    <svg class="w-4 h-4 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" /></svg> Mode Super Admin — menampilkan data gabungan dari SEMUA company.
</div>
@endif

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">No PO</th>
                @if(auth()->user()->isSuperAdmin())
                <th class="px-4 py-3 text-left">Company</th>
                @endif
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
                @if(auth()->user()->isSuperAdmin())
                <td class="px-4 py-3">
                    <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                        {{ $p->company->nama ?? '-' }}
                    </span>
                </td>
                @endif
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
            <tr><td colspan="{{ auth()->user()->isSuperAdmin() ? 8 : 7 }}" class="px-4 py-8 text-center text-gray-400">Belum ada Purchase Order.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection