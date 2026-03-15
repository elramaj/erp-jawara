<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Fb extends Model
{
    protected $table = 'fb';
    protected $fillable = ['no_fb', 'tanggal', 'po_id', 'total', 'terbayar', 'status', 'jatuh_tempo', 'catatan', 'created_by'];
    protected $casts = ['tanggal' => 'date', 'jatuh_tempo' => 'date'];

    public function po() { return $this->belongsTo(Po::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function pembayaran() { return $this->hasMany(FbBayar::class); }

    public function getSisaAttribute()
    {
        return $this->total - $this->terbayar;
    }
}