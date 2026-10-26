@extends('layouts.app')

@section('content')
@php $s = $d->status; @endphp
<div class="mb-6">
    <a href="{{ route('dokumen.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
    <div class="flex flex-wrap items-center gap-3 mt-2">
        <h1 class="text-2xl font-bold text-gray-800">{{ $d->jenis_dokumen }}</h1>
        @include('dokumen._badge', ['status' => $s])
    </div>
    <p class="text-sm text-gray-400 mt-1">{{ $d->no_permintaan }}</p>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-800 mb-3">Keperluan</h2>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $d->keperluan ?: '-' }}</p>
            <dl class="grid grid-cols-2 gap-4 text-sm mt-4 pt-4 border-t">
                <div><dt class="text-xs text-gray-400">Peminta</dt><dd class="font-medium text-gray-800">{{ $d->creator->name ?? '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Penyiap</dt><dd class="font-medium text-gray-800">{{ $d->penyiap->name ?? '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Deadline</dt><dd class="font-medium {{ $d->deadline && $d->deadline->isPast() && in_array($s, ['baru','diproses']) ? 'text-red-600' : 'text-gray-800' }}">{{ $d->deadline ? $d->deadline->format('d M Y') : '-' }}</dd></div>
                @if($d->proyek)<div><dt class="text-xs text-gray-400">Proyek</dt><dd class="font-medium text-gray-800">{{ $d->proyek->nama_proyek }}</dd></div>@endif
            </dl>
            @if($s === 'dibatalkan' && $d->alasan_batal)
            <div class="mt-4 bg-gray-50 border text-gray-600 rounded-lg px-4 py-3 text-sm"><strong>Alasan batal:</strong> {{ $d->alasan_batal }}</div>
            @endif
        </div>

        @if($s === 'selesai')
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-800 mb-3">Hasil</h2>
            @if($d->catatan_penyiap)<p class="text-sm text-gray-700 whitespace-pre-line mb-3"><strong>Catatan penyiap:</strong> {{ $d->catatan_penyiap }}</p>@endif

            @if($d->arsip)
            <div class="mb-3 bg-indigo-50 border border-indigo-200 rounded-lg px-4 py-3 text-sm">
                <p class="text-indigo-800"><strong>Dipenuhi dari arsip legalitas:</strong> {{ $d->arsip->jenis }} — {{ $d->arsip->nama }}{{ $d->arsip->nomor ? ' (' . $d->arsip->nomor . ')' : '' }}</p>
                @if($lihatArsip)
                <a href="{{ route('arsip.file', [$d->arsip, 'unduh' => 1]) }}" class="text-indigo-700 hover:underline font-semibold text-xs">Unduh dokumen arsip</a>
                @endif
            </div>
            @endif

            @if($d->file->isNotEmpty())
            <ul class="divide-y divide-gray-100 text-sm">
                @foreach($d->file as $f)
                <li class="flex items-center justify-between gap-3 py-2">
                    <div class="min-w-0">
                        <p class="text-gray-800 truncate">{{ $f->nama_asli }}</p>
                        <p class="text-xs text-gray-400">{{ \App\Support\BerkasPrivat::ukuranLabel((int) $f->ukuran) }} · {{ $f->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <a href="{{ route('dokumen.file', [$d, $f, 'unduh' => 1]) }}" class="text-xs text-indigo-600 hover:underline font-semibold flex-shrink-0">Unduh</a>
                </li>
                @endforeach
            </ul>
            @elseif(!$d->arsip)
            <p class="text-sm text-gray-400">Tidak ada file.</p>
            @endif
        </div>
        @endif
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow p-5 space-y-3">
            <h3 class="font-semibold text-gray-800">Aksi</h3>

            @if($s === 'baru' && $siapkan)
            <form method="POST" action="{{ route('dokumen.ambil', $d) }}">@csrf
                <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Ambil & Siapkan</button>
            </form>
            @endif

            @if(in_array($s, ['baru', 'diproses']) && $siapkan)
            <form method="POST" action="{{ route('dokumen.selesai', $d) }}" enctype="multipart/form-data" class="space-y-2 pt-2 {{ $s === 'baru' ? 'border-t' : '' }}">@csrf
                <label class="block text-xs text-gray-500">Unggah dokumen (PDF/gambar/Word/Excel/ZIP, maks. 10 MB per file)</label>
                <input type="file" name="file[]" multiple class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm bg-white">

                @if($daftarArsip->isNotEmpty())
                <label class="block text-xs text-gray-500 pt-1">…atau penuhi dari arsip legalitas</label>
                <select name="arsip_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">
                    <option value="">— tidak dari arsip —</option>
                    @foreach($daftarArsip as $a)
                    <option value="{{ $a->id }}" {{ old('arsip_id') == $a->id ? 'selected' : '' }}>{{ $a->jenis }} — {{ $a->nama }}{{ $a->tanggal_kedaluwarsa ? ' (s/d ' . $a->tanggal_kedaluwarsa->format('d M Y') . ')' : '' }}</option>
                    @endforeach
                </select>
                @endif

                <textarea name="catatan_penyiap" rows="2" maxlength="2000" placeholder="Catatan untuk peminta (opsional)"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('catatan_penyiap') }}</textarea>
                <button class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Tandai Selesai</button>
            </form>
            @endif

            @if(in_array($s, ['baru', 'diproses']) && $bisaBatal)
            <form method="POST" action="{{ route('dokumen.batalkan', $d) }}" class="space-y-2 pt-2 border-t" onsubmit="return confirm('Batalkan permintaan ini?')">@csrf
                <input type="text" name="alasan_batal" required maxlength="500" placeholder="Alasan pembatalan"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
                <button class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-semibold transition">Batalkan Permintaan</button>
            </form>
            @endif

            @if(in_array($s, ['selesai', 'dibatalkan']) || (!$siapkan && !$bisaBatal))
            <p class="text-sm text-gray-400">Tidak ada aksi tersedia.</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow p-5 text-sm space-y-1">
            <h3 class="font-semibold text-gray-800 mb-2">Riwayat</h3>
            <p class="text-gray-600">Diajukan: {{ $d->created_at->format('d M Y H:i') }}</p>
            @if($d->diambil_at)<p class="text-gray-600">Diambil: {{ $d->diambil_at->format('d M Y H:i') }}</p>@endif
            @if($d->selesai_at)<p class="text-gray-600">Selesai: {{ $d->selesai_at->format('d M Y H:i') }}</p>@endif
        </div>
    </div>
</div>
@endsection
