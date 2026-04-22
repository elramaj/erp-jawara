@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">⚙️ Pengaturan</h1>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300">❌ {{ session('error') }}</div>
@endif
@if($errors->any())
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300">❌ {{ $errors->first() }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Departemen --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4">🏢 Manajemen Departemen</h2>
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
        <h2 class="font-semibold text-gray-700 mb-4">🕐 Pengaturan Jam Kerja</h2>
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
            <h2 class="font-semibold text-gray-700">🏭 Manajemen PT / Perusahaan</h2>
            <button onclick="toggleFormPT()"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                + Tambah PT
            </button>
        </div>

        {{-- Form Tambah/Edit PT --}}
        <div id="form-pt" class="border border-indigo-100 bg-indigo-50 rounded-lg p-4 mb-4 hidden">
            <p class="text-sm font-medium text-gray-700 mb-3" id="form-pt-title">➕ Tambah PT Baru</p>
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
                        <p class="text-xs font-semibold text-gray-600 mb-2 mt-1 border-t pt-3">📍 Lokasi Kantor (untuk Absensi GPS)</p>
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
                            💡 Ambil koordinat dari <a href="https://maps.google.com" target="_blank" class="underline font-semibold">Google Maps</a>
                            → klik kanan lokasi kantor → "What's here?" → salin angka koordinatnya.
                        </p>
                    </div>

                    {{-- ✅ TAMBAHAN: Status Aktif --}}
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
                                ✅ {{ $c->radius_meter }}m
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
                                {{-- ✅ TAMBAHAN: parameter is_active --}}
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
    document.getElementById('form-dept-title').textContent = '✏️ Edit Departemen';
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
    document.getElementById('form-pt-title').textContent = '➕ Tambah PT Baru';
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
    {{-- ✅ TAMBAHAN: reset checkbox --}}
    document.getElementById('pt-is-active').checked = true;
    document.getElementById('form-pt').classList.add('hidden');
}

{{-- ✅ TAMBAHAN: parameter isActive --}}
function editPT(id, kode, nama, telepon, email, alamat, latitude, longitude, radius, isActive) {
    document.getElementById('form-pt-title').textContent = '✏️ Edit PT';
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