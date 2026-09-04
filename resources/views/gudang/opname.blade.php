@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>Stok Opname</h1>
        <p class="text-gray-500 text-sm mt-1">Cocokkan stok sistem dengan stok fisik di gudang.</p>
    </div>
    <a href="{{ route('gudang.index') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5 w-fit">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg> Kembali
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
                <svg class="w-5 h-5 inline-block -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>Simpan Hasil Opname
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