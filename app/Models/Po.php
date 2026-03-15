<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Po extends Model
{
    protected $table = 'po';
    protected $fillable = ['no_po', 'tanggal', 'supplier_id', 'proyek_id', 'status', 'catatan', 'created_by'];
    protected $casts = ['tanggal' => 'date'];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function proyek() { return $this->belongsTo(Proyek::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function detail() { return $this->hasMany(PoDetail::class); }
    public function fb() { return $this->hasMany(Fb::class); }

    public function getTotalAttribute()
    {
        return $this->detail->sum(fn($d) => $d->jumlah * $d->harga);
    }
}