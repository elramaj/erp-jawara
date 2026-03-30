<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class So extends Model
{
    protected $table = 'so';
    protected $fillable = [
    'company_id', 'no_so', 'tanggal', 'customer_id',
    'proyek_id', 'status', 'catatan', 'created_by'
    ];
    protected $casts = ['tanggal' => 'date'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function proyek() { return $this->belongsTo(Proyek::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function detail() { return $this->hasMany(SoDetail::class); }
    public function sj() { return $this->hasMany(Sj::class); }
    public function fj() { return $this->hasMany(Fj::class); }

    public function getTotalAttribute()
    {
        return $this->detail->sum(fn($d) => $d->jumlah * $d->harga);
    }
}