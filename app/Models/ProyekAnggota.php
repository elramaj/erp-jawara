<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyekAnggota extends Model
{
    protected $table = 'proyek_anggota';
    public $timestamps = false;

    protected $fillable = ['proyek_id', 'user_id', 'peran'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}