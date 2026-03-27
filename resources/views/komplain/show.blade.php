@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-xs text-gray-400 font-mono">{{ $komplain->no_komplain }}</p>
        <h1 class="text-2xl font-bold text-gray-800">{{ $komplain->judul }}</h1>
        <div class="flex gap-2 mt-1 flex-wrap">
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                {{ $komplain->prioritas == 'critical' ? 'bg-red-100 text-red-700' : '' }}
                {{ $komplain->prioritas == 'high' ? 'bg-orange-100 text-orange-700' : '' }}
                {{ $komplain->prioritas == 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $komplain->prioritas == 'low' ? 'bg-green-100 text-green-700' : '' }}">
                {{ $komplain->prioritas_label }}
            </span>
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                {{ $komplain->status == 'open' ? 'bg-red-100 text-red-700' : '' }}
                {{ $komplain->status == 'in_progress' ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $komplain->status == 'resolved' ? 'bg-green-100 text-green-700' : '' }}">
                {{ ucfirst(str_replace('_', ' ', $komplain->status)) }}
            </span>
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                {{ $komplain->jenis == 'barang' ? '📦 Barang' : '📄 Dokumen' }}
            </span>
            @if($komplain->masih_garansi)
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">🛡️ Masih Garansi</span>
            @endif
        </div>
    </div>
    <a href="{{ route('komplain.index') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
        ← Kembali
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">✅ {{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        {{-- Detail Komplain --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">📋 Detail Komplain</h2>
            <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                <div>
                    <p class="text-xs text-gray-400">Proyek</p>
                    <p class="font-medium text-gray-700 mt-1">{{ $komplain->proyek->nama_proyek ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Dibuat oleh</p>
                    <p class="font-medium text-gray-700 mt-1">{{ $komplain->creator->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Tanggal Komplain</p>
                    <p class="font-medium text-gray-700 mt-1">{{ $komplain->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Ditangani oleh</p>
                    <p class="font-medium text-gray-700 mt-1">{{ $komplain->handler->name ?? '-' }}</p>
                </div>
                @if($komplain->resolved_at)
                <div>
                    <p class="text-xs text-gray-400">Selesai pada</p>
                    <p class="font-medium text-green-600 mt-1">{{ $komplain->resolved_at->format('d M Y H:i') }}</p>
                </div>
                @endif
            </div>
            @if($komplain->deskripsi)
            <div class="border-t pt-4">
                <p class="text-xs text-gray-400 mb-1">Deskripsi</p>
                <p class="text-sm text-gray-600">{{ $komplain->deskripsi }}</p>
            </div>
            @endif
        </div>

        {{-- Update Status --}}
        @if($komplain->status != 'resolved')
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">🔄 Update Status</h2>
            <form method="POST" action="{{ route('komplain.status', $komplain) }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="text-xs text-gray-500">Status Baru *</label>
                        <select name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="open" {{ $komplain->status == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $komplain->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved">Resolved ✅</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Ditangani oleh</label>
                        <select name="handled_by"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="">-- Pilih --</option>
                            @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ $komplain->handled_by == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->role->name ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-500">Keterangan Update *</label>
                        <textarea name="keterangan" rows="2" required
                            placeholder="Jelaskan tindakan yang diambil..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
                    </div>
                </div>
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Update Status
                </button>
            </form>
        </div>
        @endif

        {{-- Timeline --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">📅 Timeline Penanganan</h2>
            <div class="space-y-4">
                @forelse($komplain->timeline as $t)
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold
                            {{ $t->status_baru == 'open' ? 'bg-red-500' : '' }}
                            {{ $t->status_baru == 'in_progress' ? 'bg-yellow-500' : '' }}
                            {{ $t->status_baru == 'resolved' ? 'bg-green-500' : '' }}
                            {{ !$t->status_baru ? 'bg-gray-400' : '' }}">
                            {{ strtoupper(substr($t->creator->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="w-0.5 bg-gray-200 flex-1 mt-1"></div>
                    </div>
                    <div class="pb-4 flex-1">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-medium text-gray-700">{{ $t->creator->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400">{{ $t->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ $t->keterangan }}</p>
                        @if($t->status_baru)
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold mt-1 inline-block
                            {{ $t->status_baru == 'open' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $t->status_baru == 'in_progress' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $t->status_baru == 'resolved' ? 'bg-green-100 text-green-700' : '' }}">
                            → {{ ucfirst(str_replace('_', ' ', $t->status_baru)) }}
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-3">Belum ada timeline.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Kolom Kanan --}}
    <div>
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">ℹ️ Info Singkat</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-xs text-gray-400">No. Komplain</p>
                    <p class="font-mono font-semibold text-indigo-600">{{ $komplain->no_komplain }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Jenis</p>
                    <p class="font-medium text-gray-700">{{ $komplain->jenis == 'barang' ? '📦 Barang' : '📄 Dokumen' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Prioritas</p>
                    <p class="font-medium text-gray-700">{{ $komplain->prioritas_label }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Status</p>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $komplain->status == 'open' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $komplain->status == 'in_progress' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $komplain->status == 'resolved' ? 'bg-green-100 text-green-700' : '' }}">
                        {{ ucfirst(str_replace('_', ' ', $komplain->status)) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Garansi</p>
                    <p class="font-medium text-gray-700">{{ $komplain->masih_garansi ? '✅ Masih Garansi' : '❌ Tidak/Habis' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection