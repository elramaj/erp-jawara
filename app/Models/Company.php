<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';
    protected $fillable = [
    'nama', 'kode', 'alamat', 'telepon', 'email',
    'latitude', 'longitude', 'radius_meter', 'is_active',
    'logo', 'template_cetak',
    ];

    /** URL logo PT untuk kop surat; kalau belum diunggah pakai logo bawaan aplikasi. */
    public function getLogoUrlAttribute(): string
    {
        return $this->logo ? asset('storage/' . $this->logo) : asset('images/logo.png');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}