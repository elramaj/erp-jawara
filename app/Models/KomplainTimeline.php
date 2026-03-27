<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KomplainTimeline extends Model
{
    protected $table = 'komplain_timeline';
    public $timestamps = false;

    protected $fillable = [
        'komplain_id', 'keterangan', 'status_baru', 'created_by',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function komplain()
    {
        return $this->belongsTo(Komplain::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}