<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom buat nyimpen hasil hitungan lembur (dalam menit) saat karyawan
     * check-out melewati jam pulang yang diatur di Pengaturan > Jam Kerja.
     */
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            if (!Schema::hasColumn('absensi', 'lembur_menit')) {
                $table->unsignedInteger('lembur_menit')->default(0)->after('jam_keluar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn('lembur_menit');
        });
    }
};
