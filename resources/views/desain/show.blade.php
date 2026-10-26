@extends('layouts.app')

@section('content')
@php $s = $d->status; @endphp
<div class="mb-6">
    <a href="{{ route('desain.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
    <div class="flex flex-wrap items-center gap-3 mt-2">
        <h1 class="text-2xl font-bold text-gray-800">{{ $d->judul }}</h1>
        @include('desain._badge', ['status' => $s])
    </div>
    <p class="text-sm text-gray-400 mt-1">{{ $d->no_desain }}</p>
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
            <h2 class="font-semibold text-gray-800 mb-3">Brief</h2>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $d->brief ?: '-' }}</p>
            <dl class="grid grid-cols-2 gap-4 text-sm mt-4 pt-4 border-t">
                <div><dt class="text-xs text-gray-400">Ukuran / Format</dt><dd class="font-medium text-gray-800">{{ $d->ukuran_format ?: '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Deadline</dt><dd class="font-medium {{ $d->deadline && $d->deadline->isPast() && $d->isAktif() ? 'text-red-600' : 'text-gray-800' }}">{{ $d->deadline ? $d->deadline->format('d M Y') : '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Peminta</dt><dd class="font-medium text-gray-800">{{ $d->creator->name ?? '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Desainer</dt><dd class="font-medium text-gray-800">{{ $d->desainer->name ?? '-' }}</dd></div>
                @if($d->proyek)<div><dt class="text-xs text-gray-400">Proyek</dt><dd class="font-medium text-gray-800">{{ $d->proyek->nama_proyek }}</dd></div>@endif
                @if($d->tayang)<div><dt class="text-xs text-gray-400">Tayang terkait</dt><dd><a href="{{ route('tayang.show', $d->tayang) }}" class="text-indigo-600 hover:underline font-medium">{{ $d->tayang->judul }}</a></dd></div>@endif
            </dl>
            @if($s === 'revisi' && $d->catatan_revisi)
            <div class="mt-4 bg-orange-50 border border-orange-200 text-orange-800 rounded-lg px-4 py-3 text-sm"><strong>Catatan revisi:</strong> <span class="whitespace-pre-line">{{ $d->catatan_revisi }}</span></div>
            @endif
            @if($s === 'dibatalkan' && $d->alasan_batal)
            <div class="mt-4 bg-gray-50 border text-gray-600 rounded-lg px-4 py-3 text-sm"><strong>Alasan batal:</strong> {{ $d->alasan_batal }}</div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-800 mb-3">File Referensi</h2>
            @if($referensi->isEmpty())
            <p class="text-sm text-gray-400">Tidak ada file referensi.</p>
            @else
            @include('desain._file_list', ['files' => $referensi, 'desain' => $d])
            @endif
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-800 mb-3">Hasil Desain</h2>
            @forelse($hasil as $versi => $files)
            <div class="mb-4 last:mb-0">
                <p class="text-xs font-semibold text-indigo-600 mb-1">Versi {{ $versi }}{{ $loop->first ? ' (terbaru)' : '' }}</p>
                @include('desain._file_list', ['files' => $files, 'desain' => $d])
            </div>
            @empty
            <p class="text-sm text-gray-400">Belum ada hasil yang dikirim.</p>
            @endforelse
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow p-5 space-y-3">
            <h3 class="font-semibold text-gray-800">Aksi</h3>

            @if($s === 'baru' && $kerjakan)
            <form method="POST" action="{{ route('desain.ambil', $d) }}">@csrf
                <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Ambil & Kerjakan</button>
            </form>
            @endif

            @if(in_array($s, ['dikerjakan', 'revisi']) && $kerjakan)
            <form method="POST" action="{{ route('desain.kirim', $d) }}" enctype="multipart/form-data" class="space-y-2">@csrf
                <label class="block text-xs text-gray-500">Unggah hasil desain (bisa lebih dari satu file)</label>
                <input type="file" name="hasil[]" multiple required class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm bg-white">
                <button class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Kirim Hasil untuk Review</button>
            </form>
            @endif

            @if($s === 'review' && $putuskan)
            <form method="POST" action="{{ route('desain.setujui', $d) }}" onsubmit="return confirm('Setujui desain ini?')">@csrf
                <button class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Setujui Desain</button>
            </form>
            <form method="POST" action="{{ route('desain.revisi', $d) }}" class="space-y-2">@csrf
                <textarea name="catatan_revisi" rows="3" required maxlength="2000" placeholder="Bagian mana yang perlu diubah..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"></textarea>
                <button class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Minta Revisi</button>
            </form>
            @elseif($s === 'review')
            <p class="text-sm text-gray-500">Menunggu keputusan peminta.</p>
            @endif

            @if($d->isAktif() && $putuskan)
            <form method="POST" action="{{ route('desain.batalkan', $d) }}" class="space-y-2 pt-2 border-t" onsubmit="return confirm('Batalkan permintaan ini?')">@csrf
                <input type="text" name="alasan_batal" required maxlength="500" placeholder="Alasan pembatalan"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
                <button class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-semibold transition">Batalkan Permintaan</button>
            </form>
            @endif

            @if(in_array($s, ['selesai', 'dibatalkan']))
            <p class="text-sm text-gray-400">Tidak ada aksi tersedia.</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow p-5 text-sm space-y-1">
            <h3 class="font-semibold text-gray-800 mb-2">Riwayat</h3>
            <p class="text-gray-600">Diajukan: {{ $d->created_at->format('d M Y H:i') }}</p>
            @if($d->diambil_at)<p class="text-gray-600">Diambil: {{ $d->diambil_at->format('d M Y H:i') }}</p>@endif
            @if($d->dikirim_at)<p class="text-gray-600">Hasil terakhir dikirim: {{ $d->dikirim_at->format('d M Y H:i') }}</p>@endif
            @if($d->selesai_at)<p class="text-gray-600">Disetujui: {{ $d->selesai_at->format('d M Y H:i') }}</p>@endif
            <p class="text-gray-600">Jumlah revisi: {{ $d->jumlah_revisi }}</p>
        </div>
    </div>
</div>
@endsection
