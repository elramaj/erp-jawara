<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasRoles, HasApiTokens;

    protected $fillable = [
    'company_id', 'role_id', 'department_id', 'name', 'email',
    'password', 'phone', 'photo', 'employee_id', 'join_date', 'is_active',
    'is_super_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_super_admin' => 'boolean',
            'join_date' => 'date',
        ];
    }

    /**
     * True kalau user ini Super Admin — bisa lihat data GABUNGAN dari
     * semua company (bypass filter company_id) di dashboard web.
     * Dipakai oleh trait BelongsToCompany di semua model yang di-scope
     * per company (GudangBarang, Po, So, Proyek, dll).
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getCompanyIdAttribute($value)
    {
        return $value;
    }

    /**
     * Scope manual (BUKAN global scope) buat filter User by company_id.
     * Sengaja gak dibikin global scope seperti trait BelongsToCompany,
     * karena kalau tabel `users` di-global-scope otomatis, proses
     * `auth()->user()` (yang juga query ke tabel users) bisa muter
     * jadi infinite loop pas ada yang login. Jadi ini harus dipanggil
     * manual: User::forCurrentCompany()->get();
     *
     * Otomatis bypass buat Super Admin (lihat semua company).
     */
    public function scopeForCurrentCompany($query)
    {
        $current = auth()->user();
        if ($current && !$current->isSuperAdmin()) {
            $query->where('company_id', $current->company_id);
        }
        return $query;
    }
}