<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyekDokumen extends Model
{
    protected $table = 'proyek_dokumen';
    public $timestamps = false;

    protected $fillable = [
        'proyek_id', 'nama_dokumen', 'file_path', 'jenis', 'uploaded_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}