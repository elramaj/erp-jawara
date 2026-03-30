@extends('layouts.app')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">📊 Laporan Keuangan</h1>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="{{ route('laporan.keuangan') }}" class="flex gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
            <select name="bulan" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
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
            📥 Excel
        </a>
        <a href="{{ route('laporan.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            📄 PDF
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
        <h2 class="font-semibold text-gray-700 mb-4">💰 Pemasukan Bulan Ini</h2>
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
        <h2 class="font-semibold text-gray-700 mb-4">💸 Pengeluaran Bulan Ini</h2>
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