@extends('layouts.app')

@section('content')
@php $s = $p->status; @endphp

<div class="flex justify-between items-start mb-6">
    <div>
        <a href="{{ route('produk.index') }}" class="text-xs text-gray-400 hover:text-gray-600">&larr; Kembali ke Permintaan Produk</a>
        <p class="text-xs font-mono text-gray-400 mt-1">{{ $p->no_permintaan }}</p>
        <h1 class="text-2xl font-bold text-gray-800">{{ $p->judul }}</h1>
    </div>
    <div>@include('produk._badge', ['status' => $s])</div>
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

@if($s === 'dicari' && $p->alasan_revisi)
<div class="bg-orange-50 border border-orange-300 text-orange-800 rounded-xl p-4 mb-4">
    <p class="font-semibold">Sales meminta opsi lain</p>
    <p class="text-sm mt-1">{{ $p->alasan_revisi }}</p>
</div>
@endif
@if($s === 'dibatalkan')
<div class="bg-gray-100 border border-gray-300 text-gray-700 rounded-xl p-4 mb-4">
    <p class="font-semibold">Permintaan dibatalkan</p>
    <p class="text-sm mt-1">{{ $p->alasan_batal }}</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        {{-- Info --}}
        <div class="bg-white rounded-xl shadow p-6">
            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div><dt class="text-xs text-gray-400">Sales</dt><dd class="font-medium text-gray-800">{{ $p->creator->name ?? '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">PIC Produk</dt><dd class="font-medium text-gray-800">{{ $p->picProduk->name ?? '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Proyek</dt><dd class="font-medium text-gray-800">
                    @if($p->proyek) {{ $p->proyek->kode_proyek }} — {{ $p->proyek->nama_proyek }} @else - @endif</dd></div>
                <div><dt class="text-xs text-gray-400">Customer</dt><dd class="font-medium text-gray-800">{{ $p->customer->nama ?? '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Sumber</dt><dd class="font-medium text-gray-800">{{ $p->sumber ?: '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Opsi dibutuhkan paling lambat</dt><dd class="font-medium text-gray-800">{{ $p->target_tanggal ? $p->target_tanggal->format('d M Y') : '-' }}</dd></div>
                @if($p->catatan)
                <div class="col-span-2"><dt class="text-xs text-gray-400">Catatan Sales</dt><dd class="text-gray-800 whitespace-pre-line">{{ $p->catatan }}</dd></div>
                @endif
            </dl>
        </div>

        {{-- Item + opsi --}}
        @if($s === 'opsi_siap' && $bisaKonfirmasi)
        <form id="form-konfirmasi" method="POST" action="{{ route('produk.konfirmasi', $p) }}">
            @csrf
        @endif

        @foreach($p->item as $n => $item)
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-xs text-gray-400">Item {{ $n + 1 }}</p>
                    <h3 class="font-semibold text-gray-800">{{ $item->nama_kebutuhan }}</h3>
                </div>
                <span class="text-sm font-semibold text-gray-600">{{ $item->jumlah }} {{ $item->satuan }}</span>
            </div>
            @if($item->spesifikasi)
            <div class="bg-gray-50 border rounded-lg p-3 text-sm text-gray-700 whitespace-pre-line mb-4">{{ $item->spesifikasi }}</div>
            @endif

            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold mb-2">Opsi barang ({{ $item->opsi->count() }})</p>

            @forelse($item->opsi as $o)
            @if($s === 'dikonfirmasi' && !$o->is_terpilih) @continue @endif
            <div class="border rounded-lg p-3 mb-2 {{ $o->is_terpilih ? 'border-green-400 bg-green-50' : '' }}">
                <div class="flex justify-between gap-3">
                    <div class="flex gap-3">
                        @if($s === 'opsi_siap' && $bisaKonfirmasi)
                        <input type="radio" name="pilih[{{ $item->id }}]" value="{{ $o->id }}" class="mt-1" required>
                        @endif
                        <div>
                            <p class="font-medium text-gray-800">
                                {{ $o->nama_barang }}
                                @if($o->merk)<span class="text-gray-500 font-normal">· {{ $o->merk }}</span>@endif
                                @if($o->is_terpilih)<span class="ml-2 bg-green-600 text-white px-2 py-0.5 rounded-full text-xs">Dipilih</span>@endif
                            </p>
                            @if($o->spesifikasi_ditawarkan)
                            <p class="text-sm text-gray-600 whitespace-pre-line mt-1">{{ $o->spesifikasi_ditawarkan }}</p>
                            @endif
                            @if($o->catatan)<p class="text-xs text-gray-500 mt-1">Catatan: {{ $o->catatan }}</p>@endif
                            @if($o->link_sumber)<a href="{{ $o->link_sumber }}" target="_blank" rel="noopener" class="text-xs text-indigo-600 hover:underline break-all">{{ $o->link_sumber }}</a>@endif
                        </div>
                    </div>
                    <div class="text-right text-sm whitespace-nowrap">
                        <p class="font-semibold text-gray-800">{{ $o->harga_jual !== null ? 'Rp ' . number_format($o->harga_jual, 0, ',', '.') : 'Harga belum diisi' }}</p>
                        @if($lihatHargaBeli)
                            @if($o->harga_beli !== null)<p class="text-xs text-gray-400">Beli: Rp {{ number_format($o->harga_beli, 0, ',', '.') }}</p>@endif
                            @if($o->supplier_nama)<p class="text-xs text-gray-400">{{ $o->supplier_nama }}</p>@endif
                        @endif
                        @if($kelolaProduk && $s === 'dicari')
                        <form method="POST" action="{{ route('produk.opsi.destroy', [$p, $o]) }}" onsubmit="return confirm('Hapus opsi ini?')" class="mt-1">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:text-red-700">Hapus</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 mb-2">Belum ada opsi.</p>
            @endforelse

            {{-- Form tambah opsi (Produk) --}}
            @if($kelolaProduk && $s === 'dicari')
            <details class="mt-3">
                <summary class="cursor-pointer text-sm font-semibold text-indigo-600">+ Tambah opsi untuk item ini</summary>
                <form method="POST" action="{{ route('produk.opsi.store', [$p, $item]) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                    @csrf
                    <input type="text" name="nama_barang" required placeholder="Nama barang *" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <input type="text" name="merk" placeholder="Merk / tipe" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <textarea name="spesifikasi_ditawarkan" rows="3" placeholder="Spesifikasi yang ditawarkan" class="md:col-span-2 border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                    <input type="number" step="0.01" min="0" name="harga_beli" placeholder="Harga beli (internal)" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <input type="number" step="0.01" min="0" name="harga_jual" placeholder="Harga jual (ditampilkan ke Sales)" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <input type="text" name="supplier_nama" placeholder="Supplier (internal)" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <input type="url" name="link_sumber" placeholder="Link produk / sumber (https://...)" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <input type="text" name="catatan" placeholder="Catatan (garansi, ketersediaan, dll)" class="md:col-span-2 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <div class="md:col-span-2"><button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Simpan Opsi</button></div>
                </form>
            </details>
            @endif
        </div>
        @endforeach

        @if($s === 'opsi_siap' && $bisaKonfirmasi)
        </form>
        @endif
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow p-5 space-y-3">
            <h3 class="font-semibold text-gray-800">Aksi</h3>

            @if($s === 'baru' && $kelolaProduk)
            <form method="POST" action="{{ route('produk.ambil', $p) }}">@csrf
                <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Ambil & Mulai Cari</button>
            </form>
            @elseif($s === 'baru')
            <p class="text-sm text-gray-500">Menunggu tim Produk mengambil permintaan ini.</p>
            @endif

            @if($s === 'dicari' && $kelolaProduk)
            <form method="POST" action="{{ route('produk.kirim', $p) }}" onsubmit="return confirm('Kirim semua opsi ke Sales?')">@csrf
                <button class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Kirim Opsi ke Sales</button>
            </form>
            <p class="text-xs text-gray-500">Setiap item harus punya minimal satu opsi.</p>
            @elseif($s === 'dicari')
            <p class="text-sm text-gray-500">Tim Produk sedang mencarikan barang.</p>
            @endif

            @if($s === 'opsi_siap' && $bisaKonfirmasi)
            <button type="submit" form="form-konfirmasi" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Konfirmasi Pilihan</button>
            <p class="text-xs text-gray-500">Pilih satu opsi di setiap item, lalu konfirmasi. Permintaan otomatis masuk antrian Tayang.</p>
            <form method="POST" action="{{ route('produk.opsi_lain', $p) }}" class="space-y-2 pt-2 border-t">@csrf
                <textarea name="alasan_revisi" rows="2" required placeholder="Alasan / opsi seperti apa yang diinginkan..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                <button class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Minta Opsi Lain</button>
            </form>
            @elseif($s === 'opsi_siap')
            <p class="text-sm text-gray-500">Menunggu Sales memilih opsi.</p>
            @endif

            @if(in_array($s, ['baru', 'dicari', 'opsi_siap']) && $bisaBatal)
            <form method="POST" action="{{ route('produk.batalkan', $p) }}" class="space-y-2 pt-2 border-t" onsubmit="return confirm('Batalkan permintaan ini?')">@csrf
                <input type="text" name="alasan_batal" required placeholder="Alasan pembatalan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <button class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-semibold transition">Batalkan Permintaan</button>
            </form>
            @endif

            @if($s === 'dikonfirmasi')
            <p class="text-sm text-gray-500">Dikonfirmasi oleh <strong>{{ $p->konfirmator->name ?? '-' }}</strong>, {{ optional($p->dikonfirmasi_at)->format('d M Y H:i') }}.</p>
            @endif
        </div>

        @if($p->tayang->isNotEmpty())
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Tayang</h3>
            @foreach($p->tayang as $t)
            <div class="mb-2">
                <div class="flex justify-between items-center mb-1">@include('tayang._badge', ['status' => $t->status])
                    @if(\App\Services\ProdukTayangService::lihatTayang(auth()->user()))<a href="{{ route('tayang.show', $t) }}" class="text-xs text-indigo-600 hover:underline">Detail</a>@endif
                </div>
                @if($t->link_tayang)
                <a href="{{ $t->link_tayang }}" target="_blank" rel="noopener" class="text-sm text-indigo-600 hover:underline break-all">{{ $t->link_tayang }}</a>
                @else
                <p class="text-xs text-gray-400">Link belum tersedia.</p>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
