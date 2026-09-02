<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AsetRiwayat extends Model
{
    protected $table = 'aset_riwayat';
    protected $fillable = [
        'aset_id', 'user_id', 'status', 'tanggal_mulai',
        'tanggal_selesai', 'catatan', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'aset_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}