<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GudangStokKeluar extends Model
{
    protected $table = 'gudang_stok_keluar';
    public $timestamps = false;
    protected $fillable = [
        'barang_id', 'tanggal', 'jumlah', 'harga_jual',
        'proyek_id', 'tujuan', 'no_dokumen', 'keterangan', 'created_by',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'created_at' => 'datetime',
    ];

    public function barang()
    {
        return $this->belongsTo(GudangBarang::class, 'barang_id');
    }

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function fifoDetail()
    {
        return $this->hasMany(GudangFifoDetail::class, 'keluar_id');
    }
}