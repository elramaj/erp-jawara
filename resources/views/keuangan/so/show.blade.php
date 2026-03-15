@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-xs text-gray-400 font-mono">{{ $so->no_so }}</p>
        <h1 class="text-2xl font-bold text-gray-800">{{ $so->customer->nama }}</h1>
        <p class="text-gray-500 text-sm">{{ $so->tanggal->format('d M Y') }} • {{ $so->proyek->nama_proyek ?? 'Tanpa Proyek' }}</p>
    </div>
    <a href="{{ route('so.index') }}"
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
                        <th class="px-4 py-2 text-center">Jumlah</th>
                        <th class="px-4 py-2 text-right">Harga</th>
                        <th class="px-4 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($so->detail as $d)
                    <tr>
                        <td class="px-4 py-2 text-gray-700">{{ $d->barang->nama_barang ?? '-' }}</td>
                        <td class="px-4 py-2 text-center">{{ $d->jumlah }} {{ $d->barang->satuan ?? '' }}</td>
                        <td class="px-4 py-2 text-right text-gray-500">Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 text-right font-semibold">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2">
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-right font-bold text-gray-700">Total:</td>
                        <td class="px-4 py-2 text-right font-bold text-indigo-600">Rp {{ number_format($so->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Surat Jalan --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">🚚 Surat Jalan</h2>
            @forelse($so->sj as $sj)
            <div class="border rounded-lg p-3 mb-2">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-mono text-sm font-semibold text-indigo-600">{{ $sj->no_sj }}</span>
                    <span class="text-xs text-gray-400">{{ $sj->tanggal->format('d M Y') }}</span>
                </div>
                @foreach($sj->detail as $d)
                <p class="text-xs text-gray-500">• {{ $d->barang->nama_barang ?? '-' }} ({{ $d->jumlah }} {{ $d->barang->satuan ?? '' }})</p>
                @endforeach
            </div>
            @empty
            <p class="text-sm text-gray-400">Belum ada Surat Jalan.</p>
            @endforelse

            {{-- Form Buat SJ --}}
            @if(!in_array($so->status, ['selesai', 'batal']))
            <div class="border-t pt-4 mt-4">
                <p class="text-sm font-medium text-gray-700 mb-3">+ Buat Surat Jalan</p>
                <form method="POST" action="{{ route('so.sj.store', $so) }}">
                    @csrf
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="text-xs text-gray-500">Tanggal *</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Catatan</label>
                            <input type="text" name="catatan"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                    </div>
                    <table class="w-full text-sm mb-3">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                            <tr>
                                <th class="px-3 py-2 text-left">Barang</th>
                                <th class="px-3 py-2 text-center w-28">Jumlah Kirim</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($so->detail as $d)
                            <tr>
                                <td class="px-3 py-2 text-gray-700">
                                    {{ $d->barang->nama_barang ?? '-' }}
                                    <input type="hidden" name="barang_id[]" value="{{ $d->barang_id }}">
                                    <span class="text-xs text-gray-400">(Stok: {{ $d->barang->total_stok ?? 0 }})</span>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="jumlah[]" min="0" max="{{ $d->jumlah }}" value="{{ $d->jumlah }}"
                                        class="w-full border border-gray-300 rounded px-2 py-1 text-sm text-center focus:outline-none focus:ring-1 focus:ring-indigo-400">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        🚚 Buat SJ & Kurangi Stok
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- Faktur Jual --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">🧾 Faktur Jual</h2>
            @forelse($so->fj as $fj)
            <div class="border rounded-lg p-3 mb-3">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-mono text-sm font-semibold text-indigo-600">{{ $fj->no_fj }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $fj->status == 'paid' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $fj->status == 'partial' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $fj->status == 'unpaid' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($fj->status) }}
                    </span>
                </div>
                <div class="grid grid-cols-3 gap-2 text-xs text-gray-500 mb-3">
                    <div>Total: <span class="font-semibold text-gray-700">Rp {{ number_format($fj->total, 0, ',', '.') }}</span></div>
                    <div>Terbayar: <span class="font-semibold text-green-600">Rp {{ number_format($fj->terbayar, 0, ',', '.') }}</span></div>
                    <div>Sisa: <span class="font-semibold text-red-600">Rp {{ number_format($fj->sisa, 0, ',', '.') }}</span></div>
                </div>
                @if($fj->status != 'paid')
                <form method="POST" action="{{ route('fj.bayar', $fj) }}" class="border-t pt-3">
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
                            <input type="number" name="jumlah" min="1" max="{{ $fj->sisa }}" required
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
            <p class="text-sm text-gray-400">Belum ada Faktur Jual.</p>
            @endforelse

            {{-- Form Buat FJ --}}
            @if(!in_array($so->status, ['selesai', 'batal']))
            <div class="border-t pt-4 mt-2">
                <p class="text-sm font-medium text-gray-700 mb-3">+ Buat Faktur Jual</p>
                <form method="POST" action="{{ route('so.fj.store', $so) }}">
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
                    <p class="text-xs text-gray-400 mt-2">Total FJ: <strong>Rp {{ number_format($so->total, 0, ',', '.') }}</strong></p>
                    <button type="submit" class="mt-3 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        🧾 Buat Faktur Jual
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    {{-- Kolom Kanan --}}
    <div>
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">📋 Info SO</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-xs text-gray-400">Status</p>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $so->status == 'confirmed' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $so->status == 'partial' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $so->status == 'selesai' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $so->status == 'batal' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($so->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Customer</p>
                    <p class="font-medium text-gray-700">{{ $so->customer->nama ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $so->customer->telepon ?? '' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Total Nilai</p>
                    <p class="font-bold text-indigo-600">Rp {{ number_format($so->total, 0, ',', '.') }}</p>
                </div>
                @if($so->catatan)
                <div>
                    <p class="text-xs text-gray-400">Catatan</p>
                    <p class="text-gray-600">{{ $so->catatan }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs text-gray-400">Dibuat oleh</p>
                    <p class="text-gray-600">{{ $so->creator->name ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection