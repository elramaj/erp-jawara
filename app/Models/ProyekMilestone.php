<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyekMilestone extends Model
{
    protected $table = 'proyek_milestone';

    protected $fillable = [
        'proyek_id', 'judul', 'deskripsi',
        'tanggal_target', 'tanggal_selesai', 'status', 'urutan',
    ];

    protected $casts = [
        'tanggal_target'  => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }
}