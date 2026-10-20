{{-- Rekening pembayaran PT; tidak tampil kalau belum diisi --}}
@if(!empty($perusahaan->bank_rekening))
<div class="catatan"><strong>Pembayaran ke:</strong> {{ $perusahaan->bank_nama }} {{ $perusahaan->bank_rekening }}@if(!empty($perusahaan->bank_atas_nama)) a.n. {{ $perusahaan->bank_atas_nama }}@endif</div>
@endif
