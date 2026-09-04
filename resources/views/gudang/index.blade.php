@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold text-gray-800"><svg class="w-6 h-6 inline-block -mt-1 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 21v-4.5m0 4.5h3.75m-3.75 0h-9m9-4.5V9m0 7.5H12m3.75-7.5V4.875c0-.621-.504-1.125-1.125-1.125h-3.75c-.621 0-1.125.504-1.125 1.125V9m4.5 0H12m0 0V4.875c0-.621-.504-1.125-1.125-1.125h-3.75c-.621 0-1.125.504-1.125 1.125V9m4.5 0H8.25M12 9v7.5m0-7.5H8.25m4.5 7.5H8.25m0-7.5V21m0-11.625v11.625m0 0H4.875c-.621 0-1.125-.504-1.125-1.125V9.375c0-.621.504-1.125 1.125-1.125H8.25" /></svg>Gudang</h1>
    <div class="flex gap-2">
        <a href="{{ route('gudang.opname') }}"
           class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
            <svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>Opname
        </a>
        <a href="{{ route('gudang.barang.create') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
            + Tambah
        </a>
    </div>
</div>

@if(auth()->user()->isSuperAdmin())
<div class="bg-purple-50 border border-purple-300 text-purple-700 px-4 py-2 rounded-lg mb-4 text-sm font-semibold">
    <svg class="w-4 h-4 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" /></svg> Mode Super Admin — menampilkan data gabungan dari SEMUA company.
</div>
@endif

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}</div>
@endif

{{-- Alert Stok Minimum --}}
@if($alertStok->count() > 0)
<div class="bg-red-50 border border-red-300 rounded-xl p-4 mb-4">
    <p class="text-red-700 font-semibold text-sm mb-2"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>Stok Menipis! ({{ $alertStok->count() }} barang)</p>
    <div class="flex flex-wrap gap-2">
        @foreach($alertStok as $a)
        <a href="{{ route('gudang.barang.show', $a) }}"
           class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold hover:bg-red-200 transition">
            {{ $a->nama_barang }} (sisa {{ $a->total_stok }})
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- Desktop: Tabel --}}
<div class="bg-white rounded-xl shadow overflow-hidden hidden md:block">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Kode</th>
                <th class="px-4 py-3 text-left">Nama Barang</th>
                @if(auth()->user()->isSuperAdmin())
                <th class="px-4 py-3 text-left">Company</th>
                @endif
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-center">Stok</th>
                <th class="px-4 py-3 text-center">Min.</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($barang as $b)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ $b->kode_barang }}</td>
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $b->nama_barang }}</p>
                    <p class="text-xs text-gray-400">{{ $b->satuan }}</p>
                </td>
                @if(auth()->user()->isSuperAdmin())
                <td class="px-4 py-3">
                    <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                        {{ $b->company->nama ?? '-' }}
                    </span>
                </td>
                @endif
                <td class="px-4 py-3 text-gray-500">{{ $b->kategori->nama ?? '-' }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="text-lg font-bold {{ $b->total_stok <= $b->stok_minimum ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $b->total_stok }}
                    </span>
                    <span class="text-xs text-gray-400">{{ $b->satuan }}</span>
                </td>
                <td class="px-4 py-3 text-center text-gray-500">{{ $b->stok_minimum }}</td>
                <td class="px-4 py-3 text-center">
                    @if($b->total_stok <= 0)
                        <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-semibold">Habis</span>
                    @elseif($b->total_stok <= $b->stok_minimum)
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs font-semibold">Menipis</span>
                    @else
                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-semibold">Aman</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex gap-2 justify-center">
                        <a href="{{ route('gudang.barang.show', $b) }}"
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition">Detail</a>
                        <form method="POST" action="{{ route('gudang.barang.destroy', $b) }}"
                            onsubmit="return confirm('Yakin hapus barang ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded text-xs font-semibold transition">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="{{ auth()->user()->isSuperAdmin() ? 8 : 7 }}" class="px-4 py-8 text-center text-gray-400">Belum ada barang.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Mobile: Card List --}}
<div class="space-y-3 md:hidden">
    @forelse($barang as $b)
    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="font-mono text-xs text-gray-400">{{ $b->kode_barang }}</p>
                <p class="font-semibold text-gray-800 mt-0.5">{{ $b->nama_barang }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $b->kategori->nama ?? '-' }} • {{ $b->satuan }}</p>
                @if(auth()->user()->isSuperAdmin())
                <span class="inline-block mt-1 bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-[10px] font-semibold">
                    {{ $b->company->nama ?? '-' }}
                </span>
                @endif
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-2xl font-bold {{ $b->total_stok <= $b->stok_minimum ? 'text-red-600' : 'text-indigo-600' }}">
                    {{ $b->total_stok }}
                </p>
                <p class="text-xs text-gray-400">{{ $b->satuan }}</p>
            </div>
        </div>
        <div class="flex items-center justify-between mt-3">
            @if($b->total_stok <= 0)
                <span style="background:#fee2e2;color:#dc2626;padding:2px 10px;border-radius:999px;font-size:11px;font-weight:600;">Habis</span>
            @elseif($b->total_stok <= $b->stok_minimum)
                <span style="background:#fef9c3;color:#d97706;padding:2px 10px;border-radius:999px;font-size:11px;font-weight:600;">Menipis</span>
            @else
                <span style="background:#dcfce7;color:#16a34a;padding:2px 10px;border-radius:999px;font-size:11px;font-weight:600;">Aman</span>
            @endif
            <div class="flex gap-2">
                <a href="{{ route('gudang.barang.show', $b) }}"
                   style="background:#e0e7ff;color:#4338ca;padding:4px 12px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;">
                    Detail
                </a>
                <form method="POST" action="{{ route('gudang.barang.destroy', $b) }}"
                    onsubmit="return confirm('Yakin hapus barang ini?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        style="background:#fee2e2;color:#dc2626;padding:4px 12px;border-radius:6px;font-size:12px;font-weight:600;border:none;cursor:pointer;">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow p-8 text-center text-gray-400">Belum ada barang.</div>
    @endforelse
</div>

@endsection