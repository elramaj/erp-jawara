<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Khusus buat kategori "reimburse", perlu tau ini reimburse punya
     * karyawan siapa. Nullable karena kategori lain (gaji, sewa, listrik,
     * dll) tidak perlu terikat ke karyawan tertentu.
     */
    public function up(): void
    {
        Schema::table('beban_operasional', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('company_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('beban_operasional', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
