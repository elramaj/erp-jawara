<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FbBayar extends Model
{
    protected $table = 'fb_bayar';
    public $timestamps = false;
    protected $fillable = ['fb_id', 'tanggal', 'jumlah', 'metode', 'keterangan', 'created_by'];
    protected $casts = ['tanggal' => 'date', 'created_at' => 'datetime'];

    public function fb() { return $this->belongsTo(Fb::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}