@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-xs text-gray-400 font-mono">{{ $proyek->kode_proyek }}</p>
        <h1 class="text-2xl font-bold text-gray-800">{{ $proyek->nama_proyek }}</h1>
        <p class="text-gray-500 text-sm"><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg> {{ $proyek->klien }}</p>
    </div>
    <a href="{{ route('proyek.index') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5 w-fit">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg> Kembali
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kolom Kiri --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Info & Progress --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg> Info Proyek</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                <div>
                    <p class="text-xs text-gray-400">Status</p>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold mt-1 inline-block
                        {{ $proyek->status == 'aktif' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $proyek->status == 'bola_liar' ? 'bg-orange-100 text-orange-700' : '' }}
                        {{ $proyek->status == 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
                        {{ $proyek->status == 'selesai' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $proyek->status == 'dibatalkan' ? 'bg-red-100 text-red-700' : '' }}">
                        {!! $proyek->status == 'bola_liar' ? '<svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" /></svg> Bola Liar' : ucfirst($proyek->status) !!}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Nilai Kontrak</p>
                    <p class="text-sm font-semibold text-gray-700 mt-1">
                        {{ $proyek->nilai_kontrak ? 'Rp ' . number_format($proyek->nilai_kontrak, 0, ',', '.') : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Tanggal Mulai</p>
                    <p class="text-sm font-semibold text-gray-700 mt-1">
                        {{ $proyek->tanggal_mulai ? $proyek->tanggal_mulai->format('d M Y') : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Deadline</p>
                    <p class="text-sm font-semibold mt-1 {{ $proyek->deadline && $proyek->deadline->isPast() && $proyek->status != 'selesai' ? 'text-red-600' : 'text-gray-700' }}">
                        {{ $proyek->deadline ? $proyek->deadline->format('d M Y') : '-' }}
                    </p>
                </div>
            </div>

            {{-- Alert Bola Liar --}}
            @if($proyek->status == 'bola_liar')
            <div class="bg-orange-50 border border-orange-300 rounded-lg p-3 mb-4 flex items-center gap-2">
                <span class="text-xl"><svg class="w-6 h-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" /></svg></span>
                <p class="text-sm text-orange-700 font-medium">Proyek ini berstatus <strong>Bola Liar</strong> — belum ada sales yang handle. Segera tentukan sales PIC!</p>
            </div>
            @endif

            {{-- Progress Bar --}}
            <div class="mb-2">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-500">Progress Keseluruhan</span>
                    <span class="font-bold text-indigo-600">{{ $proyek->progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="h-3 rounded-full transition-all {{ $proyek->progress == 100 ? 'bg-green-500' : 'bg-indigo-500' }}"
                         style="width: {{ $proyek->progress }}%"></div>
                </div>
            </div>

            @if($proyek->deskripsi)
            <p class="text-sm text-gray-500 mt-4 border-t pt-4">{{ $proyek->deskripsi }}</p>
            @endif

            {{-- Update Progress (admin/bos) --}}
            @if(in_array(auth()->user()->role_id, [1, 10, 11]))
            <form method="POST" action="{{ route('proyek.progress', $proyek) }}" class="mt-4 border-t pt-4">
                @csrf
                <p class="text-sm font-medium text-gray-700 mb-2">Update Progress & Status</p>
                <div class="flex gap-3 items-end">
                    <div class="flex-1">
                        <label class="text-xs text-gray-500">Progress (%)</label>
                        <input type="number" name="progress" value="{{ $proyek->progress }}"
                            min="0" max="100"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div class="flex-1">
                        <label class="text-xs text-gray-500">Status</label>
                        <select name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 pr-8">
                            @foreach(['draft' => 'Draft', 'bola_liar' => 'Bola Liar', 'aktif' => 'Aktif', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $val => $label)
                            <option value="{{ $val }}" {{ $proyek->status == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Update
                    </button>
                </div>
            </form>
            @endif
        </div>

        {{-- Timeline / Milestone --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-5"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg> Timeline Proyek</h2>

            {{-- List Milestone --}}
            <div class="relative">
                @forelse($proyek->milestone as $m)
                <div class="flex gap-4 mb-4">
                    {{-- Icon & garis --}}
                    <div class="flex flex-col items-center">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold border-2
                            {{ $m->status == 'selesai' ? 'bg-green-500 border-green-500 text-white' : '' }}
                            {{ $m->status == 'proses' ? 'bg-yellow-400 border-yellow-400 text-white' : '' }}
                            {{ $m->status == 'belum' ? 'bg-white border-gray-300 text-gray-400' : '' }}">
                            @if($m->status == 'selesai') ✓
                            @elseif($m->status == 'proses') ⟳
                            @else {{ $m->urutan }}
                            @endif
                        </div>
                        @if(!$loop->last)
                        <div class="w-0.5 h-full min-h-6 {{ $m->status == 'selesai' ? 'bg-green-400' : 'bg-gray-200' }} mt-1"></div>
                        @endif
                    </div>
                    {{-- Konten --}}
                    <div class="flex-1 pb-4">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <p class="text-sm font-semibold
                                {{ $m->status == 'selesai' ? 'text-green-700' : ($m->status == 'proses' ? 'text-yellow-700' : 'text-gray-500') }}">
                                {{ $m->judul }}
                            </p>
                            <form method="POST" action="{{ route('milestone.status', $m) }}">
                                @csrf
                                <select name="status" onchange="this.form.submit()"
                                        style="padding-right: 1.5rem;"
                                        class="border rounded px-2 py-1 text-xs focus:outline-none
                                        {{ $m->status == 'selesai' ? 'border-green-300 bg-green-50 text-green-700' : '' }}
                                        {{ $m->status == 'proses' ? 'border-yellow-300 bg-yellow-50 text-yellow-700' : '' }}
                                        {{ $m->status == 'belum' ? 'border-gray-200 text-gray-500' : '' }} pr-8">
                                    <option value="belum" {{ $m->status == 'belum' ? 'selected' : '' }}>Belum</option>
                                    <option value="proses" {{ $m->status == 'proses' ? 'selected' : '' }}>Proses</option>
                                    <option value="selesai" {{ $m->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                            </form>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Target: {{ $m->tanggal_target->format('d M Y') }}
                            @if($m->tanggal_selesai)
                            <span class="text-green-500 ml-2">✓ Selesai: {{ $m->tanggal_selesai->format('d M Y') }}</span>
                            @endif
                        </p>
                        @if($m->deskripsi)
                        <p class="text-xs text-gray-500 mt-1">{{ $m->deskripsi }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-3">Belum ada milestone.</p>
                @endforelse
            </div>

            {{-- Tambah Milestone --}}
            @if(in_array(auth()->user()->role_id, [1, 10, 11]))
            <div class="border-t pt-4 mt-2">
                <p class="text-sm font-medium text-gray-700 mb-2">+ Tambah Milestone</p>
                <form method="POST" action="{{ route('proyek.milestone', $proyek) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input type="text" name="judul" placeholder="Judul milestone" required
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <input type="date" name="tanggal_target" required
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <input type="text" name="deskripsi" placeholder="Deskripsi (opsional)"
                            class="md:col-span-2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <button type="submit"
                        class="mt-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Tambah
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- Dokumen --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" /></svg> Dokumen Proyek</h2>

            <div class="space-y-2 mb-4">
                @forelse($proyek->dokumen as $d)
                <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl"><svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg></span>
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ $d->nama_dokumen }}</p>
                            <p class="text-xs text-gray-400">{{ $d->jenis ?? 'Dokumen' }} • {{ $d->uploader->name ?? '-' }}</p>
                        </div>
                    </div>
                    <a href="{{ Storage::url($d->file_path) }}" target="_blank"
                       class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-3 py-1 rounded text-xs font-semibold transition">
                        Download
                    </a>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-3">Belum ada dokumen.</p>
                @endforelse
            </div>

            {{-- Upload Dokumen --}}
            <div class="border-t pt-4">
                <p class="text-sm font-medium text-gray-700 mb-2">+ Upload Dokumen</p>
                <form method="POST" action="{{ route('proyek.dokumen', $proyek) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input type="text" name="nama_dokumen" placeholder="Nama dokumen" required
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <input type="text" name="jenis" placeholder="Jenis (Kontrak, SPK, Laporan...)"
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <input type="file" name="file" required
                            class="md:col-span-2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <button type="submit"
                        class="mt-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Upload
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan - Anggota Tim --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg> Anggota Tim</h2>
            <div class="space-y-3">
                @forelse($proyek->anggota as $a)
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($a->user->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">{{ $a->user->name ?? '-' }}</p>
                        <p class="text-xs text-gray-400">{{ $a->peran ?? $a->user->role->name ?? '-' }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">Belum ada anggota.</p>
                @endforelse
            </div>
        </div>

        {{-- Hapus Proyek --}}
        @if(in_array(auth()->user()->role_id, [1, 10, 11]))
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-red-600 mb-3"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg> Danger Zone</h2>
            <form method="POST" action="{{ route('proyek.destroy', $proyek) }}"
                onsubmit="return confirm('Yakin hapus proyek ini? Semua data milestone dan dokumen akan ikut terhapus!')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full bg-red-100 text-red-700 hover:bg-red-200 py-2 rounded-lg text-sm font-semibold transition">
                    Hapus Proyek
                </button>
            </form>
        </div>
        @endif
    </div>

</div>
@endsection