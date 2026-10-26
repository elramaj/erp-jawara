@extends('layouts.app')

@section('content')
@php $in = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400'; @endphp
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ $arsip ? 'Edit Dokumen Arsip' : 'Tambah Dokumen Arsip' }}</h1>

    @if($errors->any())
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ $arsip ? route('arsip.update', $arsip) : route('arsip.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        @if($arsip) @method('PUT') @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis *</label>
            <input type="text" name="jenis" list="daftar-jenis" value="{{ old('jenis', $arsip->jenis ?? '') }}" maxlength="100" required class="{{ $in }}">
            <datalist id="daftar-jenis">@foreach($jenisSaran as $j)<option value="{{ $j }}">@endforeach</datalist>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama / Judul *</label>
            <input type="text" name="nama" value="{{ old('nama', $arsip->nama ?? '') }}" maxlength="200" required class="{{ $in }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor</label>
            <input type="text" name="nomor" value="{{ old('nomor', $arsip->nomor ?? '') }}" maxlength="100" class="{{ $in }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit</label>
            <input type="text" name="penerbit" value="{{ old('penerbit', $arsip->penerbit ?? '') }}" maxlength="150" placeholder="Mis. OSS / Kemenkumham / Notaris" class="{{ $in }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Terbit</label>
            <input type="date" name="tanggal_terbit" value="{{ old('tanggal_terbit', optional($arsip->tanggal_terbit ?? null)->format('Y-m-d')) }}" class="{{ $in }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku s/d (kosongkan bila tanpa batas)</label>
            <input type="date" name="tanggal_kedaluwarsa" value="{{ old('tanggal_kedaluwarsa', optional($arsip->tanggal_kedaluwarsa ?? null)->format('Y-m-d')) }}" class="{{ $in }}">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">File {{ $arsip ? '(kosongkan bila tidak diganti)' : '*' }}</label>
            @if($arsip)<p class="text-xs text-gray-500 mb-1">File saat ini: {{ $arsip->nama_asli }}</p>@endif
            <input type="file" name="file" {{ $arsip ? '' : 'required' }} accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.zip" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm bg-white">
            <p class="text-xs text-gray-400 mt-1">PDF / gambar / Word / Excel / ZIP, maks. 10 MB. File disimpan privat, hanya bisa diunduh role yang berhak.</p>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
            <textarea name="catatan" rows="2" maxlength="2000" class="{{ $in }}">{{ old('catatan', $arsip->catatan ?? '') }}</textarea>
        </div>
        <div class="md:col-span-2 flex gap-2 pt-2">
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition">Simpan</button>
            <a href="{{ route('arsip.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
        </div>
    </form>
</div>
@endsection
