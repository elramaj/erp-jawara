@extends('layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('penerimaan.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2">Penerimaan Barang · {{ $po->no_po }}</h1>
    <p class="text-sm text-gray-500 mt-1">{{ $po->supplier->nama ?? '-' }}{{ $po->proyek ? ' · ' . $po->proyek->nama_proyek : '' }}</p>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="bg-white rounded-xl shadow p-6 max-w-4xl">
    @if($terbuka)
    <form method="POST" action="{{ route('penerimaan.store', $po) }}">
        @csrf
        <table class="w-full text-sm mb-4">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-3 py-2 text-left">Barang</th>
                    <th class="px-3 py-2 text-center">Dipesan</th>
                    <th class="px-3 py-2 text-center">Sudah Datang</th>
                    <th class="px-3 py-2 text-center">Sisa</th>
                    <th class="px-3 py-2 text-center w-32">Datang Sekarang</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($po->detail as $i => $d)
                @php $sisa = max(0, (int) $d->jumlah - (int) $d->jumlah_diterima); @endphp
                <tr>
                    <td class="px-3 py-2 text-gray-800 align-top">
                        {{ $d->barang->nama_barang ?? '-' }}
                        <span class="text-xs text-gray-400">{{ $d->barang->satuan ?? '' }}</span>
                        <input type="hidden" name="barang_id[{{ $i }}]" value="{{ $d->barang_id }}">
                        @if($sisa > 0 && ($d->barang->has_sn ?? false))
                        <textarea name="serial_numbers[{{ $i }}]" rows="2" placeholder="Serial number, satu per baris (opsional, jumlahnya harus sama dengan yang datang)"
                            class="mt-2 w-full border border-gray-300 rounded px-2 py-1 text-xs">{{ old("serial_numbers.$i") }}</textarea>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-center text-gray-600 align-top">{{ $d->jumlah }}</td>
                    <td class="px-3 py-2 text-center text-gray-600 align-top">{{ $d->jumlah_diterima }}</td>
                    <td class="px-3 py-2 text-center font-semibold {{ $sisa > 0 ? 'text-orange-600' : 'text-green-600' }} align-top">{{ $sisa > 0 ? $sisa : 'Lengkap' }}</td>
                    <td class="px-3 py-2 align-top">
                        @if($sisa > 0)
                        <input type="number" name="jumlah[{{ $i }}]" min="0" max="{{ $sisa }}" value="{{ old("jumlah.$i", $sisa) }}"
                            class="w-full border border-gray-300 rounded px-2 py-1 text-sm text-center focus:outline-none focus:ring-1 focus:ring-indigo-400">
                        @else
                        <input type="hidden" name="jumlah[{{ $i }}]" value="0"><span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="flex gap-3 items-end">
            <div>
                <label class="text-xs text-gray-500">Tanggal Terima *</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition">Simpan Penerimaan</button>
        </div>
        <p class="text-xs text-gray-400 mt-3">Kosongkan atau isi 0 untuk barang yang belum datang. Jumlah tidak boleh melebihi sisa PO.</p>
    </form>
    @else
    <p class="text-sm text-gray-500 mb-3">PO ini berstatus <strong>{{ $po->status }}</strong>, sehingga tidak menerima barang lagi.</p>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 uppercase"><tr><th class="px-3 py-2 text-left">Barang</th><th class="px-3 py-2 text-center">Dipesan</th><th class="px-3 py-2 text-center">Datang</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($po->detail as $d)
            <tr><td class="px-3 py-2 text-gray-800">{{ $d->barang->nama_barang ?? '-' }}</td><td class="px-3 py-2 text-center">{{ $d->jumlah }}</td><td class="px-3 py-2 text-center">{{ $d->jumlah_diterima }}</td></tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
