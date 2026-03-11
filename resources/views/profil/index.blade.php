@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">👤 Profil Saya</h1>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">
    ✅ {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Edit Profil --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">✏️ Edit Profil</h2>

        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-2xl">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-semibold capitalize">
                    {{ $user->role->name ?? '-' }}
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('profil.update') }}">
            @csrf
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
        <h2 class="text-lg font-semibold text-gray-700 mb-4">🔒 Ganti Password</h2>

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
@endsection