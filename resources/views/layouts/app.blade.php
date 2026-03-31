<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - ERP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.3/cdn.min.js"></script>
    <style>
        /* Sidebar */
        .sidebar { width: 256px; }

        /* Mobile */
        @media (max-width: 767px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0 !important; }
            .hide-mobile { display: none !important; }
        }

        /* Desktop */
        @media (min-width: 768px) {
            .hamburger { display: none !important; }
            .mobile-sidebar-overlay { display: none !important; }
            .mobile-sidebar { display: none !important; }
        }

        /* Mobile sidebar slide */
        .mobile-sidebar {
            position: fixed;
            top: 0; left: 0;
            height: 100%;
            width: 256px;
            background: #111827;
            z-index: 50;
            overflow-y: auto;
            transform: translateX(-100%);
            transition: transform 0.25s ease;
        }
        .mobile-sidebar.open {
            transform: translateX(0);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans" x-data="{ menuOpen: false }">

    {{-- NAVBAR --}}
    <nav class="bg-indigo-700 text-white px-4 py-3 flex justify-between items-center shadow-lg fixed w-full z-30">
        <div class="flex items-center gap-3">
            {{-- Hamburger mobile --}}
            <button class="hamburger p-1 rounded hover:bg-indigo-600 transition"
                @click="menuOpen = !menuOpen">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="text-xl">🏢</span>
            <span class="font-bold text-base tracking-wide">ERP Kantor</span>
        </div>
        <div class="flex items-center gap-2">

            {{-- Notifikasi --}}
            @php
                $notifIzin = 0;
                if (in_array(auth()->user()->role_id, [1, 11])) {
                    $notifIzin = \App\Models\PengajuanIzin::where('status','pending')->count();
                }
                $notifDeadline = \App\Models\Proyek::whereNotIn('status', ['selesai','dibatalkan'])
                    ->whereNotNull('deadline')
                    ->where('deadline', '<=', now()->addDays(7))
                    ->count();
                $totalNotif = $notifIzin + $notifDeadline;
            @endphp
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="relative p-1">
                    <svg class="h-6 w-6 text-indigo-200 hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if($totalNotif > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center font-bold">{{ $totalNotif }}</span>
                    @endif
                </button>
                <div x-show="open" x-transition
                    class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl z-50 overflow-hidden"
                    style="display:none;">
                    <div class="px-4 py-3 bg-indigo-600 text-white font-semibold text-sm">🔔 Notifikasi</div>
                    <div class="max-h-72 overflow-y-auto divide-y divide-gray-100">
                        @if(in_array(auth()->user()->role_id, [1, 11]) && $notifIzin > 0)
                        <a href="{{ route('izin.review') }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <span class="text-xl">📋</span>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $notifIzin }} Pengajuan Izin</p>
                                <p class="text-xs text-gray-400">Menunggu review</p>
                            </div>
                        </a>
                        @endif
                        @php
                        $proyekDeadline = \App\Models\Proyek::whereNotIn('status', ['selesai','dibatalkan'])
                            ->whereNotNull('deadline')->where('deadline', '<=', now()->addDays(7))
                            ->orderBy('deadline')->get();
                        @endphp
                        @foreach($proyekDeadline as $pd)
                        <a href="{{ route('proyek.show', $pd) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <span class="text-xl">{{ $pd->deadline->isPast() ? '🔴' : '⚠️' }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $pd->nama_proyek }}</p>
                                <p class="text-xs {{ $pd->deadline->isPast() ? 'text-red-500 font-semibold' : 'text-orange-500' }}">
                                    {{ $pd->deadline->isPast() ? 'Deadline terlewat!' : 'Deadline ' . $pd->deadline->diffForHumans() }}
                                </p>
                            </div>
                        </a>
                        @endforeach
                        @if($totalNotif == 0)
                        <div class="px-4 py-6 text-center text-gray-400 text-sm">Tidak ada notifikasi</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- User info (hide on mobile) --}}
            <div class="text-right hide-mobile">
                <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                <p class="text-xs text-indigo-200">{{ auth()->user()->role->name ?? 'Admin' }}</p>
            </div>
            <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center font-bold text-sm flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs bg-indigo-900 px-2 py-1.5 rounded-lg hover:bg-indigo-800 transition">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    {{-- Mobile overlay --}}
    <div class="mobile-sidebar-overlay fixed inset-0 bg-black bg-opacity-50 z-40"
        x-show="menuOpen" @click="menuOpen = false" style="display:none;"></div>

    {{-- Mobile sidebar --}}
    <div class="mobile-sidebar text-gray-300" :class="{ 'open': menuOpen }">
        <div class="flex items-center justify-between px-4 py-4 border-b border-gray-700">
            <div>
                <p class="font-bold text-white text-sm">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400">{{ auth()->user()->role->name ?? '-' }}</p>
            </div>
            <button @click="menuOpen = false" class="text-gray-400 hover:text-white p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @include('layouts.sidebar-menu')
    </div>

    <div class="flex pt-14 min-h-screen">

        {{-- Sidebar desktop --}}
        <aside class="sidebar bg-gray-900 text-gray-300 fixed top-14 bottom-0 overflow-y-auto shadow-xl z-10">
            @include('layouts.sidebar-menu')
        </aside>

        {{-- Konten --}}
        <main class="main-content flex-1 ml-64 p-4 md:p-6 min-w-0">
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