<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $fillable = ['kode', 'nama', 'alamat', 'telepon', 'email', 'pic', 'is_active'];

    public function po()
    {
        return $this->hasMany(Po::class);
    }
}