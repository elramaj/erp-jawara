<div class="px-4 py-3 mb-2">
    <p class="text-xs uppercase tracking-widest font-semibold" style="color:rgba(255,255,255,0.3);">Menu Utama</p>
</div>
<ul class="space-y-0.5 px-3">
    <li>
        <a href="{{ route('dashboard') }}"
           style="{{ request()->routeIs('dashboard') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('dashboard') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('dashboard') ? '' : "this.style.background=''" }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5V21h3.75v-7.5H3ZM10.125 9v12H13.5V9h-3.375ZM17.25 3v18H21V3h-3.75Z" /></svg> Dashboard
        </a>
    </li>
    @if(auth()->user()->role_id == 11 || auth()->user()->role_id == 1)
    <li>
        <a href="{{ route('izin.review') }}"
           style="{{ request()->routeIs('izin.review') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('izin.review') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('izin.review') ? '' : "this.style.background=''" }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg> Review Izin
            @php $pendingCount = \App\Models\PengajuanIzin::where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
            <span class="ml-auto text-white text-xs rounded-full px-1.5 py-0.5 font-bold" style="background:#dc2626;">
                {{ $pendingCount }}
            </span>
            @endif
        </a>
    </li>
    @endif
    @if(auth()->user()->role_id == 11)
    <li>
        <a href="{{ route('rekap.index') }}"
           style="{{ request()->routeIs('rekap.index') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('rekap.index') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('rekap.index') ? '' : "this.style.background=''" }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg> Rekap Absensi
        </a>
    </li>
    @endif
    <li>
        <a href="{{ route('profil.index') }}"
           style="{{ request()->routeIs('profil.index') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('profil.index') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('profil.index') ? '' : "this.style.background=''" }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg> Profil Saya
        </a>
    </li>
    <li>
        <a href="{{ route('proyek.index') }}"
           style="{{ request()->routeIs('proyek.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('proyek.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('proyek.*') ? '' : "this.style.background=''" }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-19.5 0v6a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25v-6m-19.5 0V6a2.25 2.25 0 0 1 2.25-2.25h5.379a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H19.5A2.25 2.25 0 0 1 21.75 9v3.75" /></svg> Proyek
        </a>
    </li>
    @if(in_array(auth()->user()->role_id, [1, 4, 5, 7, 11]))
    <li>
        <a href="{{ route('komplain.index') }}"
           style="{{ request()->routeIs('komplain.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('komplain.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('komplain.*') ? '' : "this.style.background=''" }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg> Komplain
        </a>
    </li>
    @endif
    @if(in_array(auth()->user()->role_id, [1, 2, 3, 4, 11]))
    <li>
        <a href="{{ route('gudang.index') }}"
           style="{{ request()->routeIs('gudang.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('gudang.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('gudang.*') ? '' : "this.style.background=''" }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg> Gudang
        </a>
    </li>
    @endif
</ul>

{{-- Keuangan --}}
@if(in_array(auth()->user()->role_id, [1, 2, 3, 11, 14]))
<div class="px-4 py-3 mt-4 mb-2">
    <p class="text-xs uppercase tracking-widest font-semibold" style="color:rgba(255,255,255,0.3);">Keuangan</p>
</div>
<ul class="space-y-0.5 px-3">
    @if(in_array(auth()->user()->role_id, [1, 2, 3, 11]))
    <li>
        <a href="{{ route('so.index') }}"
           style="{{ request()->routeIs('so.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('so.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('so.*') ? '' : "this.style.background=''" }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.994-4.665 2.615-7.108a1.125 1.125 0 0 0-1.087-1.394H5.121M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg> Sales Order
        </a>
    </li>
    @endif
    <li>
        <a href="{{ route('po.index') }}"
           style="{{ request()->routeIs('po.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('po.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('po.*') ? '' : "this.style.background=''" }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" /></svg> Purchase Order
        </a>
    </li>
    <li>
        <a href="{{ route('customer.index') }}"
           style="{{ request()->routeIs('customer.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('customer.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('customer.*') ? '' : "this.style.background=''" }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg> Customer
        </a>
    </li>
    <li>
        <a href="{{ route('supplier.index') }}"
           style="{{ request()->routeIs('supplier.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('supplier.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('supplier.*') ? '' : "this.style.background=''" }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.925-2.885A2.25 2.25 0 0 1 6.879 3h10.242a2.25 2.25 0 0 1 1.872 1.001l1.925 2.885a3.004 3.004 0 0 1-.621 4.72M9.75 21v-4.5a.75.75 0 0 1 .75-.75H12a.75.75 0 0 1 .75.75V21" /></svg> Supplier
        </a>
    </li>
    @if(in_array(auth()->user()->role_id, [1, 2, 11]))
    <li>
        <a href="{{ route('laporan.keuangan') }}"
           style="{{ request()->routeIs('laporan.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('laporan.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('laporan.*') ? '' : "this.style.background=''" }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12h-9m9-3.75h-9m5.625-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg> Laporan
        </a>
    </li>
    @endif
</ul>
@endif

{{-- Admin --}}
@if(auth()->user()->role_id == 11)
<div class="px-4 py-3 mt-4 mb-2">
    <p class="text-xs uppercase tracking-widest font-semibold" style="color:rgba(255,255,255,0.3);">Admin</p>
</div>
<ul class="space-y-0.5 px-3 pb-6">
    <li>
        <a href="{{ route('karyawan.index') }}"
           style="{{ request()->routeIs('karyawan.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('karyawan.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('karyawan.*') ? '' : "this.style.background=''" }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0ZM8.25 15a3.75 3.75 0 0 1 7.5 0v.375a.375.375 0 0 1-.375.375h-6.75a.375.375 0 0 1-.375-.375V15Z" /></svg> Kelola Karyawan
        </a>
    </li>
    <li>
        <a href="{{ route('pengaturan.index') }}"
           style="{{ request()->routeIs('pengaturan.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('pengaturan.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('pengaturan.*') ? '' : "this.style.background=''" }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg> Pengaturan
        </a>
    </li>
</ul>
@endif