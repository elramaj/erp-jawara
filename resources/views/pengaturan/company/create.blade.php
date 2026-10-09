@extends('layouts.app')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>Tambah PT Baru</h1>
</div>
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('company.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode PT *</label>
                <input type="text" name="kode" value="{{ old('kode') }}" placeholder="PT-001"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                @error('kode')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama PT *</label>
                <input type="text" name="nama" value="{{ old('nama') }}" placeholder="PT Contoh Jaya"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                <input type="text" name="telepon" value="{{ old('telepon') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('alamat') }}</textarea>
            </div>
            <div class="md:col-span-2 border-t pt-3">
                <p class="text-sm font-semibold text-gray-600 mb-2">Kop Surat & Template Cetak</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo PT</label>
                        @if(isset($company) && $company->logo)
                        <div class="flex items-center gap-3 mb-2">
                            <img src="{{ $company->logo_url }}" alt="Logo" class="h-12 w-auto border rounded p-1 bg-white">
                            <label class="text-xs text-red-600 flex items-center gap-1"><input type="checkbox" name="hapus_logo" value="1"> Hapus logo</label>
                        </div>
                        @endif
                        <input type="file" name="logo" accept="image/png,image/jpeg,image/webp"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white">
                        <p class="text-xs text-gray-400 mt-1">PNG/JPG/WEBP, maks 2 MB. Dipakai di kop SO, PO, DO, dan Tanda Terima Service. Kosong = logo bawaan aplikasi.</p>
                        @error('logo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Template Cetak</label>
                        <select name="template_cetak" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm pr-8">
                            <option value="">Default (sama seperti PT pertama)</option>
                            @foreach($templates as $kunci => $label)
                            @continue($kunci === 'default')
                            <option value="{{ $kunci }}" {{ old('template_cetak', $company->template_cetak ?? '') === $kunci ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">PT baru otomatis memakai Default. Template baru dibuat dengan menyalin folder <code>resources/views/cetak/default</code>.</p>
                        @error('template_cetak')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition">Simpan</button>
            <a href="{{ route('company.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
        </div>
    </form>
</div>
@endsection