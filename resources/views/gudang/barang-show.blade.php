@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-xs text-gray-400 font-mono">{{ $barang->kode_barang }}</p>
        <h1 class="text-2xl font-bold text-gray-800">{{ $barang->nama_barang }}</h1>
        <p class="text-gray-500 text-sm">{{ $barang->kategori->nama ?? '-' }} • {{ $barang->satuan }}
            @if($barang->has_sn)
            <span class="ml-2 bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-semibold">Ada SN</span>
            @endif
        </p>
    </div>
    <a href="{{ route('gudang.index') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
        ← Kembali
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">
    ✅ {{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300">
    ❌ {{ $errors->first() }}
</div>
@endif

{{-- Summary Stok --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-indigo-500">
        <p class="text-gray-500 text-xs">Total Stok</p>
        <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $barang->total_stok }}</p>
        <p class="text-xs text-gray-400">{{ $barang->satuan }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <p class="text-gray-500 text-xs">Total Masuk</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ $stokMasuk->sum('jumlah') }}</p>
        <p class="text-xs text-gray-400">{{ $barang->satuan }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-red-500">
        <p class="text-gray-500 text-xs">Total Keluar</p>
        <p class="text-3xl font-bold text-red-600 mt-1">{{ $stokKeluar->sum('jumlah') }}</p>
        <p class="text-xs text-gray-400">{{ $barang->satuan }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-500">
        <p class="text-gray-500 text-xs">Stok Minimum</p>
        <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $barang->stok_minimum }}</p>
        <p class="text-xs text-gray-400">{{ $barang->satuan }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Form Stok Masuk --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4">📥 Catat Stok Masuk</h2>
        <form method="POST" action="{{ route('gudang.masuk', $barang) }}">
            @csrf
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Tanggal *</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Jumlah *</label>
                        <input type="number" name="jumlah" min="1" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Harga Beli (Rp)</label>
                        <input type="number" name="harga_beli"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Supplier</label>
                        <input type="text" name="supplier"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>
                <div>
                    <label class="text-xs text-gray-500">No. Dokumen (PO/Faktur)</label>
                    <input type="text" name="no_dokumen"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Keterangan</label>
                    <input type="text" name="keterangan"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                @if($barang->has_sn)
                <div>
                    <label class="text-xs text-gray-500">Serial Numbers (1 SN per baris)</label>
                    <textarea name="serial_numbers" rows="4"
                        placeholder="SN001&#10;SN002&#10;SN003"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
                    <p class="text-xs text-gray-400 mt-1">Isi 1 serial number per baris, jumlah harus sama dengan jumlah masuk.</p>
                </div>
                @endif
            </div>
            <button type="submit"
                class="mt-4 w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm font-semibold transition">
                Catat Masuk
            </button>
        </form>
    </div>

    {{-- Form Stok Keluar --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4">📤 Catat Stok Keluar (FIFO)</h2>
        <form method="POST" action="{{ route('gudang.keluar', $barang) }}">
            @csrf
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Tanggal *</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Jumlah *</label>
                        <input type="number" name="jumlah" min="1" max="{{ $barang->total_stok }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Harga Jual (Rp)</label>
                        <input type="number" name="harga_jual"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Tujuan</label>
                        <input type="text" name="tujuan"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Proyek (opsional)</label>
                    <select name="proyek_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">-- Pilih Proyek --</option>
                        @foreach($proyek as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_proyek }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">No. Dokumen</label>
                    <input type="text" name="no_dokumen"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Keterangan</label>
                    <input type="text" name="keterangan"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                @if($barang->has_sn)
                <div>
                    <label class="text-xs text-gray-500">Pilih Serial Number yang Keluar</label>
                    <div class="border border-gray-200 rounded-lg p-2 max-h-36 overflow-y-auto space-y-1">
                        @forelse($barang->serialNumbers->where('status', 'tersedia') as $sn)
                        <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-50 p-1 rounded">
                            <input type="checkbox" name="serial_numbers[]" value="{{ $sn->id }}">
                            <span class="font-mono">{{ $sn->serial_number }}</span>
                            <span class="text-xs text-gray-400">({{ ucfirst($sn->kondisi) }})</span>
                        </label>
                        @empty
                        <p class="text-xs text-gray-400 p-1">Tidak ada SN tersedia.</p>
                        @endforelse
                    </div>
                </div>
                @endif
            </div>
            <button type="submit"
                class="mt-4 w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg text-sm font-semibold transition">
                Catat Keluar (FIFO)
            </button>
        </form>
    </div>
</div>

{{-- Tabel Batch Stok (FIFO view) --}}
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <h2 class="font-semibold text-gray-700 mb-4">📦 Stok per Batch (FIFO)</h2>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Tanggal Masuk</th>
                <th class="px-4 py-3 text-left">Supplier</th>
                <th class="px-4 py-3 text-left">No. Dokumen</th>
                <th class="px-4 py-3 text-center">Jumlah Masuk</th>
                <th class="px-4 py-3 text-center">Sisa</th>
                <th class="px-4 py-3 text-right">Harga Beli</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($stokMasuk as $m)
            <tr class="hover:bg-gray-50 {{ $m->sisa == 0 ? 'opacity-40' : '' }}">
                <td class="px-4 py-3 text-gray-700">{{ $m->tanggal->format('d M Y') }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $m->supplier ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $m->no_dokumen ?? '-' }}</td>
                <td class="px-4 py-3 text-center font-semibold">{{ $m->jumlah }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="font-bold {{ $m->sisa == 0 ? 'text-gray-400' : 'text-indigo-600' }}">
                        {{ $m->sisa }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right text-gray-500">
                    {{ $m->harga_beli ? 'Rp ' . number_format($m->harga_beli, 0, ',', '.') : '-' }}
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Belum ada stok masuk.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Riwayat Keluar --}}
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <h2 class="font-semibold text-gray-700 mb-4">📋 Riwayat Stok Keluar</h2>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Tujuan</th>
                <th class="px-4 py-3 text-left">Proyek</th>
                <th class="px-4 py-3 text-center">Jumlah</th>
                <th class="px-4 py-3 text-right">Harga Jual</th>
                <th class="px-4 py-3 text-left">Dicatat Oleh</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($stokKeluar as $k)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-700">{{ $k->tanggal->format('d M Y') }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $k->tujuan ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $k->proyek->nama_proyek ?? '-' }}</td>
                <td class="px-4 py-3 text-center font-bold text-red-600">{{ $k->jumlah }}</td>
                <td class="px-4 py-3 text-right text-gray-500">
                    {{ $k->harga_jual ? 'Rp ' . number_format($k->harga_jual, 0, ',', '.') : '-' }}
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $k->creator->name ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Belum ada stok keluar.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Daftar Serial Number --}}
@if($barang->has_sn)
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-semibold text-gray-700 mb-4">🔢 Daftar Serial Number</h2>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Serial Number</th>
                <th class="px-4 py-3 text-left">Tgl Masuk</th>
                <th class="px-4 py-3 text-left">Kondisi</th>
                <th class="px-4 py-3 text-center">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($barang->serialNumbers->sortByDesc('created_at') as $sn)
            <tr class="hover:bg-gray-50 {{ $sn->status != 'tersedia' ? 'opacity-50' : '' }}">
                <td class="px-4 py-3 font-mono font-semibold text-gray-800">{{ $sn->serial_number }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $sn->masuk->tanggal->format('d M Y') }}</td>
                <td class="px-4 py-3 text-gray-500 capitalize">{{ $sn->kondisi }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $sn->status == 'tersedia' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $sn->status == 'dipakai' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $sn->status == 'terjual' ? 'bg-gray-100 text-gray-500' : '' }}
                        {{ $sn->status == 'rusak' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($sn->status) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Belum ada SN tercatat.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

@endsection