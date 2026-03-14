<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GudangStokMasuk extends Model
{
    protected $table = 'gudang_stok_masuk';
    public $timestamps = false;
    protected $fillable = [
        'barang_id', 'tanggal', 'jumlah', 'sisa',
        'harga_beli', 'supplier', 'no_dokumen', 'keterangan', 'created_by',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'created_at' => 'datetime',
    ];

    public function barang()
    {
        return $this->belongsTo(GudangBarang::class, 'barang_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}