<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - ERP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

    {{-- NAVBAR --}}
    <nav class="bg-indigo-700 text-white px-6 py-3 flex justify-between items-center shadow-lg fixed w-full z-10">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🏢</span>
            <span class="font-bold text-lg tracking-wide">ERP Kantor</span>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                <p class="text-xs text-indigo-200">{{ auth()->user()->role->name ?? 'Admin' }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-indigo-500 flex items-center justify-center font-bold text-lg">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm bg-indigo-900 px-3 py-1.5 rounded-lg hover:bg-indigo-800 transition">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <div class="flex pt-14 min-h-screen">

        {{-- SIDEBAR --}}
        <aside class="w-64 bg-gray-900 text-gray-300 min-h-screen fixed pt-4 shadow-xl">
            <div class="px-4 py-3 mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold">Menu Utama</p>
            </div>
            <ul class="space-y-0.5 px-3">
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : '' }}">
                        <span>📊</span> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('absensi.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition">
                        <span>🕐</span> Absensi
                    </a>
                </li>
                <li>
    <a href="{{ route('izin.index') }}"
       class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition">
        <span>📋</span> Izin / Cuti
    </a>
</li>
@if(auth()->user()->role_id == 11 || auth()->user()->role_id == 1)
<li>
    <a href="{{ route('izin.review') }}"
       class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition">
        <span>✅</span> Review Izin
    </a>
</li>
@endif
            </ul>

            @if(auth()->user()->role_id == 11)
            <div class="px-4 py-3 mt-4 mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold">Admin</p>
            </div>
            <ul class="space-y-0.5 px-3">
                <li>
                    <a href="{{ route('karyawan.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition">
                        <span>👥</span> Kelola Karyawan
                    </a>
                </li>
                <li>
                    <a href="#"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition">
                        <span>⚙️</span> Pengaturan
                    </a>
                </li>
            </ul>
            @endif
        </aside>

        {{-- KONTEN UTAMA --}}
        <main class="flex-1 ml-64 p-6">
            {{-- PAGE HEADER --}}
            @isset($header)
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">{{ $header }}</h1>
            </div>
            @endisset

            @yield('content')
        </main>

    </div>

</body>
</html>