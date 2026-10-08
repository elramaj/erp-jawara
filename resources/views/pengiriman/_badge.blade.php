@php
    $warna = [
        'diusulkan'  => 'bg-yellow-100 text-yellow-700',
        'revisi'     => 'bg-orange-100 text-orange-700',
        'terjadwal'  => 'bg-blue-100 text-blue-700',
        'dikirim'    => 'bg-indigo-100 text-indigo-700',
        'diterima'   => 'bg-green-100 text-green-700',
        'dibatalkan' => 'bg-gray-200 text-gray-600',
    ][$status] ?? 'bg-gray-100 text-gray-600';
@endphp
<span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $warna }}">
    {{ \App\Models\Pengiriman::STATUS_LABEL[$status] ?? ucfirst($status) }}
</span>
