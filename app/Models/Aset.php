<?php
namespace App\Models;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    use BelongsToCompany;

    protected $table = 'aset';
    protected $fillable = [
        'company_id', 'kode_aset', 'nama_aset', 'kategori_id',
        'merk', 'model', 'serial_number', 'spesifikasi',
        'tanggal_beli', 'harga_beli', 'kondisi', 'status',
        'dipegang_oleh', 'catatan', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_beli' => 'date',
            'harga_beli' => 'decimal:2',
        ];
    }

    public function kategori()
    {
        return $this->belongsTo(AsetKategori::class, 'kategori_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function pemegang()
    {
        return $this->belongsTo(User::class, 'dipegang_oleh');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function riwayat()
    {
        return $this->hasMany(AsetRiwayat::class, 'aset_id')->orderBy('tanggal_mulai', 'desc');
    }
}