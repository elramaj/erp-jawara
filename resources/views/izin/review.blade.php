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
                <th class="px-4 py-3 text-center">Lampiran</th>
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

                {{-- Lampiran --}}
                <td class="px-4 py-3 text-center">
                    @php
                        $attachments = \DB::table('izin_attachments')
                            ->where('pengajuan_izin_id', $p->id)
                            ->get();
                    @endphp
                    @if($attachments->count() > 0)
                        <button onclick="lihatLampiran({{ $attachments->toJson() }})"
                            class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-2 py-1 rounded text-xs font-semibold transition">
                            📎 {{ $attachments->count() }} file
                        </button>
                    @else
                        <span class="text-gray-300 text-xs">-</span>
                    @endif
                </td>

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
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada pengajuan izin.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Lampiran --}}
<div id="modal-lampiran" class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden flex items-center justify-center p-4"
    onclick="tutupLampiran()">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto"
        onclick="event.stopPropagation()">
        <div class="flex justify-between items-center p-6 border-b">
            <h3 class="text-lg font-bold text-gray-800">📎 Lampiran Izin</h3>
            <button onclick="tutupLampiran()" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
        </div>
        <div id="modal-lampiran-content" class="p-6 grid grid-cols-2 gap-4"></div>
    </div>
</div>

<script>
function lihatLampiran(attachments) {
    const content = document.getElementById('modal-lampiran-content');
    content.innerHTML = '';

    attachments.forEach(file => {
        const url = '/storage/' + file.file_path;
        if (file.file_type === 'image') {
            content.innerHTML += `
                <div class="rounded-xl overflow-hidden border border-gray-200">
                    <img src="${url}" class="w-full object-cover cursor-pointer hover:opacity-90"
                        onclick="window.open('${url}', '_blank')"
                        title="Klik untuk buka full">
                    <p class="text-xs text-gray-500 p-2 truncate">${file.file_name}</p>
                </div>`;
        } else {
            content.innerHTML += `
                <div class="rounded-xl border border-gray-200 p-4 flex flex-col items-center gap-3">
                    <div class="w-16 h-16 bg-red-100 rounded-xl flex items-center justify-center">
                        <span class="text-3xl">📄</span>
                    </div>
                    <p class="text-xs text-gray-600 text-center truncate w-full">${file.file_name}</p>
                    <a href="${url}" target="_blank"
                        class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                        Buka PDF
                    </a>
                </div>`;
        }
    });

    document.getElementById('modal-lampiran').classList.remove('hidden');
}

function tutupLampiran() {
    document.getElementById('modal-lampiran').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') tutupLampiran();
});
</script>
@endsection