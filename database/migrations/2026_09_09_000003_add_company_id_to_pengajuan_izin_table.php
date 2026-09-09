<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sama seperti kasus komplain: pengajuan_izin sebelumnya tidak
     * punya company_id sendiri, jadi halaman review (approval atasan)
     * menampilkan pengajuan izin dari SEMUA company tanpa filter.
     * Kolom ini dipakai oleh trait BelongsToCompany di model PengajuanIzin.
     */
    public function up(): void
    {
        Schema::table('pengajuan_izin', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
        });

        // Isi company_id untuk data lama berdasarkan company milik pengaju izin
        if (Schema::hasTable('users')) {
            \DB::statement('
                UPDATE pengajuan_izin
                INNER JOIN users ON users.id = pengajuan_izin.user_id
                SET pengajuan_izin.company_id = users.company_id
                WHERE pengajuan_izin.company_id IS NULL
            ');
        }
    }

    public function down(): void
    {
        Schema::table('pengajuan_izin', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
};
