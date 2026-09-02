@extends('layouts.app')

@section('content')
@php
$statusBadge = [
    'tersedia'   => ['bg-blue-100 text-blue-700', 'Tersedia'],
    'dipakai'    => ['bg-green-100 text-green-700', 'Dipakai'],
    'diperbaiki' => ['bg-yellow-100 text-yellow-700', 'Diperbaiki'],
    'hilang'     => ['bg-red-100 text-red-700', 'Hilang'],
];
$kondisiBadge = [
    'baik'         => ['bg-green-100 text-green-700', 'Baik'],
    'rusak_ringan' => ['bg-yellow-100 text-yellow-700', 'Rusak Ringan'],
    'rusak_berat'  => ['bg-red-100 text-red-700', 'Rusak Berat'],
    'hilang'       => ['bg-red-100 text-red-700', 'Hilang'],
];
$riwayatStatusColor = [
    'dipakai'    => 'bg-green-500',
    'gudang'     => 'bg-blue-500',
    'diperbaiki' => 'bg-yellow-500',
    'hilang'     => 'bg-red-500',
];
$riwayatBadgeColor = [
    'dipakai'    => 'bg-green-100 text-green-700',
    'gudang'     => 'bg-blue-100 text-blue-700',
    'diperbaiki' => 'bg-yellow-100 text-yellow-700',
    'hilang'     => 'bg-red-100 text-red-700',
];
$riwayatLabel = [
    'dipakai'    => 'Dipakai',
    'gudang'     => 'Di Gudang',
    'diperbaiki' => 'Diperbaiki',
    'hilang'     => 'Hilang',
];
@endphp

<div class="flex justify-between items-start mb-4">
    <div>
        <a href="{{ route('aset.index') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Kembali ke daftar aset
        </a>
        <h1 class="text-xl font-bold text-gray-800">{{ $aset->nama_aset }}</h1>
        <p class="text-sm text-gray-400 font-mono">{{ $aset->kode_aset }}</p>
    </div>
    <a href="{{ route('aset.edit', $aset) }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
        Edit Data
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Kolom kiri: Detail --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex gap-2 mb-4">
                <span class="{{ $statusBadge[$aset->status][0] }} px-3 py-1 rounded-full text-xs font-semibold">{{ $statusBadge[$aset->status][1] }}</span>
                <span class="{{ $kondisiBadge[$aset->kondisi][0] }} px-3 py-1 rounded-full text-xs font-semibold">{{ $kondisiBadge[$aset->kondisi][1] }}</span>
            </div>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-400 text-xs">Kategori</dt>
                    <dd class="text-gray-700 font-medium">{{ $aset->kategori->nama ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs">Dipegang Oleh</dt>
                    <dd class="text-gray-700 font-medium">{{ $aset->pemegang->name ?? '— (di gudang)' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs">Merk / Model</dt>
                    <dd class="text-gray-700 font-medium">{{ $aset->merk ?? '-' }} {{ $aset->model }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs">Serial Number</dt>
                    <dd class="text-gray-700 font-medium font-mono">{{ $aset->serial_number ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs">Tanggal Beli</dt>
                    <dd class="text-gray-700 font-medium">{{ $aset->tanggal_beli?->format('d M Y') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs">Harga Beli</dt>
                    <dd class="text-gray-700 font-medium">{{ $aset->harga_beli ? 'Rp ' . number_format($aset->harga_beli, 0, ',', '.') : '-' }}</dd>
                </div>
                @if(auth()->user()->isSuperAdmin())
                <div>
                    <dt class="text-gray-400 text-xs">Company</dt>
                    <dd class="text-gray-700 font-medium">{{ $aset->company->nama ?? '-' }}</dd>
                </div>
                @endif
            </dl>
            @if($aset->spesifikasi)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <dt class="text-gray-400 text-xs mb-1">Spesifikasi</dt>
                <dd class="text-gray-600 text-sm whitespace-pre-line">{{ $aset->spesifikasi }}</dd>
            </div>
            @endif
            @if($aset->catatan)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <dt class="text-gray-400 text-xs mb-1">Catatan</dt>
                <dd class="text-gray-600 text-sm whitespace-pre-line">{{ $aset->catatan }}</dd>
            </div>
            @endif
        </div>

        {{-- Riwayat Timeline --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                Riwayat Kepemilikan
            </h2>
            <div class="space-y-4">
                @forelse($aset->riwayat as $r)
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold {{ $riwayatStatusColor[$r->status] ?? 'bg-gray-400' }}">
                            {{ strtoupper(substr($r->user->name ?? 'G', 0, 1)) }}
                        </div>
                        <div class="w-0.5 bg-gray-200 flex-1 mt-1"></div>
                    </div>
                    <div class="pb-4 flex-1">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-medium text-gray-700">{{ $r->user->name ?? 'Di Gudang' }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $r->tanggal_mulai->format('d M Y') }}
                                @if($r->tanggal_selesai)
                                    – {{ $r->tanggal_selesai->format('d M Y') }}
                                @else
                                    – sekarang
                                @endif
                            </p>
                        </div>
                        @if($r->catatan)
                        <p class="text-sm text-gray-600 mt-1">{{ $r->catatan }}</p>
                        @endif
                        <span class="{{ $riwayatBadgeColor[$r->status] ?? 'bg-gray-100 text-gray-600' }} px-2 py-0.5 rounded-full text-xs font-semibold mt-1 inline-block">
                            {{ $riwayatLabel[$r->status] ?? $r->status }}
                        </span>
                        <p class="text-xs text-gray-300 mt-1">dicatat oleh {{ $r->creator->name ?? '-' }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-3">Belum ada riwayat.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Kolom kanan: Pindah Tangan --}}
    <div>
        <div class="bg-white rounded-xl shadow p-6 sticky top-20">
            <h2 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
                Pindah Tangan / Ubah Status
            </h2>
            <form method="POST" action="{{ route('aset.pindah', $aset) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status Baru *</label>
                    <select name="status_baru" id="status_baru" onchange="document.getElementById('wrap_user_id').style.display = this.value === 'dipakai' ? 'block' : 'none'"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 pr-8" required>
                        <option value="dipakai">Dipakai (assign ke karyawan)</option>
                        <option value="gudang">Kembalikan ke gudang</option>
                        <option value="diperbaiki">Sedang diperbaiki</option>
                        <option value="hilang">Hilang</option>
                    </select>
                </div>
                <div id="wrap_user_id">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dipegang Oleh *</label>
                    <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 pr-8">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawan as $kry)
                        <option value="{{ $kry->id }}" {{ $aset->dipegang_oleh == $kry->id ? 'selected' : '' }}>{{ $kry->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Catatan</label>
                    <textarea name="catatan" rows="2" placeholder="Misal: serah terima karena resign, ganti unit, dll"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
                </div>
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection