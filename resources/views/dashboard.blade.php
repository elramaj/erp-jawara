@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-indigo-500">
        <p class="text-gray-500 text-sm">Role Kamu</p>
        <p class="text-2xl font-bold text-indigo-700 mt-1 capitalize">{{ auth()->user()->role->name ?? '-' }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <p class="text-gray-500 text-sm">Status Akun</p>
        <p class="text-2xl font-bold text-green-600 mt-1">✅ Aktif</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-500">
        <p class="text-gray-500 text-sm">Tanggal Hari Ini</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ now()->translatedFormat('d M Y') }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-2">👋 Selamat Datang, {{ auth()->user()->name }}!</h2>
    <p class="text-gray-500 text-sm">Sistem ERP Kantor siap digunakan. Pilih menu di sidebar untuk memulai.</p>
</div>
@endsection