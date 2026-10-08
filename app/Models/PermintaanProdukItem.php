<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanProdukItem extends Model
{
    protected $table = 'permintaan_produk_item';

    protected $fillable = ['permintaan_id', 'nama_kebutuhan', 'spesifikasi', 'jumlah', 'satuan'];

    public function permintaan() { return $this->belongsTo(PermintaanProduk::class, 'permintaan_id'); }
    public function opsi()       { return $this->hasMany(ProdukOpsi::class, 'item_id'); }
    public function opsiTerpilih() { return $this->hasOne(ProdukOpsi::class, 'item_id')->where('is_terpilih', true); }
}
