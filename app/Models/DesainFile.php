<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesainFile extends Model
{
    protected $table = 'desain_file';

    protected $fillable = ['permintaan_id', 'jenis', 'versi', 'path', 'nama_asli', 'mime', 'ukuran', 'uploaded_by'];

    public function permintaan() { return $this->belongsTo(PermintaanDesain::class, 'permintaan_id'); }
    public function uploader()   { return $this->belongsTo(User::class, 'uploaded_by'); }
}
