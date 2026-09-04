@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><svg class="w-6 h-6 inline-block -mt-1 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.241.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.213-1.28Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>Pengaturan</h1>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg> {{ session('error') }}</div>
@endif
@if($errors->any())
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg> {{ $errors->first() }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Departemen --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>Manajemen Departemen</h2>
        <div class="space-y-2 mb-4">
            @forelse($departments as $d)
            <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                <div>
                    <p class="font-medium text-gray-700 text-sm">{{ $d->name }}</p>
                    @if($d->description)
                    <p class="text-xs text-gray-400">{{ $d->description }}</p>
                    @endif
                </div>
                <div class="flex gap-2">
                    <button onclick="editDepartment({{ $d->id }}, '{{ $d->name }}', '{{ $d->description }}')"
                        class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-2 py-1 rounded text-xs font-semibold transition">Edit</button>
                    <form method="POST" action="{{ route('pengaturan.department.destroy', $d) }}"
                        onsubmit="return confirm('Yakin hapus departemen ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded text-xs font-semibold transition">Hapus</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-3">Belum ada departemen.</p>
            @endforelse
        </div>
        <div class="border-t pt-4" id="form-department">
            <p class="text-sm font-medium text-gray-700 mb-2" id="form-dept-title">+ Tambah Departemen</p>
            <form method="POST" id="dept-form" action="{{ route('pengaturan.department.store') }}">
                @csrf
                <input type="hidden" name="_method" id="dept-method" value="POST">
                <input type="hidden" name="department_id" id="dept-id">
                <div class="space-y-2">
                    <input type="text" name="name" id="dept-name" placeholder="Nama departemen *"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                    <input type="text" name="description" id="dept-desc" placeholder="Deskripsi (opsional)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div class="flex gap-2 mt-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Simpan</button>
                    <button type="button" onclick="resetDeptForm()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">Reset</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Jam Kerja --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>Pengaturan Jam Kerja</h2>
        <form method="POST" action="{{ route('pengaturan.jamkerja') }}">
            @csrf
            <div class="space-y-3">
                @foreach($jamKerja as $j)
                <div class="border border-gray-100 rounded-lg p-3 {{ $j->is_libur ? 'bg-gray-50' : '' }}">
                    <input type="hidden" name="jam_kerja_id[]" value="{{ $j->id }}">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-semibold text-gray-700">{{ ucfirst($j->hari) }}</p>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_libur[]" value="{{ $j->id }}"
                                {{ $j->is_libur ? 'checked' : '' }}
                                class="w-4 h-4 text-indigo-600 rounded"
                                onchange="toggleHari(this, {{ $j->id }})">
                            <span class="text-xs text-gray-500">Hari Libur</span>
                        </label>
                    </div>
                    <div class="grid grid-cols-3 gap-2 hari-inputs-{{ $j->id }} {{ $j->is_libur ? 'opacity-40 pointer-events-none' : '' }}">
                        <div>
                            <label class="text-xs text-gray-400">Jam Masuk</label>
                            <input type="time" name="jam_masuk[]" value="{{ $j->jam_masuk }}"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="text-xs text-gray-400">Jam Keluar</label>
                            <input type="time" name="jam_keluar[]" value="{{ $j->jam_keluar }}"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="text-xs text-gray-400">Toleransi (menit)</label>
                            <input type="number" name="toleransi_menit[]" value="{{ $j->toleransi_menit }}" min="0"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-400">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="submit"
                class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg text-sm font-semibold transition">
                Simpan Jam Kerja
            </button>
        </form>
    </div>

    {{-- Manajemen PT --}}
    <div class="bg-white rounded-xl shadow p-6 lg:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-gray-700"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 21v-4.5m0 4.5h3.75m-3.75 0h-9m9-4.5V9m0 7.5H12m3.75-7.5V4.875c0-.621-.504-1.125-1.125-1.125h-3.75c-.621 0-1.125.504-1.125 1.125V9m4.5 0H12m0 0V4.875c0-.621-.504-1.125-1.125-1.125h-3.75c-.621 0-1.125.504-1.125 1.125V9m4.5 0H8.25M12 9v7.5m0-7.5H8.25m4.5 7.5H8.25m0-7.5V21m0-11.625v11.625m0 0H4.875c-.621 0-1.125-.504-1.125-1.125V9.375c0-.621.504-1.125 1.125-1.125H8.25" /></svg>Manajemen PT / Perusahaan</h2>
            <button onclick="toggleFormPT()"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                + Tambah PT
            </button>
        </div>

        {{-- Form Tambah/Edit PT --}}
        <div id="form-pt" class="border border-indigo-100 bg-indigo-50 rounded-lg p-4 mb-4 hidden">
            <p class="text-sm font-medium text-gray-700 mb-3" id="form-pt-title">+ Tambah PT Baru</p>
            <form method="POST" id="pt-form" action="{{ route('company.store') }}">
                @csrf
                <input type="hidden" name="_method" id="pt-method" value="POST">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Kode PT *</label>
                        <input type="text" name="kode" id="pt-kode" placeholder="PT-001"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Nama PT *</label>
                        <input type="text" name="nama" id="pt-nama" placeholder="PT Contoh Jaya"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Telepon</label>
                        <input type="text" name="telepon" id="pt-telepon"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Email</label>
                        <input type="email" name="email" id="pt-email"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-500">Alamat</label>
                        <input type="text" name="alamat" id="pt-alamat"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>

                    {{-- Lokasi Kantor per PT --}}
                    <div class="md:col-span-3">
                        <p class="text-xs font-semibold text-gray-600 mb-2 mt-1 border-t pt-3"><svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>Lokasi Kantor (untuk Absensi GPS)</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Latitude</label>
                        <input type="text" name="latitude" id="pt-latitude" placeholder="-7.2575"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Longitude</label>
                        <input type="text" name="longitude" id="pt-longitude" placeholder="112.7521"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Radius Absensi (meter)</label>
                        <input type="number" name="radius_meter" id="pt-radius" value="100" min="10" max="5000"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div class="md:col-span-3">
                        <p class="text-xs text-blue-600 bg-blue-50 border border-blue-200 rounded-lg p-2">
                            <svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" /></svg>Ambil koordinat dari <a href="https://maps.google.com" target="_blank" class="underline font-semibold">Google Maps</a>
                            → klik kanan lokasi kantor → "What's here?" → salin angka koordinatnya.
                        </p>
                    </div>

                    {{-- TAMBAHAN: Status Aktif --}}
                    <div class="md:col-span-3 border-t pt-3">
                        <input type="hidden" name="is_active" value="0">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" id="pt-is-active" value="1" checked
                                class="w-4 h-4 text-indigo-600 rounded">
                            <span class="text-sm font-medium text-gray-700">PT Aktif</span>
                        </label>
                    </div>
                </div>
                <div class="flex gap-2 mt-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Simpan</button>
                    <button type="button" onclick="resetFormPT()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</button>
                </div>
            </form>
        </div>

        {{-- List PT --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Nama PT</th>
                        <th class="px-4 py-3 text-left">Telepon</th>
                        <th class="px-4 py-3 text-center">Karyawan</th>
                        <th class="px-4 py-3 text-center">Lokasi GPS</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($companies as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ $c->kode }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $c->nama }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $c->telepon ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                                {{ $c->users_count }} orang
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($c->latitude)
                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                                <svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>{{ $c->radius_meter }}m
                            </span>
                            @else
                            <span class="bg-gray-100 text-gray-400 px-2 py-0.5 rounded-full text-xs">Belum diset</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $c->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $c->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 justify-center">
                                {{-- TAMBAHAN: parameter is_active --}}
                                <button onclick="editPT({{ $c->id }}, '{{ $c->kode }}', '{{ addslashes($c->nama) }}', '{{ $c->telepon }}', '{{ $c->email }}', '{{ addslashes($c->alamat) }}', '{{ $c->latitude }}', '{{ $c->longitude }}', '{{ $c->radius_meter }}', {{ $c->is_active ? 1 : 0 }})"
                                    class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-2 py-1 rounded text-xs font-semibold transition">Edit</button>
                                <form method="POST" action="{{ route('company.destroy', $c) }}"
                                    onsubmit="return confirm('Yakin hapus PT ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded text-xs font-semibold transition">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Belum ada PT.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function editDepartment(id, name, desc) {
    document.getElementById('form-dept-title').textContent = 'Edit Departemen';
    document.getElementById('dept-form').action = '/pengaturan/department/' + id;
    document.getElementById('dept-method').value = 'PUT';
    document.getElementById('dept-id').value = id;
    document.getElementById('dept-name').value = name;
    document.getElementById('dept-desc').value = desc ?? '';
    document.getElementById('form-department').scrollIntoView({ behavior: 'smooth' });
}

function resetDeptForm() {
    document.getElementById('form-dept-title').textContent = '+ Tambah Departemen';
    document.getElementById('dept-form').action = '{{ route('pengaturan.department.store') }}';
    document.getElementById('dept-method').value = 'POST';
    document.getElementById('dept-name').value = '';
    document.getElementById('dept-desc').value = '';
}

function toggleHari(checkbox, id) {
    const inputs = document.querySelector('.hari-inputs-' + id);
    if (checkbox.checked) {
        inputs.classList.add('opacity-40', 'pointer-events-none');
    } else {
        inputs.classList.remove('opacity-40', 'pointer-events-none');
    }
}

function toggleFormPT() {
    const form = document.getElementById('form-pt');
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        form.scrollIntoView({ behavior: 'smooth' });
    }
}

function resetFormPT() {
    document.getElementById('form-pt-title').textContent = '+ Tambah PT Baru';
    document.getElementById('pt-form').action = '{{ route('company.store') }}';
    document.getElementById('pt-method').value = 'POST';
    document.getElementById('pt-kode').value = '';
    document.getElementById('pt-nama').value = '';
    document.getElementById('pt-telepon').value = '';
    document.getElementById('pt-email').value = '';
    document.getElementById('pt-alamat').value = '';
    document.getElementById('pt-latitude').value = '';
    document.getElementById('pt-longitude').value = '';
    document.getElementById('pt-radius').value = '100';
    {{-- TAMBAHAN: reset checkbox --}}
    document.getElementById('pt-is-active').checked = true;
    document.getElementById('form-pt').classList.add('hidden');
}

{{-- TAMBAHAN: parameter isActive --}}
function editPT(id, kode, nama, telepon, email, alamat, latitude, longitude, radius, isActive) {
    document.getElementById('form-pt-title').textContent = 'Edit PT';
    document.getElementById('pt-form').action = '/company/' + id;
    document.getElementById('pt-method').value = 'PUT';
    document.getElementById('pt-kode').value = kode;
    document.getElementById('pt-nama').value = nama;
    document.getElementById('pt-telepon').value = telepon ?? '';
    document.getElementById('pt-email').value = email ?? '';
    document.getElementById('pt-alamat').value = alamat ?? '';
    document.getElementById('pt-latitude').value = latitude ?? '';
    document.getElementById('pt-longitude').value = longitude ?? '';
    document.getElementById('pt-radius').value = radius ?? 100;
    document.getElementById('pt-is-active').checked = isActive == 1;
    document.getElementById('form-pt').classList.remove('hidden');
    document.getElementById('form-pt').scrollIntoView({ behavior: 'smooth' });
}
</script>
@endsection