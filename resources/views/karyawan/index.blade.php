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
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">
    ✅ {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300">
    ❌ {{ session('error') }}
</div>
@endif

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Email</th>
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
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada karyawan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection