@extends('layouts.app')

@section('content')
@php $s = $t->status; $bisaEdit = $kelola && in_array($s, ['antrian', 'disiapkan', 'tayang']); @endphp

<div class="flex justify-between items-start mb-6">
    <div>
        <a href="{{ route('tayang.index') }}" class="text-xs text-gray-400 hover:text-gray-600">&larr; Kembali ke Tayang</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ $t->judul }}</h1>
        <p class="text-sm text-gray-500">
            {{ $t->permintaan->no_permintaan ?? '' }}
            @if($bisaLihatPermintaan && $t->permintaan)<a href="{{ route('produk.show', $t->permintaan) }}" class="text-indigo-600 hover:underline">(lihat permintaan)</a>@endif
            · Sales: {{ $t->permintaan->creator->name ?? '-' }}
        </p>
    </div>
    <div>@include('tayang._badge', ['status' => $s])</div>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300 text-sm">
    <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        {{-- Barang yang dikonfirmasi Sales --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-800 mb-3">Barang yang Ditayangkan</h2>
            <div class="space-y-3">
                @foreach($t->permintaan->item as $item)
                @php $o = $item->opsiTerpilih; @endphp
                <div class="border rounded-lg p-3">
                    <div class="flex justify-between">
                        <p class="font-medium text-gray-800">{{ $item->nama_kebutuhan }}</p>
                        <span class="text-sm text-gray-500">{{ $item->jumlah }} {{ $item->satuan }}</span>
                    </div>
                    @if($item->spesifikasi)<p class="text-xs text-gray-500 whitespace-pre-line mt-1">Spesifikasi diminta: {{ $item->spesifikasi }}</p>@endif
                    @if($o)
                    <p class="text-sm text-gray-700 mt-1">{{ $o->nama_barang }}@if($o->merk) · {{ $o->merk }}@endif</p>
                    @if($o->spesifikasi_ditawarkan)<p class="text-sm text-gray-600 whitespace-pre-line mt-1">{{ $o->spesifikasi_ditawarkan }}</p>@endif
                    <p class="text-sm font-semibold text-gray-800 mt-1">{{ $o->harga_jual !== null ? 'Rp ' . number_format($o->harga_jual, 0, ',', '.') : 'Harga belum diisi' }}
                        @if($lihatHargaBeli && $o->harga_beli !== null)<span class="text-xs font-normal text-gray-400">· beli Rp {{ number_format($o->harga_beli, 0, ',', '.') }}</span>@endif</p>
                    @if($o->link_sumber)<a href="{{ $o->link_sumber }}" target="_blank" rel="noopener" class="text-xs text-indigo-600 hover:underline break-all">{{ $o->link_sumber }}</a>@endif
                    @else
                    <p class="text-sm text-gray-400 mt-1">Belum ada opsi terpilih.</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Form listing --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-800 mb-3">Listing & Kebutuhan Desain</h2>

            @if($bisaEdit)
            <form id="form-tayang" method="POST" action="{{ route('tayang.update', $t) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Platform</label>
                    <input type="text" name="platform" list="daftar-platform" value="{{ old('platform', $t->platform) }}" placeholder="E-katalog, marketplace, website..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <datalist id="daftar-platform"><option value="E-Katalog LKPP"><option value="Website"><option value="Marketplace"><option value="Blibli"><option value="Tokopedia"></datalist>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Link Tayang</label>
                    <input type="url" name="link_tayang" value="{{ old('link_tayang', $t->link_tayang) }}" placeholder="https://..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="perlu_desain" value="1" {{ old('perlu_desain', $t->perlu_desain) ? 'checked' : '' }}>
                        Butuh bantuan tim Desain (foto produk, banner, deskripsi visual)
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Brief untuk Desain</label>
                    <textarea name="catatan_desain" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('catatan_desain', $t->catatan_desain) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Catatan Tayang</label>
                    <textarea name="catatan_tayang" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('catatan_tayang', $t->catatan_tayang) }}</textarea>
                </div>
                <div class="md:col-span-2"><button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">Simpan</button></div>
            </form>
            @else
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-xs text-gray-400">Platform</dt><dd class="font-medium text-gray-800">{{ $t->platform ?: '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Link Tayang</dt><dd>
                    @if($t->link_tayang)<a href="{{ $t->link_tayang }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline break-all">{{ $t->link_tayang }}</a>@else - @endif</dd></div>
                <div class="col-span-2"><dt class="text-xs text-gray-400">Butuh Desain</dt><dd class="font-medium text-gray-800">{{ $t->perlu_desain ? 'Ya' : 'Tidak' }}</dd></div>
                @if($t->catatan_desain)<div class="col-span-2"><dt class="text-xs text-gray-400">Brief untuk Desain</dt><dd class="text-gray-800 whitespace-pre-line">{{ $t->catatan_desain }}</dd></div>@endif
                @if($t->catatan_tayang)<div class="col-span-2"><dt class="text-xs text-gray-400">Catatan Tayang</dt><dd class="text-gray-800 whitespace-pre-line">{{ $t->catatan_tayang }}</dd></div>@endif
            </dl>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow p-5 space-y-3">
            <h3 class="font-semibold text-gray-800">Aksi</h3>

            @if($kelola && $s === 'antrian')
            <form method="POST" action="{{ route('tayang.mulai', $t) }}">@csrf
                <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Mulai Siapkan</button>
            </form>
            @endif

            @if($kelola && in_array($s, ['antrian', 'disiapkan']))
            <form method="POST" action="{{ route('tayang.tayangkan', $t) }}" onsubmit="return confirm('Tandai sudah tayang? Pastikan link sudah benar.')">@csrf
                <input type="hidden" name="platform" value="{{ $t->platform }}">
                <input type="hidden" name="link_tayang" value="{{ $t->link_tayang }}">
                <button class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Tandai Sudah Tayang</button>
            </form>
            <p class="text-xs text-gray-500">Simpan link tayang di formulir dulu, lalu tandai sudah tayang.</p>
            @endif

            @if($kelola && $s === 'tayang')
            <form method="POST" action="{{ route('tayang.turunkan', $t) }}" onsubmit="return confirm('Turunkan tayang ini?')">@csrf
                <button class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-semibold transition">Turunkan Tayang</button>
            </form>
            @endif

            @if(!$kelola)<p class="text-sm text-gray-400">Hanya dapat dilihat (tim Tayang yang mengubah).</p>@endif

            @if($t->link_tayang && $s === 'tayang')
            <a href="{{ $t->link_tayang }}" target="_blank" rel="noopener" class="block text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg text-sm font-semibold transition">Buka Link Tayang</a>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow p-5 text-sm space-y-1">
            <h3 class="font-semibold text-gray-800 mb-2">Riwayat</h3>
            <p>Masuk antrian · {{ $t->created_at->format('d M Y H:i') }}</p>
            @if($t->penangan)<p>Ditangani <strong>{{ $t->penangan->name }}</strong></p>@endif
            @if($t->tayang_at)<p>Tayang · {{ $t->tayang_at->format('d M Y H:i') }}</p>@endif
            @if($t->turun_at)<p>Diturunkan · {{ $t->turun_at->format('d M Y H:i') }}</p>@endif
        </div>
    </div>
</div>
@endsection
