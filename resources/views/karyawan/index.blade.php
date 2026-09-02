@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">👥 Manajemen Karyawan</h1>
    <a href="{{ route('karyawan.create') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
        + Tambah Karyawan
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg> {{ session('error') }}
</div>
@endif

@if(auth()->user()->isSuperAdmin())
<div class="bg-purple-50 border border-purple-300 text-purple-700 px-4 py-2 rounded-lg mb-4 text-sm font-semibold">
    <svg class="w-4 h-4 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" /></svg> Mode Super Admin — menampilkan data gabungan dari SEMUA company.
</div>
@endif

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Email</th>
                @if(auth()->user()->isSuperAdmin())
                <th class="px-4 py-3 text-left">Company</th>
                @endif
                <th class="px-4 py-3 text-left">Role</th>
                <th class="px-4 py-3 text-left">Departemen</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($karyawan as $k)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(substr($k->name, 0, 1)) }}
                        </div>
                        <span class="font-medium text-gray-800">{{ $k->name }}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $k->email }}</td>
                @if(auth()->user()->isSuperAdmin())
                <td class="px-4 py-3">
                    <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                        {{ $k->company->nama ?? '-' }}
                    </span>
                </td>
                @endif
                <td class="px-4 py-3">
                    <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-semibold capitalize">
                        {{ $k->role->name ?? '-' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $k->department->name ?? '-' }}</td>
                <td class="px-4 py-3">
                    @if($k->is_active)
                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-semibold">Aktif</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-semibold">Nonaktif</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('karyawan.edit', $k) }}"
                           class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-3 py-1 rounded text-xs font-semibold transition">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('karyawan.destroy', $k) }}"
                            onsubmit="return confirm('Yakin hapus karyawan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded text-xs font-semibold transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ auth()->user()->isSuperAdmin() ? 7 : 6 }}" class="px-4 py-8 text-center text-gray-400">Belum ada karyawan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection