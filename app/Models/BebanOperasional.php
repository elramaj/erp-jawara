<?php
namespace App\Models;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class BebanOperasional extends Model
{
    use BelongsToCompany;

    protected $table = 'beban_operasional';
    protected $fillable = ['company_id', 'kategori', 'tanggal', 'nominal', 'keterangan', 'created_by'];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    // Daftar kategori baku, dipakai buat dropdown & badge warna di view.
    public const KATEGORI = [
        'gaji'      => 'Gaji Karyawan',
        'sewa'      => 'Sewa Tempat',
        'listrik'   => 'Listrik & Air',
        'transport' => 'Transport & BBM',
        'reimburse' => 'Reimburse Karyawan',
        'lainnya'   => 'Lainnya',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}