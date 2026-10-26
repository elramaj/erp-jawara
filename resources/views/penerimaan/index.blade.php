@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Penerimaan Barang</h1>
    <p class="text-xs text-gray-400 mt-1">Catat barang yang datang dari supplier berdasarkan PO. Stok gudang dan status PO ikut diperbarui otomatis.</p>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">{{ session('success') }}</div>
@endif

<div class="flex gap-2 mb-4 flex-wrap">
    @foreach(['menunggu' => 'Menunggu Barang', 'selesai' => 'Sudah Lengkap', 'semua' => 'Semua'] as $val => $label)
    <a href="{{ route('penerimaan.index', ['tampil' => $val]) }}"
       class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition {{ $filter == $val ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">PO</th>
                <th class="px-4 py-3 text-left">Supplier</th>
                <th class="px-4 py-3 text-left">Proyek</th>
                <th class="px-4 py-3 text-left">Tanggal PO</th>
                <th class="px-4 py-3 text-left">Barang Datang</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($daftar as $po)
            @php
                $dipesan = (int) $po->detail->sum('jumlah');
                $datang  = (int) $po->detail->sum(fn($d) => min((int) $d->jumlah_diterima, (int) $d->jumlah));
                $persen  = $dipesan > 0 ? (int) floor($datang / $dipesan * 100) : 0;
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $po->no_po }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $po->supplier->nama ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $po->proyek->nama_proyek ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $po->tanggal ? $po->tanggal->format('d M Y') : '-' }}</td>
                <td class="px-4 py-3 w-56">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-200 rounded-full h-2"><div class="h-2 rounded-full {{ $persen >= 100 ? 'bg-green-500' : 'bg-indigo-500' }}" style="width: {{ $persen }}%"></div></div>
                        <span class="text-xs text-gray-500 whitespace-nowrap">{{ $datang }}/{{ $dipesan }}</span>
                    </div>
                </td>
                <td class="px-4 py-3"><a href="{{ route('penerimaan.show', $po) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded text-xs font-semibold transition">{{ in_array($po->status, ['confirmed','partial']) ? 'Catat Barang' : 'Lihat' }}</a></td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Tidak ada PO.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
