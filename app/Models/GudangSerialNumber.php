<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GudangSerialNumber extends Model
{
    protected $table = 'gudang_serial_number';
    public $timestamps = false;
    protected $fillable = [
        'barang_id', 'masuk_id', 'serial_number',
        'kondisi', 'status', 'keluar_id', 'keterangan',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function barang()
    {
        return $this->belongsTo(GudangBarang::class, 'barang_id');
    }

    public function masuk()
    {
        return $this->belongsTo(GudangStokMasuk::class, 'masuk_id');
    }

    public function keluar()
    {
        return $this->belongsTo(GudangStokKeluar::class, 'keluar_id');
    }
}