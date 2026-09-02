@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-xs text-gray-400 font-mono">{{ $po->no_po }}</p>
        <h1 class="text-2xl font-bold text-gray-800">{{ $po->supplier->nama }}</h1>
        <p class="text-gray-500 text-sm">{{ $po->tanggal->format('d M Y') }} • {{ $po->proyek->nama_proyek ?? 'Tanpa Proyek' }}</p>
    </div>
    <a href="{{ route('po.index') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5 w-fit">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg> Kembali
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        {{-- Detail Barang --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5V18a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18V7.5m18 0A2.25 2.25 0 0 0 18.75 5.25H5.25A2.25 2.25 0 0 0 3 7.5m18 0-8.427 4.764a2.25 2.25 0 0 1-2.146 0L3 7.5" /></svg>Detail Barang</h2>
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
            <h2 class="font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15m0-3-3-3m0 0-3 3m3-3v11.25" /></svg>Penerimaan Barang</h2>
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
                        <svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15m0-3-3-3m0 0-3 3m3-3v11.25" /></svg>Catat & Tambah Stok Gudang
                    </button>
                </div>
            </form>
            @else
            <p class="text-sm text-gray-400">Semua barang sudah diterima.</p>
            @endif
        </div>

        {{-- Faktur Beli --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>Faktur Beli</h2>
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
                            <select name="metode" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none pr-8">
                                <option value="transfer">Transfer</option>
                                <option value="tunai">Tunai</option>
                                <option value="cek">Cek</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="mt-2 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                        <svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>Catat Pembayaran
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
                        <svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>Buat Faktur Beli
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    {{-- Kolom Kanan --}}
    <div>
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>Info PO</h2>
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