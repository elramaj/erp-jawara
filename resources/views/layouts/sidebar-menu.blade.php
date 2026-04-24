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
            <span>📊</span> Dashboard
        </a>
    </li>
    @if(auth()->user()->role_id == 11 || auth()->user()->role_id == 1)
    <li>
        <a href="{{ route('izin.review') }}"
           style="{{ request()->routeIs('izin.review') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('izin.review') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('izin.review') ? '' : "this.style.background=''" }}">
            <span>✅</span> Review Izin
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
            <span>📊</span> Rekap Absensi
        </a>
    </li>
    @endif
    <li>
        <a href="{{ route('profil.index') }}"
           style="{{ request()->routeIs('profil.index') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('profil.index') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('profil.index') ? '' : "this.style.background=''" }}">
            <span>👤</span> Profil Saya
        </a>
    </li>
    <li>
        <a href="{{ route('proyek.index') }}"
           style="{{ request()->routeIs('proyek.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('proyek.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('proyek.*') ? '' : "this.style.background=''" }}">
            <span>📁</span> Proyek
        </a>
    </li>
    @if(in_array(auth()->user()->role_id, [1, 4, 5, 7, 11]))
    <li>
        <a href="{{ route('komplain.index') }}"
           style="{{ request()->routeIs('komplain.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('komplain.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('komplain.*') ? '' : "this.style.background=''" }}">
            <span>⚠️</span> Komplain
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
            <span>🏭</span> Gudang
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
            <span>🛒</span> Sales Order
        </a>
    </li>
    @endif
    <li>
        <a href="{{ route('po.index') }}"
           style="{{ request()->routeIs('po.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('po.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('po.*') ? '' : "this.style.background=''" }}">
            <span>🛍️</span> Purchase Order
        </a>
    </li>
    <li>
        <a href="{{ route('customer.index') }}"
           style="{{ request()->routeIs('customer.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('customer.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('customer.*') ? '' : "this.style.background=''" }}">
            <span>👥</span> Customer
        </a>
    </li>
    <li>
        <a href="{{ route('supplier.index') }}"
           style="{{ request()->routeIs('supplier.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('supplier.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('supplier.*') ? '' : "this.style.background=''" }}">
            <span>🏪</span> Supplier
        </a>
    </li>
    @if(in_array(auth()->user()->role_id, [1, 2, 11]))
    <li>
        <a href="{{ route('laporan.keuangan') }}"
           style="{{ request()->routeIs('laporan.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('laporan.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('laporan.*') ? '' : "this.style.background=''" }}">
            <span>📊</span> Laporan
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
            <span>👥</span> Kelola Karyawan
        </a>
    </li>
    <li>
        <a href="{{ route('pengaturan.index') }}"
           style="{{ request()->routeIs('pengaturan.*') ? 'background:#dc2626;color:white;' : '' }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-gray-300 hover:text-white"
           onmouseover="{{ request()->routeIs('pengaturan.*') ? '' : "this.style.background='rgba(220,38,38,0.15)'" }}"
           onmouseout="{{ request()->routeIs('pengaturan.*') ? '' : "this.style.background=''" }}">
            <span>⚙️</span> Pengaturan
        </a>
    </li>
</ul>
@endif