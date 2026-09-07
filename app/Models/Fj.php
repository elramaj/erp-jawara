<?php
namespace App\Models;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Fj extends Model
{
    use BelongsToCompany;

    protected $table = 'fj';
    protected $fillable = ['company_id', 'no_fj', 'tanggal', 'so_id', 'total', 'terbayar', 'status', 'jatuh_tempo', 'catatan', 'created_by'];
    protected $casts = ['tanggal' => 'date', 'jatuh_tempo' => 'date'];

    public function so() { return $this->belongsTo(So::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function pembayaran() { return $this->hasMany(FjBayar::class); }

    public function getSisaAttribute()
    {
        return $this->total - $this->terbayar;
    }
}