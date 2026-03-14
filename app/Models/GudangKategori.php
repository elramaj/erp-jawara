<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GudangKategori extends Model
{
    protected $table = 'gudang_kategori';
    public $timestamps = false;
    protected $fillable = ['nama', 'deskripsi'];

    public function barang()
    {
        return $this->hasMany(GudangBarang::class, 'kategori_id');
    }
}