@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-4">
    <div class="flex-1 min-w-0 mr-3">
        <p class="text-xs text-gray-400 font-mono">{{ $barang->kode_barang }}</p>
        <h1 class="text-lg font-bold text-gray-800 truncate">{{ $barang->nama_barang }}</h1>
        <p class="text-gray-500 text-xs">{{ $barang->kategori->nama ?? '-' }} • {{ $barang->satuan }}
            @if($barang->has_sn)
            <span class="ml-1 bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-semibold">Ada SN</span>
            @endif
        </p>
    </div>
    <a href="{{ route('gudang.index') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition flex-shrink-0 flex items-center gap-1.5 w-fit">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg> Kembali
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg> {{ $errors->first() }}</div>
@endif

{{-- Summary Stok --}}
<div class="grid grid-cols-2 gap-3 mb-4">
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-indigo-500">
        <p class="text-gray-500 text-xs">Total Stok</p>
        <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $barang->total_stok }}</p>
        <p class="text-xs text-gray-400">{{ $barang->satuan }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
        <p class="text-gray-500 text-xs">Total Masuk</p>
        <p class="text-2xl font-bold text-green-600 mt-1">{{ $stokMasuk->sum('jumlah') }}</p>
        <p class="text-xs text-gray-400">{{ $barang->satuan }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-red-500">
        <p class="text-gray-500 text-xs">Total Keluar</p>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ $stokKeluar->sum('jumlah') }}</p>
        <p class="text-xs text-gray-400">{{ $barang->satuan }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-yellow-500">
        <p class="text-gray-500 text-xs">Stok Minimum</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $barang->stok_minimum }}</p>
        <p class="text-xs text-gray-400">{{ $barang->satuan }}</p>
    </div>
</div>

{{-- Form Stok Masuk & Keluar --}}
<div class="grid grid-cols-1 gap-4 mb-4" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">

    {{-- Form Stok Masuk --}}
    <div class="bg-white rounded-xl shadow p-4">
        <h2 class="font-semibold text-gray-700 mb-3 text-sm"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>Catat Stok Masuk</h2>
        <form method="POST" action="{{ route('gudang.masuk', $barang) }}">
            @csrf
            <div class="space-y-2">
                <div class="grid grid-cols-2 gap-2">
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
                <div class="grid grid-cols-2 gap-2">
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
                    <label class="text-xs text-gray-500">Serial Numbers (1 SN per baris)</label>
                    <textarea name="serial_numbers" rows="3"
                        placeholder="SN001&#10;SN002&#10;SN003"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
                </div>
                @endif
            </div>
            <button type="submit"
                class="mt-3 w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm font-semibold transition">
                Catat Masuk
            </button>
        </form>
    </div>

    {{-- Form Stok Keluar --}}
    <div class="bg-white rounded-xl shadow p-4">
        <h2 class="font-semibold text-gray-700 mb-3 text-sm"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" /></svg>Catat Stok Keluar (FIFO)</h2>
        <form method="POST" action="{{ route('gudang.keluar', $barang) }}">
            @csrf
            <div class="space-y-2">
                <div class="grid grid-cols-2 gap-2">
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
                <div class="grid grid-cols-2 gap-2">
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
                    <label class="text-xs text-gray-500">Proyek</label>
                    <select name="proyek_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 pr-8">
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
                    <label class="text-xs text-gray-500">Pilih Serial Number</label>
                    <div class="border border-gray-200 rounded-lg p-2 max-h-36 overflow-y-auto space-y-1">
                        @forelse($barang->serialNumbers->where('status', 'tersedia') as $sn)
                        <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-50 p-1 rounded">
                            <input type="checkbox" name="serial_numbers[]" value="{{ $sn->id }}">
                            <span class="font-mono">{{ $sn->serial_number }}</span>
                        </label>
                        @empty
                        <p class="text-xs text-gray-400 p-1">Tidak ada SN tersedia.</p>
                        @endforelse
                    </div>
                </div>
                @endif
            </div>
            <button type="submit"
                class="mt-3 w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg text-sm font-semibold transition">
                Catat Keluar (FIFO)
            </button>
        </form>
    </div>
</div>

{{-- Tabel Batch - Desktop --}}
<div class="bg-white rounded-xl shadow p-4 mb-4">
    <h2 class="font-semibold text-gray-700 mb-3 text-sm"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5V18a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18V7.5m18 0A2.25 2.25 0 0 0 18.75 5.25H5.25A2.25 2.25 0 0 0 3 7.5m18 0-8.427 4.764a2.25 2.25 0 0 1-2.146 0L3 7.5" /></svg>Stok per Batch (FIFO)</h2>
    {{-- Desktop --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Tanggal Masuk</th>
                    <th class="px-4 py-3 text-left">Supplier</th>
                    <th class="px-4 py-3 text-left">No. Dok</th>
                    <th class="px-4 py-3 text-center">Masuk</th>
                    <th class="px-4 py-3 text-center">Sisa</th>
                    <th class="px-4 py-3 text-right">Harga Beli</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stokMasuk as $m)
                <tr class="hover:bg-gray-50 {{ $m->sisa == 0 ? 'opacity-40' : '' }}">
                    <td class="px-4 py-3">{{ $m->tanggal->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $m->supplier ?? '-' }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $m->no_dokumen ?? '-' }}</td>
                    <td class="px-4 py-3 text-center font-semibold">{{ $m->jumlah }}</td>
                    <td class="px-4 py-3 text-center font-bold {{ $m->sisa == 0 ? 'text-gray-400' : 'text-indigo-600' }}">{{ $m->sisa }}</td>
                    <td class="px-4 py-3 text-right text-gray-500">{{ $m->harga_beli ? 'Rp ' . number_format($m->harga_beli, 0, ',', '.') : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Belum ada stok masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Mobile --}}
    <div class="md:hidden space-y-2">
        @forelse($stokMasuk as $m)
        <div class="border border-gray-100 rounded-lg p-3 {{ $m->sisa == 0 ? 'opacity-40' : '' }}">
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-700">{{ $m->tanggal->format('d M Y') }}</span>
                <span class="font-bold {{ $m->sisa == 0 ? 'text-gray-400' : 'text-indigo-600' }}">Sisa: {{ $m->sisa }}</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">{{ $m->supplier ?? '-' }} • Masuk: {{ $m->jumlah }}</div>
        </div>
        @empty
        <p class="text-center text-gray-400 text-sm py-3">Belum ada stok masuk.</p>
        @endforelse
    </div>
</div>

{{-- Riwayat Keluar --}}
<div class="bg-white rounded-xl shadow p-4 mb-4">
    <h2 class="font-semibold text-gray-700 mb-3 text-sm"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>Riwayat Stok Keluar</h2>
    {{-- Desktop --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Tujuan</th>
                    <th class="px-4 py-3 text-left">Proyek</th>
                    <th class="px-4 py-3 text-center">Jumlah</th>
                    <th class="px-4 py-3 text-right">Harga Jual</th>
                    <th class="px-4 py-3 text-left">Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stokKeluar as $k)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $k->tanggal->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $k->tujuan ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $k->proyek->nama_proyek ?? '-' }}</td>
                    <td class="px-4 py-3 text-center font-bold text-red-600">{{ $k->jumlah }}</td>
                    <td class="px-4 py-3 text-right text-gray-500">{{ $k->harga_jual ? 'Rp ' . number_format($k->harga_jual, 0, ',', '.') : '-' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $k->creator->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Belum ada stok keluar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Mobile --}}
    <div class="md:hidden space-y-2">
        @forelse($stokKeluar as $k)
        <div class="border border-gray-100 rounded-lg p-3">
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-700">{{ $k->tanggal->format('d M Y') }}</span>
                <span class="font-bold text-red-600">-{{ $k->jumlah }}</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">{{ $k->tujuan ?? '-' }} • {{ $k->proyek->nama_proyek ?? '-' }}</div>
        </div>
        @empty
        <p class="text-center text-gray-400 text-sm py-3">Belum ada stok keluar.</p>
        @endforelse
    </div>
</div>

{{-- Serial Number --}}
@if($barang->has_sn)
<div class="bg-white rounded-xl shadow p-4">
    <h2 class="font-semibold text-gray-700 mb-3 text-sm"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.5-15-6 18m-3-18-6 18" /></svg>Daftar Serial Number</h2>
    <div class="hidden md:block overflow-x-auto">
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
                            {{ $sn->status == 'terjual' ? 'bg-gray-100 text-gray-500' : '' }}
                            {{ $sn->status == 'rusak' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucfirst($sn->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Belum ada SN.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="md:hidden space-y-2">
        @forelse($barang->serialNumbers->sortByDesc('created_at') as $sn)
        <div class="border border-gray-100 rounded-lg p-3 flex justify-between items-center {{ $sn->status != 'tersedia' ? 'opacity-50' : '' }}">
            <span class="font-mono text-sm font-semibold text-gray-800">{{ $sn->serial_number }}</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                {{ $sn->status == 'tersedia' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                {{ ucfirst($sn->status) }}
            </span>
        </div>
        @empty
        <p class="text-center text-gray-400 text-sm py-3">Belum ada SN.</p>
        @endforelse
    </div>
</div>
@endif

@endsection