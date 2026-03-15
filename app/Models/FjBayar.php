<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FjBayar extends Model
{
    protected $table = 'fj_bayar';
    public $timestamps = false;
    protected $fillable = ['fj_id', 'tanggal', 'jumlah', 'metode', 'keterangan', 'created_by'];
    protected $casts = ['tanggal' => 'date', 'created_at' => 'datetime'];

    public function fj() { return $this->belongsTo(Fj::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}