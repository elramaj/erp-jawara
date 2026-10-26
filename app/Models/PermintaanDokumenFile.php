<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanDokumenFile extends Model
{
    protected $table = 'permintaan_dokumen_file';

    protected $fillable = ['permintaan_id', 'path', 'nama_asli', 'mime', 'ukuran', 'uploaded_by'];

    public function permintaan() { return $this->belongsTo(PermintaanDokumen::class, 'permintaan_id'); }
}
