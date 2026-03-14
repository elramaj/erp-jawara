<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GudangFifoDetail extends Model
{
    protected $table = 'gudang_fifo_detail';
    public $timestamps = false;
    protected $fillable = ['keluar_id', 'masuk_id', 'jumlah'];

    public function masuk()
    {
        return $this->belongsTo(GudangStokMasuk::class, 'masuk_id');
    }
}