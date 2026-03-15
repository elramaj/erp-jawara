<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Sj extends Model
{
    protected $table = 'sj';
    protected $fillable = ['no_sj', 'tanggal', 'so_id', 'status', 'catatan', 'created_by'];
    protected $casts = ['tanggal' => 'date'];

    public function so() { return $this->belongsTo(So::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function detail() { return $this->hasMany(SjDetail::class); }
}