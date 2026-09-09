@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><svg class="w-6 h-6 inline-block -mt-1 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>Review Klaim Reimburse</h1>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg> {{ session('error') }}
</div>
@endif

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Karyawan</th>
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Keterangan</th>
                <th class="px-4 py-3 text-center">Bukti</th>
                <th class="px-4 py-3 text-right">Nominal</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($reimburse as $r)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $r->user->name ?? '-' }}</td>
                <td class="px-4 py-3">
                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                        {{ \App\Models\Reimburse::KATEGORI[$r->kategori] ?? $r->kategori }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-600 text-xs">{{ $r->tanggal_pengeluaran->format('d M Y') }}</td>
                <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $r->keterangan }}</td>
                <td class="px-4 py-3 text-center">
                    @if($r->bukti)
                    <button type="button" onclick="showBuktiModal('{{ asset('storage/' . $r->bukti) }}')"
                        class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-2 py-1 rounded text-xs font-semibold transition inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>Lihat
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
                <td class="px-4 py-3">
                    @if($r->status == 'pending')
                    <form method="POST" action="{{ route('reimburse.status', $r) }}" class="flex gap-2 items-center">
                        @csrf
                        <input type="text" name="catatan_approval" placeholder="Catatan (opsional)"
                            class="border border-gray-300 rounded px-2 py-1 text-xs w-32 focus:outline-none focus:ring-1 focus:ring-indigo-400">
                        <button type="submit" name="status" value="disetujui"
                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs font-semibold transition">
                            Setujui
                        </button>
                        <button type="submit" name="status" value="ditolak"
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold transition">
                            Tolak
                        </button>
                    </form>
                    @else
                        <span class="text-gray-400 text-xs">{{ $r->catatan_approval ?? '-' }}</span>
                        <p class="text-gray-300 text-[10px] mt-0.5">oleh {{ $r->approver->name ?? '-' }}</p>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-400">Belum ada pengajuan reimburse.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('components.bukti-modal')
@endsection
