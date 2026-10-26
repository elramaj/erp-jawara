<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PermintaanDokumen extends Model
{
    use BelongsToCompany;

    protected $table = 'permintaan_dokumen';

    public const STATUS_LABEL = [
        'baru'       => 'Baru (menunggu disiapkan)',
        'diproses'   => 'Sedang Disiapkan',
        'selesai'    => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    /** Saran jenis dokumen (boleh isi bebas). */
    public const JENIS_SARAN = [
        'NIB', 'SIUP', 'TDP', 'Akta Pendirian', 'NPWP Perusahaan', 'SKT Pajak', 'Surat Domisili',
        'BAST', 'Kontrak / SPK', 'Surat Penawaran', 'Surat Dukungan Principal', 'Faktur Pajak', 'Dokumen Tender Lainnya',
    ];

    protected $fillable = [
        'company_id', 'no_permintaan', 'jenis_dokumen', 'keperluan', 'proyek_id', 'deadline', 'status',
        'penyiap_id', 'arsip_id', 'catatan_penyiap', 'alasan_batal',
        'diambil_at', 'selesai_at', 'dilihat_peminta_at', 'created_by',
    ];

    protected $casts = [
        'deadline'           => 'date',
        'diambil_at'         => 'datetime',
        'selesai_at'         => 'datetime',
        'dilihat_peminta_at' => 'datetime',
    ];

    public function proyek()  { return $this->belongsTo(Proyek::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function penyiap() { return $this->belongsTo(User::class, 'penyiap_id'); }
    public function arsip()   { return $this->belongsTo(DokumenArsip::class, 'arsip_id'); }
    public function file()    { return $this->hasMany(PermintaanDokumenFile::class, 'permintaan_id'); }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABEL[$this->status] ?? ucfirst($this->status);
    }
}
