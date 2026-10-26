<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class DokumenArsip extends Model
{
    use BelongsToCompany;

    protected $table = 'dokumen_arsip';

    /** Peringatan "segera habis" muncul sekian hari sebelum kedaluwarsa. */
    public const HARI_PERINGATAN = 30;

    /** Saran jenis dokumen legalitas (boleh isi bebas). */
    public const JENIS_SARAN = [
        'NIB', 'SIUP', 'TDP', 'Akta Pendirian', 'Akta Perubahan', 'SK Kemenkumham',
        'NPWP Perusahaan', 'SKT Pajak', 'SPPKP', 'Surat Keterangan Domisili', 'Sertifikat ISO',
        'Sertifikat Keagenan / Distributor', 'Surat Dukungan Principal', 'Laporan Keuangan', 'KTP Direksi',
    ];

    protected $fillable = [
        'company_id', 'jenis', 'nama', 'nomor', 'penerbit', 'tanggal_terbit', 'tanggal_kedaluwarsa',
        'file_path', 'nama_asli', 'mime', 'catatan', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'tanggal_terbit'      => 'date',
        'tanggal_kedaluwarsa' => 'date',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }

    /** tanpa_batas | aktif | segera | kedaluwarsa */
    public function getStatusMasaAttribute(): string
    {
        if (!$this->tanggal_kedaluwarsa) {
            return 'tanpa_batas';
        }
        $hariIni = now()->startOfDay();
        if ($this->tanggal_kedaluwarsa->lt($hariIni)) {
            return 'kedaluwarsa';
        }
        if ($this->tanggal_kedaluwarsa->lte($hariIni->copy()->addDays(self::HARI_PERINGATAN))) {
            return 'segera';
        }
        return 'aktif';
    }

    public function getSisaHariAttribute(): ?int
    {
        return $this->tanggal_kedaluwarsa
            ? (int) now()->startOfDay()->diffInDays($this->tanggal_kedaluwarsa, false)
            : null;
    }
}
