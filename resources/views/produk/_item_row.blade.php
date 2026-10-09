{{-- Satu baris kebutuhan. Nama field diatur ulang oleh JavaScript di halaman create (item[0], item[1], ...). --}}
<div class="item-row border rounded-lg p-4 grid grid-cols-1 md:grid-cols-6 gap-3">
    <div class="md:col-span-6 flex justify-between items-center -mb-1">
        <span class="item-no text-xs font-semibold text-gray-400 uppercase tracking-wide">Item</span>
        <button type="button" class="btn-hapus-item text-red-500 hover:text-red-700 text-xs font-semibold">Hapus item</button>
    </div>
    <div class="md:col-span-3">
        <label class="block text-xs text-gray-500 mb-1">Nama kebutuhan *</label>
        <input type="text" required data-f="nama_kebutuhan" name="item[{{ $i }}][nama_kebutuhan]" value="{{ $row['nama_kebutuhan'] ?? '' }}"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Jumlah *</label>
        <input type="number" min="1" required data-f="jumlah" name="item[{{ $i }}][jumlah]" value="{{ $row['jumlah'] ?? 1 }}"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs text-gray-500 mb-1">Satuan</label>
        <input type="text" data-f="satuan" name="item[{{ $i }}][satuan]" value="{{ $row['satuan'] ?? '' }}" placeholder="unit / pcs"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
    </div>
    <div class="md:col-span-6">
        <label class="block text-xs text-gray-500 mb-1">Spesifikasi dari customer</label>
        <textarea rows="3" data-f="spesifikasi" name="item[{{ $i }}][spesifikasi]" placeholder="Tempel spesifikasi yang diminta (prosesor, RAM, ukuran, TKDN, dll)"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ $row['spesifikasi'] ?? '' }}</textarea>
    </div>
</div>
