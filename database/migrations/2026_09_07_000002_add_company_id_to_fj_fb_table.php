<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Faktur Jual (fj) & Faktur Beli (fb) sebelumnya tidak punya
     * company_id sendiri, cuma nyambung ke so_id/po_id. Kolom ini
     * dipakai oleh trait BelongsToCompany supaya Laporan Keuangan
     * bisa di-scope per company (dan digabung khusus untuk Super Admin).
     */
    public function up(): void
    {
        Schema::table('fj', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
        });
        Schema::table('fb', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
        });

        // Isi company_id untuk data lama berdasarkan company milik SO/PO terkait
        DB::statement('
            UPDATE fj
            INNER JOIN so ON so.id = fj.so_id
            SET fj.company_id = so.company_id
            WHERE fj.company_id IS NULL
        ');

        DB::statement('
            UPDATE fb
            INNER JOIN po ON po.id = fb.po_id
            SET fb.company_id = po.company_id
            WHERE fb.company_id IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('fj', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
        Schema::table('fb', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
};
