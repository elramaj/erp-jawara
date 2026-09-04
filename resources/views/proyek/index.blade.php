@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><svg class="w-6 h-6 inline-block -mt-1 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-19.5 0v6a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25v-6m-19.5 0V6a2.25 2.25 0 0 1 2.25-2.25h5.379a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H19.5A2.25 2.25 0 0 1 21.75 9v3.75" /></svg>Manajemen Proyek</h1>
    @if(in_array(auth()->user()->role_id, [1, 10, 11]))
    <a href="{{ route('proyek.create') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
        + Tambah Proyek
    </a>
    @endif
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}
</div>
@endif

@if(auth()->user()->isSuperAdmin())
<div class="bg-purple-50 border border-purple-300 text-purple-700 px-4 py-2 rounded-lg mb-4 text-sm font-semibold">
    <svg class="w-4 h-4 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" /></svg> Mode Super Admin — menampilkan data gabungan dari SEMUA company.
</div>
@endif

{{-- Filter Status --}}
<div class="flex gap-2 mb-4">
@foreach(['semua' => 'Semua', 'bola_liar' => '<svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" /></svg> Bola Liar', 'aktif' => 'Aktif', 'draft' => 'Draft', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $val => $label)
    <button onclick="filterProyek('{{ $val }}')"
        class="filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold border transition {{ $val == 'semua' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400' }}"
        data-filter="{{ $val }}">
        {!! $label !!}
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
                    {!! $p->status == 'bola_liar' ? '<svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" /></svg> Bola Liar' : ucfirst($p->status) !!}
                </span>
            </div>
            <h3 class="font-semibold text-gray-800 mb-1">{{ $p->nama_proyek }}</h3>
            <p class="text-sm text-gray-500 mb-3"><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg> {{ $p->klien }}</p>
            @if(auth()->user()->isSuperAdmin())
            <span class="inline-block mb-3 bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                {{ $p->company->nama ?? '-' }}
            </span>
            @endif

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
                <span><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg> {{ $p->anggota->count() }} anggota</span>
            </div>
        </div>
        <div class="border-t px-5 py-3 flex justify-between items-center">
            <span class="text-xs text-gray-400">
                {{ $p->nilai_kontrak ? 'Rp ' . number_format($p->nilai_kontrak, 0, ',', '.') : 'Nilai belum diset' }}
            </span>
            <a href="{{ route('proyek.show', $p) }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition">
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