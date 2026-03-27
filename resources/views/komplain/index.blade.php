@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">⚠️ Manajemen Komplain</h1>
    <a href="{{ route('komplain.create') }}"
       class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
        + Buat Komplain
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">✅ {{ session('success') }}</div>
@endif

{{-- Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-gray-400">
        <p class="text-gray-500 text-xs">Total Komplain</p>
        <p class="text-3xl font-bold text-gray-700 mt-1">{{ $komplain->count() }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-red-500">
        <p class="text-gray-500 text-xs">Open</p>
        <p class="text-3xl font-bold text-red-600 mt-1">{{ $totalOpen }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-500">
        <p class="text-gray-500 text-xs">In Progress</p>
        <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $totalInProgress }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <p class="text-gray-500 text-xs">Resolved</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ $totalResolved }}</p>
    </div>
</div>

{{-- Filter Tabs --}}
<div class="flex gap-2 mb-4 flex-wrap">
    @foreach(['semua' => 'Semua', 'open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'barang' => '📦 Barang', 'dokumen' => '📄 Dokumen'] as $val => $label)
    <button onclick="filterKomplain('{{ $val }}')"
        class="filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold border transition {{ $val == 'semua' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-gray-600 border-gray-300 hover:border-red-400' }}"
        data-filter="{{ $val }}">
        {{ $label }}
    </button>
    @endforeach
</div>

{{-- List Komplain --}}
<div class="space-y-3" id="komplain-list">
    @forelse($komplain as $k)
    <div class="bg-white rounded-xl shadow hover:shadow-md transition komplain-card"
         data-status="{{ $k->status }}" data-jenis="{{ $k->jenis }}">
        <div class="p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <span class="font-mono text-xs text-gray-400">{{ $k->no_komplain }}</span>
                        {{-- Prioritas --}}
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $k->prioritas == 'critical' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $k->prioritas == 'high' ? 'bg-orange-100 text-orange-700' : '' }}
                            {{ $k->prioritas == 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $k->prioritas == 'low' ? 'bg-green-100 text-green-700' : '' }}">
                            {{ $k->prioritas_label }}
                        </span>
                        {{-- Status --}}
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $k->status == 'open' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $k->status == 'in_progress' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $k->status == 'resolved' ? 'bg-green-100 text-green-700' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $k->status)) }}
                        </span>
                        {{-- Jenis --}}
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                            {{ $k->jenis == 'barang' ? '📦 Barang' : '📄 Dokumen' }}
                        </span>
                        @if($k->masih_garansi)
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">🛡️ Garansi</span>
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-800">{{ $k->judul }}</h3>
                    <div class="flex gap-3 mt-1 text-xs text-gray-400 flex-wrap">
                        @if($k->proyek)
                        <span>📁 {{ $k->proyek->nama_proyek }}</span>
                        @endif
                        <span>👤 {{ $k->creator->name ?? '-' }}</span>
                        <span>🕐 {{ $k->created_at->diffForHumans() }}</span>
                        @if($k->handler)
                        <span>🔧 {{ $k->handler->name }}</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('komplain.show', $k) }}"
                   class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-3 py-1 rounded text-xs font-semibold transition whitespace-nowrap">
                    Detail →
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow p-10 text-center text-gray-400">
        Belum ada komplain.
    </div>
    @endforelse
</div>

<script>
function filterKomplain(filter) {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-red-500', 'text-white', 'border-red-500');
        btn.classList.add('bg-white', 'text-gray-600', 'border-gray-300');
    });
    document.querySelector(`[data-filter="${filter}"]`).classList.add('bg-red-500', 'text-white', 'border-red-500');

    document.querySelectorAll('.komplain-card').forEach(card => {
        if (filter === 'semua') {
            card.style.display = 'block';
        } else if (filter === 'barang' || filter === 'dokumen') {
            card.style.display = card.dataset.jenis === filter ? 'block' : 'none';
        } else {
            card.style.display = card.dataset.status === filter ? 'block' : 'none';
        }
    });
}
</script>
@endsection