@extends('layouts.app')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">➕ Tambah Customer</h1>
</div>

@if($errors->any())
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300">❌ {{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('customer.store') }}">
@csrf
<div class="bg-white rounded-xl shadow p-6 mb-4">
    <h2 class="font-semibold text-gray-700 mb-4 pb-2 border-b">📋 Info Umum</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Kode Customer *</label>
            <input type="text" name="kode" value="{{ old('kode', $kode) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
            @error('kode')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-500 mb-1">Nama Customer *</label>
            <input type="text" name="nama" value="{{ old('nama') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
            @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Sales PIC</label>
            <input type="text" name="sales_pic" value="{{ old('sales_pic') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Termin Pembayaran</label>
            <input type="text" name="termin_pembayaran" value="{{ old('termin_pembayaran', 'T0') }}" placeholder="T0, T30, T60..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Batas Jatuh Tempo (hari)</label>
            <input type="number" name="batas_jtempo" value="{{ old('batas_jtempo', 0) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Batas Piutang (Rp)</label>
            <input type="number" name="batas_piutang" value="{{ old('batas_piutang') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Rayon</label>
            <input type="text" name="rayon" value="{{ old('rayon') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">COA Piutang</label>
            <input type="text" name="coa_piutang" value="{{ old('coa_piutang', '101001') }}" placeholder="101001"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tipe Harga Jual</label>
            <select name="tipe_harga_jual"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                @foreach([1,2,3,4,5] as $t)
                <option value="{{ $t }}" {{ old('tipe_harga_jual', 1) == $t ? 'selected' : '' }}>Tipe {{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">No NPWP</label>
            <input type="text" name="no_npwp" value="{{ old('no_npwp') }}" placeholder="00.000.000.0-000.000"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Diskon (%)</label>
            <input type="number" name="diskon_persen" value="{{ old('diskon_persen', 0) }}" step="0.01" min="0" max="100"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Keterangan</label>
            <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div class="flex items-end gap-6 pb-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="termasuk_supplier" value="1" {{ old('termasuk_supplier') ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded">
                <span class="text-sm text-gray-700">Termasuk Supplier</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-indigo-600 rounded">
                <span class="text-sm text-gray-700">Aktif</span>
            </label>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6 mb-4">
    <h2 class="font-semibold text-gray-700 mb-4 pb-2 border-b">📍 Detail Alamat & Kontak</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Lokasi</label>
            <input type="text" name="lokasi" value="{{ old('lokasi') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Kontak</label>
            <input type="text" name="kontak" value="{{ old('kontak') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Lahir</label>
            <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Bank Account</label>
            <input type="text" name="bank_account" value="{{ old('bank_account') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div class="md:col-span-3">
            <label class="block text-xs font-medium text-gray-500 mb-1">Alamat Baris 1</label>
            <input type="text" name="alamat1" value="{{ old('alamat1') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div class="md:col-span-3">
            <label class="block text-xs font-medium text-gray-500 mb-1">Alamat Baris 2</label>
            <input type="text" name="alamat2" value="{{ old('alamat2') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div class="md:col-span-3">
            <label class="block text-xs font-medium text-gray-500 mb-1">Alamat Baris 3</label>
            <input type="text" name="alamat3" value="{{ old('alamat3') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Kota</label>
            <input type="text" name="kota" value="{{ old('kota') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Propinsi</label>
            <input type="text" name="propinsi" value="{{ old('propinsi') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-4">
        @foreach([1,2,3,4,5] as $i)
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Phone #{{ $i }}</label>
            <input type="text" name="phone{{ $i }}" value="{{ old('phone'.$i) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        @endforeach
    </div>
    <div class="grid grid-cols-2 gap-3 mt-3 max-w-xs">
        @foreach([1,2] as $i)
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Fax #{{ $i }}</label>
            <input type="text" name="fax{{ $i }}" value="{{ old('fax'.$i) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        @endforeach
    </div>
    <div class="flex gap-6 mt-4 pt-4 border-t">
        <p class="text-xs font-semibold text-gray-500 self-center">Default Alamat:</p>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="default_kirim" value="1" class="w-4 h-4 text-indigo-600 rounded">
            <span class="text-sm text-gray-700">Kirim</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="default_tagihan" value="1" class="w-4 h-4 text-indigo-600 rounded">
            <span class="text-sm text-gray-700">Tagihan</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="default_pajak" value="1" class="w-4 h-4 text-indigo-600 rounded">
            <span class="text-sm text-gray-700">Pajak</span>
        </label>
    </div>
</div>

<div class="flex gap-3">
    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition">Simpan</button>
    <a href="{{ route('customer.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
</div>
</form>
@endsection