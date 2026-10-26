<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PermintaanDesain extends Model
{
    use BelongsToCompany;

    protected $table = 'permintaan_desain';

    public const STATUS_LABEL = [
        'baru'       => 'Baru (menunggu Desain)',
        'dikerjakan' => 'Sedang Dikerjakan',
        'review'     => 'Menunggu Review Peminta',
        'revisi'     => 'Perlu Revisi',
        'selesai'    => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    /** Status yang masih berjalan. */
    public const STATUS_AKTIF = ['baru', 'dikerjakan', 'review', 'revisi'];

    protected $fillable = [
        'company_id', 'no_desain', 'judul', 'brief', 'ukuran_format', 'deadline',
        'proyek_id', 'tayang_id', 'status', 'catatan_revisi', 'alasan_batal', 'jumlah_revisi',
        'desainer_id', 'diambil_at', 'dikirim_at', 'selesai_at', 'created_by',
    ];

    protected $casts = [
        'deadline'   => 'date',
        'diambil_at' => 'datetime',
        'dikirim_at' => 'datetime',
        'selesai_at' => 'datetime',
    ];

    public function proyek()   { return $this->belongsTo(Proyek::class); }
    public function tayang()   { return $this->belongsTo(Tayang::class); }
    public function creator()  { return $this->belongsTo(User::class, 'created_by'); }
    public function desainer() { return $this->belongsTo(User::class, 'desainer_id'); }
    public function file()     { return $this->hasMany(DesainFile::class, 'permintaan_id'); }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABEL[$this->status] ?? ucfirst($this->status);
    }

    public function isAktif(): bool
    {
        return in_array($this->status, self::STATUS_AKTIF, true);
    }
}
