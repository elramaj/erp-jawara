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

            {{-- Notifikasi (khusus admin) --}}
            @if(auth()->user()->role_id == 11)
            @php $notifCount = \App\Models\PengajuanIzin::where('status','pending')->count(); @endphp
            <a href="{{ route('izin.review') }}" class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-200 hover:text-white transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                @if($notifCount > 0)
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center font-bold">
                    {{ $notifCount }}
                </span>
                @endif
            </a>
            @endif

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
        <aside class="w-64 bg-gray-900 text-gray-300 fixed top-14 bottom-0 overflow-y-auto shadow-xl">
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
                        @php $pendingCount = \App\Models\PengajuanIzin::where('status','pending')->count(); @endphp
                        @if($pendingCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 font-bold">
                            {{ $pendingCount }}
                        </span>
                        @endif
                    </a>
                </li>
                @endif
                @if(auth()->user()->role_id == 11)
                <li>
                    <a href="{{ route('rekap.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition">
                        <span>📊</span> Rekap Absensi
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('profil.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition">
                        <span>👤</span> Profil Saya
                    </a>
                </li>
                <li>
                    <a href="{{ route('proyek.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition">
                        <span>📁</span> Proyek
                    </a>
                </li>
                @if(in_array(auth()->user()->role_id, [1, 2, 3, 4, 11]))
                <li>
                    <a href="{{ route('gudang.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition">
                        <span>🏭</span> Gudang
                    </a>
                </li>
                @endif
            </ul>

            {{-- Keuangan --}}
            @if(in_array(auth()->user()->role_id, [1, 2, 3, 11, 14]))
            <div class="px-4 py-3 mt-4 mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold">Keuangan</p>
            </div>
            <ul class="space-y-0.5 px-3">
                {{-- SO hanya untuk finance, po, bos, admin --}}
                @if(in_array(auth()->user()->role_id, [1, 2, 3, 11]))
                <li>
                    <a href="{{ route('so.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition">
                        <span>🛒</span> Sales Order
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('po.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition">
                        <span>🛍️</span> Purchase Order
                    </a>
                </li>
                <li>
                    <a href="{{ route('customer.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition">
                        <span>👥</span> Customer
                    </a>
                </li>
                <li>
                    <a href="{{ route('supplier.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition">
                        <span>🏪</span> Supplier
                    </a>
                </li>
                {{-- Laporan hanya untuk finance, bos, admin --}}
                @if(in_array(auth()->user()->role_id, [1, 2, 11]))
                <li>
                    <a href="{{ route('laporan.keuangan') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition">
                        <span>📊</span> Laporan
                    </a>
                </li>
                @endif
            </ul>
            @endif

            {{-- Admin --}}
            @if(auth()->user()->role_id == 11)
            <div class="px-4 py-3 mt-4 mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold">Admin</p>
            </div>
            <ul class="space-y-0.5 px-3 pb-6">
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