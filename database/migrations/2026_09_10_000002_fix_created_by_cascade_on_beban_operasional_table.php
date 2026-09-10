<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BUG: kolom created_by di beban_operasional sebelumnya onDelete('cascade').
     * Artinya kalau karyawan yang PERNAH mencatat beban operasional dihapus dari
     * sistem, SEMUA baris beban operasional yang dia catat ikut TERHAPUS otomatis
     * -- termasuk yang sudah kehitung di Laporan Laba Rugi bulan-bulan sebelumnya.
     * Ini bisa diam-diam mengubah angka laporan keuangan yang sudah "closed".
     *
     * Diganti jadi nullable + onDelete('set null'): data beban tetap ada,
     * cuma kolom "dicatat oleh"-nya jadi kosong kalau pencatatnya sudah
     * tidak ada di sistem.
     */
    public function up(): void
    {
        Schema::table('beban_operasional', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        // Pakai raw SQL (bukan ->nullable()->change()) biar gak butuh
        // package doctrine/dbal tambahan -- sama kayak migration
        // make_company_id_nullable_on_users_table sebelumnya.
        \DB::statement('ALTER TABLE beban_operasional MODIFY created_by BIGINT UNSIGNED NULL');

        Schema::table('beban_operasional', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('beban_operasional', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
