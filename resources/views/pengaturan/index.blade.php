@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">⚙️ Pengaturan</h1>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300">✅ {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300">❌ {{ $errors->first() }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Departemen --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4">🏢 Manajemen Departemen</h2>

        {{-- List Departemen --}}
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
                        class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-2 py-1 rounded text-xs font-semibold transition">
                        Edit
                    </button>
                    <form method="POST" action="{{ route('pengaturan.department.destroy', $d) }}"
                        onsubmit="return confirm('Yakin hapus departemen ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded text-xs font-semibold transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-3">Belum ada departemen.</p>
            @endforelse
        </div>

        {{-- Form Tambah --}}
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
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Simpan
                    </button>
                    <button type="button" onclick="resetDeptForm()"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Reset
                    </button>
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
</script>
@endsection