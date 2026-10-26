@php
    $warna = [
        'baru'       => 'bg-gray-200 text-gray-700',
        'diproses'   => 'bg-blue-100 text-blue-700',
        'selesai'    => 'bg-green-100 text-green-700',
        'dibatalkan' => 'bg-gray-200 text-gray-500',
    ][$status] ?? 'bg-gray-100 text-gray-600';
@endphp
<span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $warna }}">{{ \App\Models\PermintaanDokumen::STATUS_LABEL[$status] ?? ucfirst($status) }}</span>
