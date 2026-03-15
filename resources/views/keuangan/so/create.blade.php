@extends('layouts.app')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">➕ Buat Sales Order</h1>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <form method="POST" action="{{ route('so.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No SO *</label>
                <input type="text" name="no_so" value="{{ old('no_so', $no_so) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                @error('no_so')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
                <select name="customer_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                    <option value="">-- Pilih Customer --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Proyek (opsional)</label>
                <select name="proyek_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">-- Pilih Proyek --</option>
                    @foreach($proyek as $p)
                    <option value="{{ $p->id }}" {{ old('proyek_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_proyek }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <input type="text" name="catatan" value="{{ old('catatan') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
        </div>

        {{-- Detail Barang --}}
        <h2 class="font-semibold text-gray-700 mb-3">📦 Detail Barang</h2>
        <div class="overflow-x-auto mb-4">
            <table class="w-full text-sm" id="tabel-barang">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-3 py-2 text-left">Barang</th>
                        <th class="px-3 py-2 text-center w-24">Stok</th>
                        <th class="px-3 py-2 text-center w-24">Jumlah</th>
                        <th class="px-3 py-2 text-right w-40">Harga Satuan</th>
                        <th class="px-3 py-2 text-right w-40">Subtotal</th>
                        <th class="px-3 py-2 w-10"></th>
                    </tr>
                </thead>
                <tbody id="detail-body">
                    <tr class="detail-row">
                        <td class="px-3 py-2">
                            <select name="barang_id[]" onchange="updateStok(this)"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-400" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach($barang as $b)
                                <option value="{{ $b->id }}" data-stok="{{ $b->stok }}" data-harga="0">{{ $b->nama_barang }} ({{ $b->satuan }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-3 py-2 text-center stok-cell text-gray-400">-</td>
                        <td class="px-3 py-2">
                            <input type="number" name="jumlah[]" min="1" value="1" onchange="hitungSubtotal(this)"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-1 focus:ring-indigo-400" required>
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" name="harga[]" min="0" value="0" onchange="hitungSubtotal(this)"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm text-right focus:outline-none focus:ring-1 focus:ring-indigo-400" required>
                        </td>
                        <td class="px-3 py-2 text-right font-semibold subtotal-cell text-gray-700">Rp 0</td>
                        <td class="px-3 py-2 text-center">
                            <button type="button" onclick="hapusBaris(this)" class="text-red-400 hover:text-red-600 font-bold text-lg">×</button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="border-t-2">
                        <td colspan="4" class="px-3 py-2 text-right font-semibold text-gray-700">Total:</td>
                        <td class="px-3 py-2 text-right font-bold text-indigo-600" id="grand-total">Rp 0</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <button type="button" onclick="tambahBaris()"
            class="mb-6 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
            + Tambah Baris
        </button>

        <div class="flex gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition">
                Simpan SO
            </button>
            <a href="{{ route('so.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-semibold transition">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
const barangData = @json($barang->keyBy('id'));

function updateStok(select) {
    const row = select.closest('tr');
    const id = select.value;
    const stok = id && barangData[id] ? barangData[id].stok : '-';
    row.querySelector('.stok-cell').textContent = stok;
}

function hitungSubtotal(input) {
    const row = input.closest('tr');
    const jumlah = parseFloat(row.querySelector('input[name="jumlah[]"]').value) || 0;
    const harga  = parseFloat(row.querySelector('input[name="harga[]"]').value) || 0;
    const subtotal = jumlah * harga;
    row.querySelector('.subtotal-cell').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    hitungTotal();
}

function hitungTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal-cell').forEach(el => {
        total += parseFloat(el.textContent.replace(/[^0-9]/g, '')) || 0;
    });
    document.getElementById('grand-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function tambahBaris() {
    const tbody = document.getElementById('detail-body');
    const baris = tbody.querySelector('.detail-row').cloneNode(true);
    baris.querySelector('select').value = '';
    baris.querySelector('.stok-cell').textContent = '-';
    baris.querySelector('input[name="jumlah[]"]').value = 1;
    baris.querySelector('input[name="harga[]"]').value = 0;
    baris.querySelector('.subtotal-cell').textContent = 'Rp 0';
    tbody.appendChild(baris);
}

function hapusBaris(btn) {
    const rows = document.querySelectorAll('.detail-row');
    if (rows.length > 1) {
        btn.closest('tr').remove();
        hitungTotal();
    }
}
</script>
@endsection