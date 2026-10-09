<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomplainBarangService extends Model
{
    protected $table = 'komplain_barang_service';

    protected $fillable = ['komplain_id', 'nama_barang', 'serial_number', 'qty'];

    public function komplain()
    {
        return $this->belongsTo(Komplain::class);
    }
}
