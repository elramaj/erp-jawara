@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><svg class="w-6 h-6 inline-block -mt-1 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>Pengeluaran (Beban Operasional)</h1>
    <button onclick="document.getElementById('form-tambah').classList.toggle('hidden')"
        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg> Tambah Beban
    </button>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}
</div>
@endif

@if(auth()->user()->isSuperAdmin())
<div class="bg-purple-50 border border-purple-300 text-purple-700 px-4 py-2 rounded-lg mb-4 text-sm font-semibold flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" /></svg>
    Mode Super Admin — menampilkan data gabungan dari SEMUA company.
</div>
@endif

{{-- Form Tambah (collapsible) --}}
<div id="form-tambah" class="bg-white rounded-xl shadow p-6 mb-6 hidden">
    <h2 class="font-semibold text-gray-700 mb-4">Catat Beban Operasional Baru</h2>
    <form method="POST" action="{{ route('pengeluaran.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        @csrf
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Kategori *</label>
            <select name="kategori" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                @foreach(\App\Models\BebanOperasional::KATEGORI as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal *</label>
            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Nominal (Rp) *</label>
            <input type="number" name="nominal" min="1" required placeholder="0"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Keterangan</label>
            <input type="text" name="keterangan" placeholder="Opsional"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            Simpan
        </button>
    </form>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="{{ route('pengeluaran.index') }}" class="flex gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
            <select name="bulan" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 pr-8">
                @foreach(range(1,12) as $b)
                <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                    {{ Carbon\Carbon::createFromDate($tahun, $b, 1)->translatedFormat('F') }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
            <select name="tahun" class="border border-gray-300 rounded-lg px-3 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                @foreach(range(2024, 2027) as $t)
                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            Tampilkan
        </button>
    </form>
</div>

{{-- Summary --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-red-500 md:col-span-1">
        <p class="text-gray-500 text-xs">Total Beban Bulan Ini</p>
        <p class="text-xl font-bold text-red-600 mt-1">Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 md:col-span-2">
        <p class="text-gray-500 text-xs mb-2">Breakdown per Kategori</p>
        <div class="flex flex-wrap gap-2">
            @forelse($perKategori as $kategori => $total)
            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-semibold">
                {{ \App\Models\BebanOperasional::KATEGORI[$kategori] ?? $kategori }}:
                Rp {{ number_format($total, 0, ',', '.') }}
            </span>
            @empty
            <span class="text-gray-400 text-xs">Belum ada beban tercatat bulan ini.</span>
            @endforelse
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Tanggal</th>
                @if(auth()->user()->isSuperAdmin())
                <th class="px-4 py-3 text-left">Company</th>
                @endif
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-left">Keterangan</th>
                <th class="px-4 py-3 text-left">Dicatat oleh</th>
                <th class="px-4 py-3 text-right">Nominal</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($beban as $b)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-500">{{ $b->tanggal->format('d M Y') }}</td>
                @if(auth()->user()->isSuperAdmin())
                <td class="px-4 py-3">
                    <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                        {{ $b->company->nama ?? '-' }}
                    </span>
                </td>
                @endif
                <td class="px-4 py-3">
                    <span class="bg-red-50 text-red-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                        {{ \App\Models\BebanOperasional::KATEGORI[$b->kategori] ?? $b->kategori }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $b->keterangan ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $b->creator->name ?? '-' }}</td>
                <td class="px-4 py-3 text-right font-semibold text-red-600">
                    Rp {{ number_format($b->nominal, 0, ',', '.') }}
                </td>
                <td class="px-4 py-3 text-center">
                    <form method="POST" action="{{ route('pengeluaran.destroy', $b) }}" onsubmit="return confirm('Hapus catatan beban ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="{{ auth()->user()->isSuperAdmin() ? 7 : 6 }}" class="px-4 py-8 text-center text-gray-400">Belum ada beban operasional tercatat bulan ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection