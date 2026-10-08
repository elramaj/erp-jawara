@php
    $warna = [
        'antrian'   => 'bg-gray-200 text-gray-700',
        'disiapkan' => 'bg-blue-100 text-blue-700',
        'tayang'    => 'bg-green-100 text-green-700',
        'turun'     => 'bg-gray-200 text-gray-500',
    ][$status] ?? 'bg-gray-100 text-gray-600';
@endphp
<span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $warna }}">{{ \App\Models\Tayang::STATUS_LABEL[$status] ?? ucfirst($status) }}</span>
