<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - ERP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif !important; }

                /* Sidebar */
        .sidebar { width: 256px; transition: transform 0.25s ease; }
        .sidebar.collapsed { transform: translateX(-256px); }
        .main-content { transition: margin-left 0.25s ease; }

        @media (max-width: 767px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0 !important; }
            .hide-mobile { display: none !important; }
            .desktop-toggle { display: none !important; }
        }

        @media (min-width: 768px) {
            .hamburger { display: none !important; }
            .mobile-sidebar-overlay { display: none !important; }
            .mobile-sidebar { display: none !important; }
        }

        .mobile-sidebar {
            position: fixed;
            top: 0; left: 0;
            height: 100%;
            width: 256px;
            background: #1a0a0a;
            z-index: 50;
            overflow-y: auto;
            transform: translateX(-100%);
            transition: transform 0.25s ease;
        }
        .mobile-sidebar.open { transform: translateX(0); }

        /* Notif dropdown aktif indikator */
        .notif-header { background: #dc2626 !important; }

        /* Active menu item */
        .menu-active {
            background: #dc2626 !important;
            color: white !important;
        }

        /* Sidebar menu hover */
        .sidebar-link:hover {
            background: rgba(220,38,38,0.15) !important;
            color: #fca5a5 !important;
        }
    </style>
</head>
<body class="bg-gray-100" x-data="{ menuOpen: false, sidebarCollapsed: false }">

    {{-- NAVBAR --}}
    <nav style="background:#dc2626;" class="text-white px-4 py-3 flex justify-between items-center shadow-lg fixed w-full z-30">
        <div class="flex items-center gap-3">
                        <button class="desktop-toggle p-1 rounded transition" style="hover:background:rgba(0,0,0,0.1);"
                @click="sidebarCollapsed = !sidebarCollapsed" title="Sembunyikan/tampilkan menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <img src="{{ asset('images/logo.png') }}" alt="Logo"
                style="height:32px;filter:brightness(0) invert(1);">
            <span class="font-bold text-base tracking-wide">{{ config('app.name') }}</span>
        </div>
        <div class="flex items-center gap-2">

            {{-- Notifikasi --}}
                        @php
                $notifIzin = 0;
                if (in_array(auth()->user()->role_id, [1, 11])) {
                    $notifIzin = \App\Models\PengajuanIzin::where('status','pending')->count();
                }
                $notifTugas = \App\Services\TugasService::ringkasan(auth()->user());
                $notifDeadline = $notifTugas['deadline']->count();
                $notifTugasLain = $notifTugas['reimburse']->count() + $notifTugas['tagihan_jual']->count() + $notifTugas['tagihan_beli']->count()
                    + $notifTugas['komplain']->count() + $notifTugas['po']->count();
                $notifKomplain = \App\Models\Komplain::where('handled_by', auth()->id())
                    ->where('status', '!=', 'resolved')
                    ->count();
                $notifPengirimanData = \App\Services\PengirimanService::ringkasan(auth()->user());
                $notifPengirimanTotal = collect($notifPengirimanData)->sum(fn($c) => $c->count());
                $notifPT = \App\Services\ProdukTayangService::ringkasan(auth()->user());
                $notifPTTotal = collect($notifPT)->sum(fn($c) => $c->count());
                $notifDesain = \App\Services\DesainService::ringkasan(auth()->user());
                $notifDokumen = \App\Services\DokumenService::ringkasan(auth()->user());
                $notifDesainTotal = collect($notifDesain)->sum(fn($c) => $c->count()) + collect($notifDokumen)->sum(fn($c) => $c->count());
                $totalNotif = $notifIzin + $notifDeadline + $notifKomplain + $notifPengirimanTotal + $notifPTTotal + $notifDesainTotal + $notifTugasLain;
            @endphp
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="relative p-1">
                    <svg class="h-6 w-6" style="color:rgba(255,255,255,0.8);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if($totalNotif > 0)
                    <span class="absolute -top-1 -right-1 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center font-bold" style="background:#7f1d1d;">{{ $totalNotif }}</span>
                    @endif
                </button>
                <div x-show="open" x-transition
                    class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl z-50 overflow-hidden"
                    style="display:none;">
                    <div class="px-4 py-3 text-white font-semibold text-sm flex items-center gap-2" style="background:#dc2626;">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                        Notifikasi
                    </div>
                    <div class="max-h-72 overflow-y-auto divide-y divide-gray-100">
                        @if(in_array(auth()->user()->role_id, [1, 11]) && $notifIzin > 0)
                        <a href="{{ route('izin.review') }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $notifIzin }} Pengajuan Izin</p>
                                <p class="text-xs text-gray-400">Menunggu review</p>
                            </div>
                        </a>
                        @endif
                        @foreach($notifPT['produk'] as $nq)
                        <a href="{{ route('produk.show', $nq) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $nq->judul }}</p>
                                <p class="text-xs text-blue-600">{{ $nq->status === 'baru' ? 'Permintaan baru dari Sales' : ($nq->alasan_revisi ? 'Sales minta opsi lain' : 'Sedang dicarikan') }}</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifPT['konfirmasi'] as $nc)
                        <a href="{{ route('produk.show', $nc) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $nc->judul }}</p>
                                <p class="text-xs text-yellow-600">Opsi barang siap, menunggu pilihan Sales</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifPT['tayang'] as $nt)
                        <a href="{{ route('tayang.show', $nt) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $nt->judul }}</p>
                                <p class="text-xs text-green-600">{{ $nt->status === 'antrian' ? 'Antrian tayang baru' : 'Sedang disiapkan' }}</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifPT['desain'] as $nd)
                        <a href="{{ route('tayang.show', $nd) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-purple-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $nd->judul }}</p>
                                <p class="text-xs text-purple-600">Butuh desain untuk tayang</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifDesain['tugas'] as $nx)
                        <a href="{{ route('desain.show', $nx) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-purple-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $nx->judul }}</p>
                                <p class="text-xs text-purple-600">{{ $nx->status === 'baru' ? 'Permintaan desain baru' : ($nx->status === 'revisi' ? 'Perlu direvisi' : 'Sedang kamu kerjakan') }}</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifDesain['review'] as $nx)
                        <a href="{{ route('desain.show', $nx) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $nx->judul }}</p>
                                <p class="text-xs text-yellow-600">Hasil desain siap, menunggu review kamu</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifDokumen['siapkan'] as $nx)
                        <a href="{{ route('dokumen.show', $nx) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $nx->jenis_dokumen }}</p>
                                <p class="text-xs text-blue-600">{{ $nx->status === 'baru' ? 'Permintaan dokumen baru' : 'Sedang disiapkan' }}{{ $nx->deadline ? ' · deadline ' . $nx->deadline->format('d M') : '' }}</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifDokumen['selesai'] as $nx)
                        <a href="{{ route('dokumen.show', $nx) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $nx->jenis_dokumen }}</p>
                                <p class="text-xs text-green-600">Dokumen sudah siap diunduh</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifDokumen['arsip'] as $nx)
                        <a href="{{ route('arsip.index', ['tampil' => $nx->status_masa === 'kedaluwarsa' ? 'kedaluwarsa' : 'segera']) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 {{ $nx->status_masa === 'kedaluwarsa' ? 'text-red-500' : 'text-orange-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $nx->nama }}</p>
                                <p class="text-xs {{ $nx->status_masa === 'kedaluwarsa' ? 'text-red-500 font-semibold' : 'text-orange-500' }}">{{ $nx->status_masa === 'kedaluwarsa' ? 'Sudah kedaluwarsa' : 'Berakhir ' . $nx->tanggal_kedaluwarsa->format('d M Y') . ' (' . $nx->sisa_hari . ' hari)' }}</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifPengirimanData['siap'] as $np)
                        <a href="{{ route('pengiriman.create', $np['proyek']) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $np['proyek']->nama_proyek }}</p>
                                <p class="text-xs text-indigo-600">Perlu dijadwalkan: {{ $np['alasan'] }}</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifPengirimanData['konfirmasi'] as $nk)
                        <a href="{{ route('pengiriman.show', $nk) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $nk->proyek->nama_proyek }}</p>
                                <p class="text-xs text-yellow-600">Jadwal kirim {{ $nk->tanggal_kirim->format('d M') }} menunggu konfirmasi</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifPengirimanData['revisi'] as $nr)
                        <a href="{{ route('pengiriman.show', $nr) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $nr->proyek->nama_proyek }}</p>
                                <p class="text-xs text-orange-600">Jadwal kirim diminta diubah (Sales/Gudang)</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifTugas['reimburse'] as $nt2)
                        <a href="{{ route('reimburse.review') }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">Reimburse {{ $nt2->user->name ?? '-' }} · Rp {{ number_format($nt2->nominal, 0, ',', '.') }}</p>
                                <p class="text-xs text-teal-600">Menunggu persetujuan</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifTugas['tagihan_jual'] as $nt2)
                        <a href="{{ $nt2->so_id ? route('so.show', $nt2->so_id) : route('so.index') }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">Tagihan customer {{ $nt2->no_fj }}</p>
                                <p class="text-xs text-green-600">{{ $nt2->jatuh_tempo->isPast() && !$nt2->jatuh_tempo->isToday() ? 'Lewat jatuh tempo ' . (int) abs($nt2->jatuh_tempo->diffInDays(now()->startOfDay())) . ' hari' : 'Jatuh tempo ' . $nt2->jatuh_tempo->format('d M') }} · sisa Rp {{ number_format($nt2->sisa, 0, ',', '.') }}</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifTugas['tagihan_beli'] as $nt2)
                        <a href="{{ $nt2->po_id ? route('po.show', $nt2->po_id) : route('po.index') }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">Tagihan supplier {{ $nt2->no_fb }}{{ optional(optional($nt2->po)->supplier)->nama ? ' · ' . $nt2->po->supplier->nama : '' }}</p>
                                <p class="text-xs text-rose-600">{{ $nt2->jatuh_tempo->isPast() && !$nt2->jatuh_tempo->isToday() ? 'Lewat jatuh tempo ' . (int) abs($nt2->jatuh_tempo->diffInDays(now()->startOfDay())) . ' hari' : 'Jatuh tempo ' . $nt2->jatuh_tempo->format('d M') }} · sisa Rp {{ number_format($nt2->sisa, 0, ',', '.') }}</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifTugas['komplain'] as $nt2)
                        <a href="{{ route('komplain.show', $nt2) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $nt2->judul }}</p>
                                <p class="text-xs text-purple-600">Komplain baru, belum ditugaskan · {{ $nt2->no_komplain }}</p>
                            </div>
                        </a>
                        @endforeach
                        @foreach($notifTugas['po'] as $nt2)
                        <a href="{{ route('penerimaan.show', $nt2) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-sky-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $nt2->no_po }}{{ optional($nt2->supplier)->nama ? ' · ' . $nt2->supplier->nama : '' }}</p>
                                <p class="text-xs text-sky-600">{{ $nt2->status === 'partial' ? 'Barang datang sebagian, menunggu sisanya' : 'Menunggu barang datang' }}</p>
                            </div>
                        </a>
                        @endforeach
                        @php $proyekDeadline = $notifTugas['deadline']; @endphp
                        @foreach($proyekDeadline as $pd)
                        <a href="{{ route('proyek.show', $pd) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 {{ $pd->deadline->isPast() ? 'text-red-500' : 'text-orange-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $pd->nama_proyek }}</p>
                                <p class="text-xs {{ $pd->deadline->isPast() ? 'text-red-500 font-semibold' : 'text-orange-500' }}">
                                    {{ $pd->deadline->isPast() ? 'Deadline terlewat!' : 'Deadline ' . $pd->deadline->diffForHumans() }}
                                </p>
                            </div>
                        </a>
                        @endforeach
                                                @php
                        $komplainSaya = \App\Models\Komplain::where('handled_by', auth()->id())
                            ->where('status', '!=', 'resolved')
                            ->orderBy('created_at', 'desc')->get();
                        @endphp
                        @foreach($komplainSaya as $k)
                        <a href="{{ route('komplain.show', $k) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 flex-shrink-0 text-purple-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.569 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" /></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $k->judul }}</p>
                                <p class="text-xs text-gray-400">{{ $k->no_komplain }} · {{ ucfirst(str_replace('_',' ',$k->status)) }}</p>
                            </div>
                        </a>
                        @endforeach
                        @if($totalNotif == 0)
                        <div class="px-4 py-6 text-center text-gray-400 text-sm">Tidak ada notifikasi</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- User info --}}
            <div class="text-right hide-mobile">
                <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                <p class="text-xs" style="color:rgba(255,255,255,0.7);">{{ auth()->user()->isSuperAdmin() ? 'Super Admin' : (auth()->user()->role->name ?? '-') }}</p>
            </div>
            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0 text-white" style="background:rgba(0,0,0,0.2);">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs px-2 py-1.5 rounded-lg transition text-white" style="background:rgba(0,0,0,0.2);">
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
        <div class="flex items-center justify-between px-4 py-4" style="border-bottom:1px solid rgba(255,255,255,0.1);">
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
                <aside class="sidebar fixed top-14 bottom-0 overflow-y-auto shadow-xl z-10 text-gray-300" :class="{ 'collapsed': sidebarCollapsed }" style="background:#1a0a0a;">
            @include('layouts.sidebar-menu')
        </aside>

        {{-- Konten --}}
                <main class="main-content flex-1 p-4 md:p-6 min-w-0" :style="sidebarCollapsed ? 'margin-left:0' : 'margin-left:16rem'">
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