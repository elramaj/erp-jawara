<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    protected $table = 'proyek';

    protected $fillable = [
    'company_id', 'kode_proyek', 'nama_proyek', 'klien',
    'nilai_kontrak', 'tanggal_mulai', 'tanggal_selesai',
    'deadline', 'status', 'progress', 'deskripsi', 'created_by',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'deadline'        => 'date',
        'nilai_kontrak'   => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function anggota()
    {
        return $this->hasMany(ProyekAnggota::class);
    }

    public function milestone()
    {
        return $this->hasMany(ProyekMilestone::class)->orderBy('urutan');
    }

    public function dokumen()
    {
        return $this->hasMany(ProyekDokumen::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'proyek_anggota')
            ->withPivot('peran')
            ->withTimestamps();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}