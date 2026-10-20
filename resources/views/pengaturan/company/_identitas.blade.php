{{-- Identitas PT untuk dokumen cetak. Param opsional: $company (null saat tambah baru) --}}
@php
    $in = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400';
    $nilai = fn($k) => old($k, isset($company) ? $company->{$k} : null);
@endphp
<div class="border-t pt-3 mt-3 md:col-span-2">
    <p class="text-sm font-semibold text-gray-600 mb-2">Identitas &amp; Rekening untuk Dokumen Cetak</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NPWP</label>
            <input type="text" name="npwp" maxlength="30" value="{{ $nilai('npwp') }}" placeholder="00.000.000.0-000.000" class="{{ $in }}">
        </div>
        <div></div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
            <input type="text" name="bank_nama" maxlength="100" value="{{ $nilai('bank_nama') }}" placeholder="BCA / Mandiri / ..." class="{{ $in }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">No. Rekening</label>
            <input type="text" name="bank_rekening" maxlength="50" value="{{ $nilai('bank_rekening') }}" class="{{ $in }}">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Atas Nama Rekening</label>
            <input type="text" name="bank_atas_nama" maxlength="150" value="{{ $nilai('bank_atas_nama') }}" class="{{ $in }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penandatangan</label>
            <input type="text" name="penandatangan_nama" maxlength="150" value="{{ $nilai('penandatangan_nama') }}" class="{{ $in }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan Penandatangan</label>
            <input type="text" name="penandatangan_jabatan" maxlength="100" value="{{ $nilai('penandatangan_jabatan') }}" placeholder="Direktur" class="{{ $in }}">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Footer Dokumen</label>
            <textarea name="footer_dokumen" rows="2" maxlength="1000" placeholder="Mis. syarat pembayaran, garansi, atau catatan kaki lain" class="{{ $in }}">{{ $nilai('footer_dokumen') }}</textarea>
        </div>
    </div>
</div>
