<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Tayang extends Model
{
    use BelongsToCompany;

    protected $table = 'tayang';

    public const STATUS_LABEL = [
        'antrian'   => 'Antrian',
        'disiapkan' => 'Sedang Disiapkan',
        'tayang'    => 'Sudah Tayang',
        'turun'     => 'Diturunkan',
    ];

    protected $fillable = [
        'company_id', 'permintaan_id', 'proyek_id', 'judul', 'platform', 'link_tayang', 'status',
        'perlu_desain', 'catatan_desain', 'catatan_tayang', 'ditangani_oleh', 'tayang_at', 'turun_at',
    ];

    protected $casts = [
        'perlu_desain' => 'boolean',
        'tayang_at'    => 'datetime',
        'turun_at'     => 'datetime',
    ];

    public function permintaan() { return $this->belongsTo(PermintaanProduk::class, 'permintaan_id'); }
    public function proyek()     { return $this->belongsTo(Proyek::class); }
    public function penangan()   { return $this->belongsTo(User::class, 'ditangani_oleh'); }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABEL[$this->status] ?? ucfirst($this->status);
    }
}
