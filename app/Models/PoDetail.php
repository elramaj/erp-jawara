<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PoDetail extends Model
{
    protected $table = 'po_detail';
    public $timestamps = false;
    protected $fillable = ['po_id', 'barang_id', 'jumlah', 'jumlah_diterima', 'harga'];

    public function barang() { return $this->belongsTo(GudangBarang::class, 'barang_id'); }
    public function po() { return $this->belongsTo(Po::class); }

    public function getSubtotalAttribute()
    {
        return $this->jumlah * $this->harga;
    }
}