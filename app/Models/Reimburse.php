<?php
namespace App\Models;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Reimburse extends Model
{
    use BelongsToCompany;

    protected $table = 'reimburse';
    protected $fillable = [
        'company_id', 'user_id', 'kategori', 'tanggal_pengeluaran',
        'nominal', 'keterangan', 'bukti',
        'status', 'approved_by', 'approved_at', 'catatan_approval',
        'beban_operasional_id',
    ];

    protected $casts = [
        'tanggal_pengeluaran' => 'date',
        'approved_at'         => 'datetime',
        'nominal'             => 'decimal:2',
    ];

    public const KATEGORI = [
        'transport'  => 'Transport',
        'makan'      => 'Makan',
        'akomodasi'  => 'Akomodasi',
        'medis'      => 'Medis',
        'lainnya'    => 'Lainnya',
    ];

    // Karyawan yang mengajukan.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Yang approve/tolak (orang Keuangan).
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function bebanOperasional()
    {
        return $this->belongsTo(BebanOperasional::class);
    }
}
