<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
    'user_id', 'tanggal', 'jam_masuk', 'jam_keluar',
    'status', 'tipe', 'catatan', 'nama_tujuan', 'urutan_visit',
    'foto_masuk', 'foto_keluar',
    'lat_masuk', 'lng_masuk', 'lat_keluar', 'lng_keluar',
    'lokasi_valid', 'lokasi_masuk', 'lokasi_keluar',
    'keterangan', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}