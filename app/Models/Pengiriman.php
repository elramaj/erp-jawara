<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    use BelongsToCompany;

    protected $table = 'pengiriman';

    public const STATUS_LABEL = [
        'diusulkan'  => 'Menunggu Konfirmasi',
        'revisi'     => 'Perlu Revisi',
        'terjadwal'  => 'Terjadwal',
        'dikirim'    => 'Dalam Pengiriman',
        'diterima'   => 'Diterima Customer',
        'dibatalkan' => 'Dibatalkan',
    ];

    // Status yang dianggap "masih berjalan" (proyek tidak perlu dijadwalkan lagi)
    public const STATUS_AKTIF = ['diusulkan', 'revisi', 'terjadwal', 'dikirim', 'diterima'];

    protected $fillable = [
        'company_id', 'proyek_id', 'no_pengiriman',
        'tanggal_kirim', 'jam', 'alamat_tujuan',
        'penerima_nama', 'penerima_kontak', 'kendaraan', 'driver', 'catatan',
        'status', 'alasan_revisi', 'alasan_batal',
        'dikonfirmasi_oleh', 'dikonfirmasi_at', 'dikirim_at', 'diterima_at',
        'created_by',
    ];

    protected $casts = [
        'tanggal_kirim'   => 'date',
        'dikonfirmasi_at' => 'datetime',
        'dikirim_at'      => 'datetime',
        'diterima_at'     => 'datetime',
    ];

    public function proyek()       { return $this->belongsTo(Proyek::class); }
    public function creator()      { return $this->belongsTo(User::class, 'created_by'); }
    public function konfirmator()  { return $this->belongsTo(User::class, 'dikonfirmasi_oleh'); }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABEL[$this->status] ?? ucfirst($this->status);
    }

    public function isFinal(): bool
    {
        return in_array($this->status, ['diterima', 'dibatalkan'], true);
    }
}
