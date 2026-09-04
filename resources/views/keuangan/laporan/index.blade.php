@extends('layouts.app')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><svg class="w-6 h-6 inline-block -mt-1 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>Laporan Keuangan</h1>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="{{ route('laporan.keuangan') }}" class="flex gap-3 items-end">
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
        <a href="{{ route('laporan.excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            <svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>Excel
        </a>
        <a href="{{ route('laporan.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            <svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>PDF
        </a>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <p class="text-gray-500 text-xs">Pemasukan</p>
        <p class="text-xl font-bold text-green-600 mt-1">Rp {{ number_format($pemasukan, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-red-500">
        <p class="text-gray-500 text-xs">Pengeluaran</p>
        <p class="text-xl font-bold text-red-600 mt-1">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 {{ $labaRugi >= 0 ? 'border-indigo-500' : 'border-orange-500' }}">
        <p class="text-gray-500 text-xs">{{ $labaRugi >= 0 ? 'Laba' : 'Rugi' }}</p>
        <p class="text-xl font-bold {{ $labaRugi >= 0 ? 'text-indigo-600' : 'text-orange-600' }} mt-1">
            Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}
        </p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-500">
        <p class="text-gray-500 text-xs">Piutang</p>
        <p class="text-xl font-bold text-yellow-600 mt-1">Rp {{ number_format($piutang, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-500">
        <p class="text-gray-500 text-xs">Hutang</p>
        <p class="text-xl font-bold text-purple-600 mt-1">Rp {{ number_format($hutang, 0, ',', '.') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Riwayat Pemasukan --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>Pemasukan Bulan Ini</h2>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-3 py-2 text-left">Tanggal</th>
                    <th class="px-3 py-2 text-left">Customer</th>
                    <th class="px-3 py-2 text-left">No FJ</th>
                    <th class="px-3 py-2 text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($riwayatMasuk as $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-2 text-gray-500">{{ $r->tanggal->format('d M') }}</td>
                    <td class="px-3 py-2 text-gray-700">{{ $r->fj->so->customer->nama ?? '-' }}</td>
                    <td class="px-3 py-2 font-mono text-xs text-indigo-600">{{ $r->fj->no_fj ?? '-' }}</td>
                    <td class="px-3 py-2 text-right font-semibold text-green-600">
                        Rp {{ number_format($r->jumlah, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-3 py-6 text-center text-gray-400">Belum ada pemasukan bulan ini.</td></tr>
                @endforelse
            </tbody>
            @if($riwayatMasuk->count() > 0)
            <tfoot class="border-t-2">
                <tr>
                    <td colspan="3" class="px-3 py-2 text-right font-bold text-gray-700">Total:</td>
                    <td class="px-3 py-2 text-right font-bold text-green-600">Rp {{ number_format($pemasukan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    {{-- Riwayat Pengeluaran --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>Pengeluaran Bulan Ini</h2>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-3 py-2 text-left">Tanggal</th>
                    <th class="px-3 py-2 text-left">Supplier</th>
                    <th class="px-3 py-2 text-left">No FB</th>
                    <th class="px-3 py-2 text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($riwayatKeluar as $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-2 text-gray-500">{{ $r->tanggal->format('d M') }}</td>
                    <td class="px-3 py-2 text-gray-700">{{ $r->fb->po->supplier->nama ?? '-' }}</td>
                    <td class="px-3 py-2 font-mono text-xs text-indigo-600">{{ $r->fb->no_fb ?? '-' }}</td>
                    <td class="px-3 py-2 text-right font-semibold text-red-600">
                        Rp {{ number_format($r->jumlah, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-3 py-6 text-center text-gray-400">Belum ada pengeluaran bulan ini.</td></tr>
                @endforelse
            </tbody>
            @if($riwayatKeluar->count() > 0)
            <tfoot class="border-t-2">
                <tr>
                    <td colspan="3" class="px-3 py-2 text-right font-bold text-gray-700">Total:</td>
                    <td class="px-3 py-2 text-right font-bold text-red-600">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection