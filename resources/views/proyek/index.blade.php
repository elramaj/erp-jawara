@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">📁 Manajemen Proyek</h1>
    @if(in_array(auth()->user()->role_id, [1, 10, 11]))
    <a href="{{ route('proyek.create') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
        + Tambah Proyek
    </a>
    @endif
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">
    ✅ {{ session('success') }}
</div>
@endif

{{-- Filter Status --}}
<div class="flex gap-2 mb-4">
@foreach(['semua' => 'Semua', 'bola_liar' => '🎱 Bola Liar', 'aktif' => 'Aktif', 'draft' => 'Draft', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $val => $label)
    <button onclick="filterProyek('{{ $val }}')"
        class="filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold border transition {{ $val == 'semua' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400' }}"
        data-filter="{{ $val }}">
        {{ $label }}
    </button>
    @endforeach
</div>

{{-- Grid Proyek --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="proyek-grid">
    @forelse($proyek as $p)
    <div class="bg-white rounded-xl shadow hover:shadow-md transition proyek-card" data-status="{{ $p->status }}">
        <div class="p-5">
            <div class="flex justify-between items-start mb-3">
                <span class="text-xs font-mono text-gray-400">{{ $p->kode_proyek }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                    {{ $p->status == 'aktif' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $p->status == 'bola_liar' ? 'bg-orange-100 text-orange-700' : '' }}
                    {{ $p->status == 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
                    {{ $p->status == 'selesai' ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ $p->status == 'dibatalkan' ? 'bg-red-100 text-red-700' : '' }}">
                    {{ $p->status == 'bola_liar' ? '🎱 Bola Liar' : ucfirst($p->status) }}
                </span>
            </div>
            <h3 class="font-semibold text-gray-800 mb-1">{{ $p->nama_proyek }}</h3>
            <p class="text-sm text-gray-500 mb-3">🏢 {{ $p->klien }}</p>

            {{-- Progress Bar --}}
            <div class="mb-3">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Progress</span>
                    <span class="font-semibold">{{ $p->progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $p->progress == 100 ? 'bg-green-500' : 'bg-indigo-500' }}"
                         style="width: {{ $p->progress }}%"></div>
                </div>
            </div>

            <div class="flex justify-between items-center text-xs text-gray-400">
                <span>⏰ {{ $p->deadline ? $p->deadline->format('d M Y') : '-' }}</span>
                <span>👥 {{ $p->anggota->count() }} anggota</span>
            </div>
        </div>
        <div class="border-t px-5 py-3 flex justify-between items-center">
            <span class="text-xs text-gray-400">
                {{ $p->nilai_kontrak ? 'Rp ' . number_format($p->nilai_kontrak, 0, ',', '.') : 'Nilai belum diset' }}
            </span>
            <a href="{{ route('proyek.show', $p) }}"
               class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-3 py-1 rounded text-xs font-semibold transition">
                Detail →
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-xl shadow p-10 text-center text-gray-400">
        Belum ada proyek.
    </div>
    @endforelse
</div>

<script>
function filterProyek(status) {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        btn.classList.add('bg-white', 'text-gray-600', 'border-gray-300');
    });
    document.querySelector(`[data-filter="${status}"]`).classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
    document.querySelectorAll('.proyek-card').forEach(card => {
        card.style.display = (status === 'semua' || card.dataset.status === status) ? 'block' : 'none';
    });
}
</script>
@endsection