@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><svg class="w-6 h-6 inline-block -mt-1 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>Reimburse Saya</h1>
    <a href="{{ route('reimburse.create') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
        + Ajukan Reimburse
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-left">Keterangan</th>
                <th class="px-4 py-3 text-center">Bukti</th>
                <th class="px-4 py-3 text-right">Nominal</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Catatan Keuangan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($reimburse as $r)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-600">{{ $r->tanggal_pengeluaran->format('d M Y') }}</td>
                <td class="px-4 py-3">
                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                        {{ \App\Models\Reimburse::KATEGORI[$r->kategori] ?? $r->kategori }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $r->keterangan }}</td>
                <td class="px-4 py-3 text-center">
                    @if($r->bukti)
                    <button type="button" onclick="showBuktiModal('{{ asset('storage/' . $r->bukti) }}')" class="text-indigo-600 hover:text-indigo-800">
                        <svg class="w-4 h-4 inline-block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                    </button>
                    @else
                    <span class="text-gray-300 text-xs">-</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right font-semibold text-gray-800">
                    Rp {{ number_format($r->nominal, 0, ',', '.') }}
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $r->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $r->status == 'disetujui' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $r->status == 'ditolak' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($r->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $r->catatan_approval ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada pengajuan reimburse.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('components.bukti-modal')
@endsection
