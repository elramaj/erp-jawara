@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">📋 Stok Opname</h1>
        <p class="text-gray-500 text-sm mt-1">Cocokkan stok sistem dengan stok fisik di gudang.</p>
    </div>
    <a href="{{ route('gudang.index') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
        ← Kembali
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <form method="POST" action="{{ route('gudang.opname.store') }}">
        @csrf
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Barang</th>
                    <th class="px-4 py-3 text-center">Stok Sistem</th>
                    <th class="px-4 py-3 text-center">Stok Fisik</th>
                    <th class="px-4 py-3 text-center">Selisih</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="opname-table">
                @foreach($barang as $b)
                <tr>
                    <td class="px-4 py-3">
                        <input type="hidden" name="barang_id[]" value="{{ $b->id }}">
                        <p class="font-medium text-gray-800">{{ $b->nama_barang }}</p>
                        <p class="text-xs text-gray-400">{{ $b->kode_barang }} • {{ $b->satuan }}</p>
                    </td>
                    <td class="px-4 py-3 text-center font-bold text-indigo-600" id="sistem_{{ $b->id }}">
                        {{ $b->total_stok }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input type="number" name="stok_fisik[]"
                            value="{{ $b->total_stok }}" min="0"
                            class="w-24 border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-indigo-400"
                            onchange="hitungSelisih({{ $b->id }}, this.value, {{ $b->total_stok }})">
                    </td>
                    <td class="px-4 py-3 text-center font-semibold" id="selisih_{{ $b->id }}">
                        <span class="text-gray-400">0</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6 flex gap-3">
            <button type="submit"
                class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg text-sm font-semibold transition"
                onclick="return confirm('Yakin simpan hasil opname? Stok akan disesuaikan!')">
                💾 Simpan Hasil Opname
            </button>
            <a href="{{ route('gudang.index') }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-semibold transition">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
function hitungSelisih(id, fisik, sistem) {
    const selisih = parseInt(fisik) - parseInt(sistem);
    const el = document.getElementById('selisih_' + id);
    if (selisih > 0) {
        el.innerHTML = '<span class="text-green-600">+' + selisih + '</span>';
    } else if (selisih < 0) {
        el.innerHTML = '<span class="text-red-600">' + selisih + '</span>';
    } else {
        el.innerHTML = '<span class="text-gray-400">0</span>';
    }
}
</script>
@endsection