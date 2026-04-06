<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $fillable = [
        'kode', 'nama', 'alamat', 'telepon', 'email', 'pic', 'is_active',
        'sales_pic', 'termin_pembayaran', 'batas_jtempo', 'batas_piutang',
        'rayon', 'coa_piutang', 'tipe_harga_jual',
        'no_npwp', 'diskon_persen', 'keterangan', 'termasuk_supplier',
        'lokasi', 'alamat1', 'alamat2', 'alamat3', 'kota', 'propinsi',
        'kontak', 'tgl_lahir',
        'phone1', 'phone2', 'phone3', 'phone4', 'phone5',
        'fax1', 'fax2', 'bank_account',
        'default_kirim', 'default_tagihan', 'default_pajak',
    ];

    protected $casts = [
        'termasuk_supplier' => 'boolean',
        'default_kirim'     => 'boolean',
        'default_tagihan'   => 'boolean',
        'default_pajak'     => 'boolean',
        'is_active'         => 'boolean',
        'tgl_lahir'         => 'date',
    ];

    public function so()
    {
        return $this->hasMany(So::class);
    }
}