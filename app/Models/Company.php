<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';
    protected $fillable = [
    'nama', 'kode', 'alamat', 'telepon', 'email',
    'latitude', 'longitude', 'radius_meter', 'is_active'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}