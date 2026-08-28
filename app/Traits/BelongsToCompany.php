<?php

namespace App\Traits;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;

/**
 * Pasang trait ini ke model mana pun yang punya kolom `company_id`
 * (GudangBarang, Po, So, Proyek, dll) supaya query-nya OTOMATIS
 * ke-filter sesuai company_id user yang lagi login.
 *
 * Kalau user yang login adalah Super Admin (is_super_admin = true),
 * filter ini otomatis di-skip — jadi Super Admin lihat data GABUNGAN
 * dari semua company tanpa perlu ubah kode di controller sama sekali.
 *
 * Cara pakai di model:
 *   class GudangBarang extends Model
 *   {
 *       use BelongsToCompany;
 *       ...
 *   }
 *
 * Kalau butuh query TANPA scope ini dalam kondisi khusus (mis. proses
 * background job lintas company), pakai:
 *   GudangBarang::withoutCompanyScope()->get();
 */
trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $user = auth()->user();

            // Belum login (mis. dipanggil dari command/job) -> jangan filter,
            // biar gak nge-block proses internal yang legit.
            if (!$user) {
                return;
            }

            // Super Admin -> bypass, lihat semua company.
            if ($user->isSuperAdmin()) {
                return;
            }

            $builder->where(
                $builder->getModel()->getTable() . '.company_id',
                $user->company_id
            );
        });
    }

    public function scopeWithoutCompanyScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('company');
    }

    /**
     * Relasi ke company pemilik data ini. Berguna khusus buat tampilan
     * Super Admin, biar tiap baris data jelas kepunyaan company mana.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}