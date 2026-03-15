<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $fillable = ['kode', 'nama', 'alamat', 'telepon', 'email', 'pic', 'is_active'];

    public function so()
    {
        return $this->hasMany(So::class);
    }
}