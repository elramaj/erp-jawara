<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PermintaanProduk extends Model
{
    use BelongsToCompany;

    protected $table = 'permintaan_produk';

    public const STATUS_LABEL = [
        'baru'         => 'Baru (menunggu Produk)',
        'dicari'       => 'Sedang Dicarikan Produk',
        'opsi_siap'    => 'Opsi Siap (menunggu Sales)',
        'dikonfirmasi' => 'Dikonfirmasi',
        'dibatalkan'   => 'Dibatalkan',
    ];

    protected $fillable = [
        'company_id', 'no_permintaan', 'proyek_id', 'customer_id', 'judul', 'sumber',
        'target_tanggal', 'catatan', 'status', 'alasan_revisi', 'alasan_batal',
        'produk_pic', 'diambil_at', 'opsi_dikirim_at', 'dikonfirmasi_oleh', 'dikonfirmasi_at', 'created_by',
    ];

    protected $casts = [
        'target_tanggal'  => 'date',
        'diambil_at'      => 'datetime',
        'opsi_dikirim_at' => 'datetime',
        'dikonfirmasi_at' => 'datetime',
    ];

    public function item()       { return $this->hasMany(PermintaanProdukItem::class, 'permintaan_id'); }
    public function proyek()     { return $this->belongsTo(Proyek::class); }
    public function customer()   { return $this->belongsTo(Customer::class); }
    public function creator()    { return $this->belongsTo(User::class, 'created_by'); }
    public function picProduk()  { return $this->belongsTo(User::class, 'produk_pic'); }
    public function konfirmator(){ return $this->belongsTo(User::class, 'dikonfirmasi_oleh'); }
    public function tayang()     { return $this->hasMany(Tayang::class, 'permintaan_id'); }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABEL[$this->status] ?? ucfirst($this->status);
    }
}
