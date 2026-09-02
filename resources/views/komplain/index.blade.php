@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><svg class="w-6 h-6 inline-block -mt-1 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>Manajemen Komplain</h1>
    <a href="{{ route('komplain.create') }}"
       class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5 w-fit">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg> Buat Komplain
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}</div>
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
    @foreach(['semua' => 'Semua', 'open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'barang' => 'Barang', 'dokumen' => 'Dokumen'] as $val => $label)
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
                            @if($k->jenis == 'barang')<svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg> Barang @else<svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg> Dokumen @endif
                        </span>
                        @if($k->masih_garansi)
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700"><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg> Garansi</span>
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-800">{{ $k->judul }}</h3>
                    <div class="flex gap-3 mt-1 text-xs text-gray-400 flex-wrap">
                        @if($k->proyek)
                        <span><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-19.5 0v6a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25v-6m-19.5 0V6a2.25 2.25 0 0 1 2.25-2.25h5.379a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H19.5A2.25 2.25 0 0 1 21.75 9v3.75" /></svg> {{ $k->proyek->nama_proyek }}</span>
                        @endif
                        <span><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg> {{ $k->creator->name ?? '-' }}</span>
                        <span><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg> {{ $k->created_at->diffForHumans() }}</span>
                        @if($k->handler)
                        <span><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" /></svg> {{ $k->handler->name }}</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('komplain.show', $k) }}"
                   class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5 whitespace-nowrap w-fit">
                    Detail <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
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