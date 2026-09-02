<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AsetKategori extends Model
{
    protected $table = 'aset_kategori';
    public $timestamps = false;
    protected $fillable = ['nama', 'deskripsi'];

    public function aset()
    {
        return $this->hasMany(Aset::class, 'kategori_id');
    }
}