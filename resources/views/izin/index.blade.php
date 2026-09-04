@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><svg class="w-6 h-6 inline-block -mt-1 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>Pengajuan Izin / Cuti</h1>
    <a href="{{ route('izin.create') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
        + Ajukan Izin
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