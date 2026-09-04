@extends('layouts.app')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><svg class="w-6 h-6 inline-block -mt-1 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" /></svg>Edit Customer</h1>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg> {{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('customer.update', $customer) }}">
@csrf @method('PUT')
<div class="bg-white rounded-xl shadow p-6 mb-4">
    <h2 class="font-semibold text-gray-700 mb-4 pb-2 border-b"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>Info Umum</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Kode Customer *</label>
            <input type="text" name="kode" value="{{ old('kode', $customer->kode) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
            @error('kode')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-500 mb-1">Nama Customer *</label>
            <input type="text" name="nama" value="{{ old('nama', $customer->nama) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Sales PIC</label>
            <input type="text" name="sales_pic" value="{{ old('sales_pic', $customer->sales_pic) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Termin Pembayaran</label>
            <input type="text" name="termin_pembayaran" value="{{ old('termin_pembayaran', $customer->termin_pembayaran) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Batas Jatuh Tempo (hari)</label>
            <input type="number" name="batas_jtempo" value="{{ old('batas_jtempo', $customer->batas_jtempo) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Batas Piutang (Rp)</label>
            <input type="number" name="batas_piutang" value="{{ old('batas_piutang', $customer->batas_piutang) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Rayon</label>
            <input type="text" name="rayon" value="{{ old('rayon', $customer->rayon) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">COA Piutang</label>
            <input type="text" name="coa_piutang" value="{{ old('coa_piutang', $customer->coa_piutang) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tipe Harga Jual</label>
            <select name="tipe_harga_jual"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 pr-8">
                @foreach([1,2,3,4,5] as $t)
                <option value="{{ $t }}" {{ old('tipe_harga_jual', $customer->tipe_harga_jual) == $t ? 'selected' : '' }}>Tipe {{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">No NPWP</label>
            <input type="text" name="no_npwp" value="{{ old('no_npwp', $customer->no_npwp) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Diskon (%)</label>
            <input type="number" name="diskon_persen" value="{{ old('diskon_persen', $customer->diskon_persen) }}" step="0.01" min="0" max="100"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Keterangan</label>
            <input type="text" name="keterangan" value="{{ old('keterangan', $customer->keterangan) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div class="flex items-end gap-6 pb-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="termasuk_supplier" value="1" {{ $customer->termasuk_supplier ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded">
                <span class="text-sm text-gray-700">Termasuk Supplier</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ $customer->is_active ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded">
                <span class="text-sm text-gray-700">Aktif</span>
            </label>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6 mb-4">
    <h2 class="font-semibold text-gray-700 mb-4 pb-2 border-b"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>Detail Alamat & Kontak</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Lokasi</label>
            <input type="text" name="lokasi" value="{{ old('lokasi', $customer->lokasi) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Kontak</label>
            <input type="text" name="kontak" value="{{ old('kontak', $customer->kontak) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Lahir</label>
            <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir', $customer->tgl_lahir?->format('Y-m-d')) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Bank Account</label>
            <input type="text" name="bank_account" value="{{ old('bank_account', $customer->bank_account) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div class="md:col-span-3">
            <label class="block text-xs font-medium text-gray-500 mb-1">Alamat Baris 1</label>
            <input type="text" name="alamat1" value="{{ old('alamat1', $customer->alamat1) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div class="md:col-span-3">
            <label class="block text-xs font-medium text-gray-500 mb-1">Alamat Baris 2</label>
            <input type="text" name="alamat2" value="{{ old('alamat2', $customer->alamat2) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div class="md:col-span-3">
            <label class="block text-xs font-medium text-gray-500 mb-1">Alamat Baris 3</label>
            <input type="text" name="alamat3" value="{{ old('alamat3', $customer->alamat3) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Kota</label>
            <input type="text" name="kota" value="{{ old('kota', $customer->kota) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Propinsi</label>
            <input type="text" name="propinsi" value="{{ old('propinsi', $customer->propinsi) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-4">
        @foreach([1,2,3,4,5] as $i)
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Phone #{{ $i }}</label>
            <input type="text" name="phone{{ $i }}" value="{{ old('phone'.$i, $customer->{'phone'.$i}) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        @endforeach
    </div>
    <div class="grid grid-cols-2 gap-3 mt-3 max-w-xs">
        @foreach([1,2] as $i)
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Fax #{{ $i }}</label>
            <input type="text" name="fax{{ $i }}" value="{{ old('fax'.$i, $customer->{'fax'.$i}) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        @endforeach
    </div>
    <div class="flex gap-6 mt-4 pt-4 border-t">
        <p class="text-xs font-semibold text-gray-500 self-center">Default Alamat:</p>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="default_kirim" value="1" {{ $customer->default_kirim ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded">
            <span class="text-sm text-gray-700">Kirim</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="default_tagihan" value="1" {{ $customer->default_tagihan ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded">
            <span class="text-sm text-gray-700">Tagihan</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="default_pajak" value="1" {{ $customer->default_pajak ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded">
            <span class="text-sm text-gray-700">Pajak</span>
        </label>
    </div>
</div>

<div class="flex gap-3">
    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition">Update</button>
    <a href="{{ route('customer.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
</div>
</form>
@endsection