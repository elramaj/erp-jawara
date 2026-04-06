<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $fillable = [
        'kode', 'nama', 'alamat', 'telepon', 'email', 'pic', 'is_active',
        'termin_pembayaran', 'batas_hutang', 'coa_hutang',
        'no_npwp', 'diskon_persen', 'keterangan', 'termasuk_customer',
        'lokasi', 'alamat1', 'alamat2', 'alamat3', 'kota', 'propinsi',
        'kontak', 'phone1', 'phone2', 'phone3', 'phone4', 'phone5',
        'fax1', 'fax2', 'bank_account',
        'default_kirim', 'default_penagihan', 'default_pajak',
    ];

    protected $casts = [
        'termasuk_customer' => 'boolean',
        'default_kirim'     => 'boolean',
        'default_penagihan' => 'boolean',
        'default_pajak'     => 'boolean',
        'is_active'         => 'boolean',
    ];

    public function po()
    {
        return $this->hasMany(Po::class);
    }
}