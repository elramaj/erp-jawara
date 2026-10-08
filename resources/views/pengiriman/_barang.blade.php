<div class="mb-3">
    <div class="flex justify-between text-xs text-gray-500 mb-1">
        <span>{{ $kel['item_lengkap'] }}/{{ $kel['total_item'] }} item lengkap</span>
        <span class="font-semibold">{{ $kel['persen'] }}%</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2">
        <div class="h-2 rounded-full {{ $kel['lengkap'] ? 'bg-green-500' : 'bg-indigo-500' }}" style="width: {{ $kel['persen'] }}%"></div>
    </div>
</div>

@if(!$kel['ada_po'])
<p class="text-sm text-gray-400">Belum ada PO aktif untuk proyek ini.</p>
@else
<div class="space-y-1 max-h-72 overflow-y-auto">
    @foreach($proyek->po as $po)
        @continue(in_array($po->status, ['draft', 'batal']))
        @foreach($po->detail as $d)
        @php $ok = $d->jumlah_diterima >= $d->jumlah; @endphp
        <div class="flex justify-between items-center text-xs border-b border-gray-100 py-1">
            <span class="text-gray-700 truncate pr-2">{{ $d->barang->nama_barang ?? 'Barang #' . $d->barang_id }}</span>
            <span class="{{ $ok ? 'text-green-600' : 'text-orange-600' }} font-semibold whitespace-nowrap">{{ $d->jumlah_diterima }}/{{ $d->jumlah }}</span>
        </div>
        @endforeach
    @endforeach
</div>
@endif
