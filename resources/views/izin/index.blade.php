@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">📋 Pengajuan Izin / Cuti</h1>
    <a href="{{ route('izin.create') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
        + Ajukan Izin
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">
    ✅ {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Jenis</th>
                <th class="px-4 py-3 text-left">Tanggal Mulai</th>
                <th class="px-4 py-3 text-left">Tanggal Selesai</th>
                <th class="px-4 py-3 text-left">Alasan</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Catatan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($pengajuan as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-semibold capitalize">
                        {{ str_replace('_', ' ', $p->jenis) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $p->tanggal_mulai->format('d M Y') }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $p->tanggal_selesai->format('d M Y') }}</td>
                <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $p->alasan }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $p->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $p->status == 'disetujui' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $p->status == 'ditolak' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($p->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $p->catatan_review ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada pengajuan izin.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection