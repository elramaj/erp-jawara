<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanIzin extends Model
{
    protected $table = 'pengajuan_izin';

    protected $fillable = [
        'user_id',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'lampiran',
        'status',
        'reviewed_by',
        'reviewed_at',
        'catatan_review',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'reviewed_at'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}