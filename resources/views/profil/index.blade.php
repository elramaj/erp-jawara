@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><svg class="w-6 h-6 inline-block -mt-1 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>Profil Saya</h1>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Ringkasan Aktivitas --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
        <p class="text-gray-500 text-xs">Hadir Bulan Ini</p>
        <p class="text-2xl font-bold text-green-600">{{ $hadir }}</p>
        @if($terlambat > 0)
        <p class="text-[10px] text-yellow-600 mt-0.5">{{ $terlambat }}x terlambat</p>
        @endif
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-indigo-500">
        <p class="text-gray-500 text-xs">Lembur Bulan Ini</p>
        <p class="text-2xl font-bold text-indigo-600">{{ floor($lemburMenitBulanIni / 60) }}j {{ $lemburMenitBulanIni % 60 }}m</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-500">
        <p class="text-gray-500 text-xs">Izin/Cuti Tahun Ini</p>
        <p class="text-2xl font-bold text-blue-600">{{ $izinTahunIni }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-orange-500">
        <p class="text-gray-500 text-xs">Aset Dipegang</p>
        <p class="text-2xl font-bold text-orange-600">{{ $asetDipegang->count() }}</p>
        @if($reimbursePending > 0)
        <p class="text-[10px] text-orange-500 mt-0.5">{{ $reimbursePending }} reimburse pending</p>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

    {{-- Edit Profil --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" /></svg>Edit Profil</h2>

        <form method="POST" action="{{ route('profil.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="flex items-center gap-4 mb-6">
                <label for="photoInput" class="relative cursor-pointer group flex-shrink-0">
                    @if($user->photo)
                    <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}"
                         class="w-16 h-16 rounded-full object-cover border-2 border-gray-100">
                    @else
                    <div class="w-16 h-16 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-2xl">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    @endif
                    <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" /></svg>
                    </div>
                    <input id="photoInput" type="file" name="photo" accept="image/png,image/jpeg" class="hidden"
                           onchange="document.getElementById('photoFileName').textContent = this.files[0]?.name ?? ''">
                </label>
                <div>
                    <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-semibold capitalize">
                        {{ $user->role->name ?? '-' }}
                    </span>
                    <p id="photoFileName" class="text-[10px] text-indigo-500 mt-1"></p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        required>
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" value="{{ $user->email }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed"
                        disabled>
                    <p class="text-xs text-gray-400 mt-1">Email tidak bisa diubah.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                    <input type="text" value="{{ $user->department->name ?? '-' }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed"
                        disabled>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung</label>
                    <input type="text" value="{{ $user->join_date ? $user->join_date->format('d M Y') : '-' }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed"
                        disabled>
                </div>
            </div>
            <button type="submit"
                class="mt-5 w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg text-sm font-semibold transition">
                Simpan Perubahan
            </button>
        </form>
    </div>

    {{-- Ganti Password --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>Ganti Password</h2>

        <form method="POST" action="{{ route('profil.password') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                    <input type="password" name="password_lama"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        required>
                    @error('password_lama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password_baru"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        required>
                    @error('password_baru')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_baru_confirmation"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        required>
                </div>
            </div>
            <button type="submit"
                class="mt-5 w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg text-sm font-semibold transition">
                Ganti Password
            </button>
        </form>
    </div>
</div>

{{-- Aset yang Dipegang --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25m0-9L3 7.5m9 5.25v9M3 7.5v9l9 5.25" /></svg>Aset yang Sedang Saya Pegang</h2>

    @if($asetDipegang->isEmpty())
    <p class="text-sm text-gray-400 py-4 text-center">Kamu belum memegang aset perusahaan apapun saat ini.</p>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($asetDipegang as $a)
        <a href="{{ route('aset.show', $a) }}" class="border border-gray-100 rounded-lg p-3 hover:border-indigo-300 hover:bg-indigo-50/30 transition block">
            <p class="font-semibold text-sm text-gray-800">{{ $a->nama_aset }}</p>
            <p class="text-xs text-gray-400 font-mono">{{ $a->kode_aset }}</p>
            <div class="flex items-center justify-between mt-2">
                <span class="text-xs text-gray-500">{{ $a->kategori->nama ?? '-' }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                    {{ $a->kondisi == 'baik' ? 'bg-green-100 text-green-700' : ($a->kondisi == 'perbaikan' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                    {{ \App\Models\Aset::KONDISI[$a->kondisi] ?? $a->kondisi }}
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection