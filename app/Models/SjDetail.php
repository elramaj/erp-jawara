<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SjDetail extends Model
{
    protected $table = 'sj_detail';
    public $timestamps = false;
    protected $fillable = ['sj_id', 'barang_id', 'jumlah'];

    public function barang() { return $this->belongsTo(GudangBarang::class, 'barang_id'); }
}