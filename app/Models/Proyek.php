<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    use BelongsToCompany;

    protected $table = 'proyek';

    protected $fillable = [
    'company_id', 'kode_proyek', 'nama_proyek', 'klien',
    'nilai_kontrak', 'tanggal_mulai', 'tanggal_selesai',
    'deadline', 'status', 'progress', 'deskripsi', 'created_by',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'deadline'        => 'date',
        'nilai_kontrak'   => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function anggota()
    {
        return $this->hasMany(ProyekAnggota::class);
    }

    public function milestone()
    {
        return $this->hasMany(ProyekMilestone::class)->orderBy('urutan');
    }

    public function dokumen()
    {
        return $this->hasMany(ProyekDokumen::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'proyek_anggota')
            ->withPivot('peran')
            ->withTimestamps();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function po()
    {
        return $this->hasMany(Po::class);
    }

    public function pengiriman()
    {
        return $this->hasMany(Pengiriman::class);
    }

    /**
     * Kelengkapan barang proyek, dihitung dari semua PO proyek ini
     * (PO berstatus draft/batal diabaikan). Barang dianggap lengkap kalau
     * jumlah_diterima >= jumlah di PO. Pakai eager load po.detail kalau
     * dipanggil untuk banyak proyek sekaligus.
     *
     * @return array{ada_po:bool, total_item:int, item_lengkap:int, dipesan:int, diterima:int, persen:int, lengkap:bool}
     */
    public function kelengkapanBarang(): array
    {
        $totalItem = 0;
        $itemLengkap = 0;
        $dipesan = 0;
        $diterima = 0;

        foreach ($this->po as $po) {
            if (in_array($po->status, ['draft', 'batal'], true)) {
                continue;
            }
            foreach ($po->detail as $d) {
                $totalItem++;
                $dipesan  += (int) $d->jumlah;
                $diterima += min((int) $d->jumlah_diterima, (int) $d->jumlah);
                if ((int) $d->jumlah_diterima >= (int) $d->jumlah && (int) $d->jumlah > 0) {
                    $itemLengkap++;
                }
            }
        }

        $adaPo = $totalItem > 0;

        return [
            'ada_po'       => $adaPo,
            'total_item'   => $totalItem,
            'item_lengkap' => $itemLengkap,
            'dipesan'      => $dipesan,
            'diterima'     => $diterima,
            'persen'       => $dipesan > 0 ? (int) floor($diterima / $dipesan * 100) : 0,
            'lengkap'      => $adaPo && $itemLengkap === $totalItem,
        ];
    }

    /** Anggota proyek yang berperan Sales (role_id 5). */
    public function salesAnggota()
    {
        return $this->anggota->filter(fn($a) => $a->user && (int) $a->user->role_id === 5)
            ->map(fn($a) => $a->user)->values();
    }
}