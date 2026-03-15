@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-xs text-gray-400 font-mono">{{ $po->no_po }}</p>
        <h1 class="text-2xl font-bold text-gray-800">{{ $po->supplier->nama }}</h1>
        <p class="text-gray-500 text-sm">{{ $po->tanggal->format('d M Y') }} • {{ $po->proyek->nama_proyek ?? 'Tanpa Proyek' }}</p>
    </div>
    <a href="{{ route('po.index') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
        ← Kembali
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">✅ {{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        {{-- Detail Barang --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">📦 Detail Barang</h2>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-2 text-left">Barang</th>
                        <th class="px-4 py-2 text-center">Dipesan</th>
                        <th class="px-4 py-2 text-center">Diterima</th>
                        <th class="px-4 py-2 text-right">Harga</th>
                        <th class="px-4 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($po->detail as $d)
                    <tr>
                        <td class="px-4 py-2 text-gray-700">{{ $d->barang->nama_barang ?? '-' }}</td>
                        <td class="px-4 py-2 text-center">{{ $d->jumlah }} {{ $d->barang->satuan ?? '' }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="{{ $d->jumlah_diterima >= $d->jumlah ? 'text-green-600' : 'text-yellow-600' }} font-semibold">
                                {{ $d->jumlah_diterima }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-right text-gray-500">Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 text-right font-semibold">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2">
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-right font-bold text-gray-700">Total:</td>
                        <td class="px-4 py-2 text-right font-bold text-indigo-600">Rp {{ number_format($po->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Barang Datang --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">📥 Penerimaan Barang</h2>
            @if(!in_array($po->status, ['selesai', 'batal']))
            <form method="POST" action="{{ route('po.barang_datang', $po) }}">
                @csrf
                <table class="w-full text-sm mb-3">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left">Barang</th>
                            <th class="px-3 py-2 text-center w-28">Jumlah Terima</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($po->detail as $d)
                        <tr>
                            <td class="px-3 py-2 text-gray-700">
                                {{ $d->barang->nama_barang ?? '-' }}
                                <input type="hidden" name="barang_id[]" value="{{ $d->barang_id }}">
                                <span class="text-xs text-gray-400">(Sisa: {{ $d->jumlah - $d->jumlah_diterima }})</span>
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" name="jumlah[]" min="0"
                                    max="{{ $d->jumlah - $d->jumlah_diterima }}"
                                    value="{{ $d->jumlah - $d->jumlah_diterima }}"
                                    class="w-full border border-gray-300 rounded px-2 py-1 text-sm text-center focus:outline-none focus:ring-1 focus:ring-indigo-400">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex gap-3 items-end">
                    <div>
                        <label class="text-xs text-gray-500">Tanggal Terima *</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        📥 Catat & Tambah Stok Gudang
                    </button>
                </div>
            </form>
            @else
            <p class="text-sm text-gray-400">Semua barang sudah diterima.</p>
            @endif
        </div>

        {{-- Faktur Beli --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">🧾 Faktur Beli</h2>
            @forelse($po->fb as $fb)
            <div class="border rounded-lg p-3 mb-3">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-mono text-sm font-semibold text-indigo-600">{{ $fb->no_fb }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $fb->status == 'paid' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $fb->status == 'partial' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $fb->status == 'unpaid' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($fb->status) }}
                    </span>
                </div>
                <div class="grid grid-cols-3 gap-2 text-xs text-gray-500 mb-3">
                    <div>Total: <span class="font-semibold text-gray-700">Rp {{ number_format($fb->total, 0, ',', '.') }}</span></div>
                    <div>Terbayar: <span class="font-semibold text-green-600">Rp {{ number_format($fb->terbayar, 0, ',', '.') }}</span></div>
                    <div>Sisa: <span class="font-semibold text-red-600">Rp {{ number_format($fb->sisa, 0, ',', '.') }}</span></div>
                </div>
                @if($fb->status != 'paid')
                <form method="POST" action="{{ route('fb.bayar', $fb) }}" class="border-t pt-3">
                    @csrf
                    <p class="text-xs font-medium text-gray-600 mb-2">Catat Pembayaran:</p>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="text-xs text-gray-400">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="text-xs text-gray-400">Jumlah</label>
                            <input type="number" name="jumlah" min="1" max="{{ $fb->sisa }}" required
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="text-xs text-gray-400">Metode</label>
                            <select name="metode" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none">
                                <option value="transfer">Transfer</option>
                                <option value="tunai">Tunai</option>
                                <option value="cek">Cek</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="mt-2 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                        💰 Catat Pembayaran
                    </button>
                </form>
                @endif
            </div>
            @empty
            <p class="text-sm text-gray-400">Belum ada Faktur Beli.</p>
            @endforelse

            {{-- Form Buat FB --}}
            @if(!in_array($po->status, ['batal']))
            <div class="border-t pt-4 mt-2">
                <p class="text-sm font-medium text-gray-700 mb-3">+ Buat Faktur Beli</p>
                <form method="POST" action="{{ route('po.fb.store', $po) }}">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-500">Tanggal *</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Jatuh Tempo</label>
                            <input type="date" name="jatuh_tempo"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Total FB: <strong>Rp {{ number_format($po->total, 0, ',', '.') }}</strong></p>
                    <button type="submit" class="mt-3 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        🧾 Buat Faktur Beli
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    {{-- Kolom Kanan --}}
    <div>
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">📋 Info PO</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-xs text-gray-400">Status</p>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $po->status == 'confirmed' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $po->status == 'partial' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $po->status == 'selesai' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $po->status == 'batal' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($po->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Supplier</p>
                    <p class="font-medium text-gray-700">{{ $po->supplier->nama ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $po->supplier->telepon ?? '' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Total Nilai</p>
                    <p class="font-bold text-indigo-600">Rp {{ number_format($po->total, 0, ',', '.') }}</p>
                </div>
                @if($po->catatan)
                <div>
                    <p class="text-xs text-gray-400">Catatan</p>
                    <p class="text-gray-600">{{ $po->catatan }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs text-gray-400">Dibuat oleh</p>
                    <p class="text-gray-600">{{ $po->creator->name ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection