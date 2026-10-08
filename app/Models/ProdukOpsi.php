<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukOpsi extends Model
{
    protected $table = 'produk_opsi';

    protected $fillable = [
        'item_id', 'nama_barang', 'merk', 'spesifikasi_ditawarkan',
        'harga_beli', 'harga_jual', 'supplier_nama', 'link_sumber', 'catatan',
        'is_terpilih', 'created_by',
    ];

    protected $casts = [
        'harga_beli'  => 'decimal:2',
        'harga_jual'  => 'decimal:2',
        'is_terpilih' => 'boolean',
    ];

    public function item() { return $this->belongsTo(PermintaanProdukItem::class, 'item_id'); }
}
