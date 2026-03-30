<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GudangBarang extends Model
{
    protected $table = 'gudang_barang';
    protected $fillable = [
        'kode_barang', 'nama_barang', 'kategori_id',
        'satuan', 'stok_minimum', 'has_sn', 'deskripsi',
    ];

    public function kategori()
    {
        return $this->belongsTo(GudangKategori::class, 'kategori_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function stokMasuk()
    {
        return $this->hasMany(GudangStokMasuk::class, 'barang_id');
    }

    public function stokKeluar()
    {
        return $this->hasMany(GudangStokKeluar::class, 'barang_id');
    }

    public function serialNumbers()
    {
        return $this->hasMany(GudangSerialNumber::class, 'barang_id');
    }

    // Total stok tersedia (jumlah sisa semua batch)
    public function getTotalStokAttribute()
    {
        return $this->stokMasuk()->sum('sisa');
    }
}