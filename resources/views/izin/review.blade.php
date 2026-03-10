@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">✅ Review Pengajuan Izin</h1>
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
                <th class="px-4 py-3 text-left">Karyawan</th>
                <th class="px-4 py-3 text-left">Jenis</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Alasan</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($pengajuan as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $p->user->name ?? '-' }}</td>
                <td class="px-4 py-3">
                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-semibold capitalize">
                        {{ str_replace('_', ' ', $p->jenis) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-600 text-xs">
                    {{ $p->tanggal_mulai->format('d M Y') }} —
                    {{ $p->tanggal_selesai->format('d M Y') }}
                </td>
                <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $p->alasan }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $p->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $p->status == 'disetujui' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $p->status == 'ditolak' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($p->status) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    @if($p->status == 'pending')
                    <form method="POST" action="{{ route('izin.status', $p) }}" class="flex gap-2 items-center">
                        @csrf
                        <input type="text" name="catatan_review" placeholder="Catatan (opsional)"
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
                        <span class="text-gray-400 text-xs">{{ $p->catatan_review ?? '-' }}</span>
                    @endif
                </td>
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