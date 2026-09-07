<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel komplain sebelumnya tidak punya company_id sendiri (cuma
     * nyambung ke proyek_id yang boleh kosong), jadi komplain yang tidak
     * terkait proyek tidak bisa di-scope per company. Kolom ini dipakai
     * oleh trait BelongsToCompany di model Komplain.
     */
    public function up(): void
    {
        Schema::table('komplain', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
        });

        // Isi company_id untuk data lama berdasarkan company milik pembuat komplain
        if (Schema::hasTable('users')) {
            \DB::statement('
                UPDATE komplain
                INNER JOIN users ON users.id = komplain.created_by
                SET komplain.company_id = users.company_id
                WHERE komplain.company_id IS NULL
            ');
        }
    }

    public function down(): void
    {
        Schema::table('komplain', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
};
