<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SoDetail extends Model
{
    protected $table = 'so_detail';
    public $timestamps = false;
    protected $fillable = ['so_id', 'barang_id', 'jumlah', 'harga'];

    public function barang() { return $this->belongsTo(GudangBarang::class, 'barang_id'); }
    public function so() { return $this->belongsTo(So::class); }

    public function getSubtotalAttribute()
    {
        return $this->jumlah * $this->harga;
    }
}