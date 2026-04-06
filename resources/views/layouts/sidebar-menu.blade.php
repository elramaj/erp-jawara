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
        <a href="{{ route('absensi.mobile') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('absensi.*') ? 'bg-indigo-600 text-white' : '' }}">
            <span>🕐</span> Absensi
        </a>
    </li>
    <li>
        <a href="{{ route('izin.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('izin.index') ? 'bg-indigo-600 text-white' : '' }}">
            <span>📋</span> Izin / Cuti
        </a>
    </li>
    @if(auth()->user()->role_id == 11 || auth()->user()->role_id == 1)
    <li>
        <a href="{{ route('izin.review') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('izin.review') ? 'bg-indigo-600 text-white' : '' }}">
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
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('rekap.index') ? 'bg-indigo-600 text-white' : '' }}">
            <span>📊</span> Rekap Absensi
        </a>
    </li>
    @endif
    <li>
        <a href="{{ route('profil.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('profil.index') ? 'bg-indigo-600 text-white' : '' }}">
            <span>👤</span> Profil Saya
        </a>
    </li>
    <li>
        <a href="{{ route('proyek.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('proyek.*') ? 'bg-indigo-600 text-white' : '' }}">
            <span>📁</span> Proyek
        </a>
    </li>
    @if(in_array(auth()->user()->role_id, [1, 4, 5, 7, 11]))
    <li>
        <a href="{{ route('komplain.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('komplain.*') ? 'bg-indigo-600 text-white' : '' }}">
            <span>⚠️</span> Komplain
        </a>
    </li>
    @endif
    @if(in_array(auth()->user()->role_id, [1, 2, 3, 4, 11]))
    <li>
        <a href="{{ route('gudang.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('gudang.*') ? 'bg-indigo-600 text-white' : '' }}">
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
    @if(in_array(auth()->user()->role_id, [1, 2, 3, 11]))
    <li>
        <a href="{{ route('so.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('so.*') ? 'bg-indigo-600 text-white' : '' }}">
            <span>🛒</span> Sales Order
        </a>
    </li>
    @endif
    <li>
        <a href="{{ route('po.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('po.*') ? 'bg-indigo-600 text-white' : '' }}">
            <span>🛍️</span> Purchase Order
        </a>
    </li>
    <li>
        <a href="{{ route('customer.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('customer.*') ? 'bg-indigo-600 text-white' : '' }}">
            <span>👥</span> Customer
        </a>
    </li>
    <li>
        <a href="{{ route('supplier.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('supplier.*') ? 'bg-indigo-600 text-white' : '' }}">
            <span>🏪</span> Supplier
        </a>
    </li>
    @if(in_array(auth()->user()->role_id, [1, 2, 11]))
    <li>
        <a href="{{ route('laporan.keuangan') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('laporan.*') ? 'bg-indigo-600 text-white' : '' }}">
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
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('karyawan.*') ? 'bg-indigo-600 text-white' : '' }}">
            <span>👥</span> Kelola Karyawan
        </a>
    </li>
    <li>
        <a href="{{ route('pengaturan.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-700 hover:text-white transition {{ request()->routeIs('pengaturan.*') ? 'bg-indigo-600 text-white' : '' }}">
            <span>⚙️</span> Pengaturan
        </a>
    </li>
</ul>
@endif