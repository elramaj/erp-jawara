<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Supaya akun Super Admin (is_super_admin = true) bisa benar-benar
     * "berdiri sendiri" tanpa terikat ke 1 company tertentu — company_id
     * dia diisi NULL. Trait BelongsToCompany dan User::forCurrentCompany()
     * sudah otomatis bypass filter kalau is_super_admin = true, TAPI kalau
     * kolom company_id di database masih NOT NULL, akun itu terpaksa tetap
     * "nempel" ke 1 company (mis. Jawara Meraki) dan ikut ke-tarik di data
     * spesifik company itu (absensi, dsb). Migration ini pastikan kolomnya
     * nullable, pakai raw SQL (bukan ->nullable()->change()) supaya gak
     * butuh package doctrine/dbal.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'company_id')) {
            DB::statement('ALTER TABLE users MODIFY company_id BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        // Sengaja tidak dikembalikan ke NOT NULL di down() — beresiko gagal
        // kalau sudah ada user (mis. super admin) dengan company_id NULL.
    }
};
