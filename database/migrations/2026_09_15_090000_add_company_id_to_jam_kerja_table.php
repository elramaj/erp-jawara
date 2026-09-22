<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jam kerja sebelumnya global (1 jadwal buat semua company). Sekarang
     * per-company: tiap company punya 7 baris (senin-minggu) sendiri.
     * Jadwal lama yang udah ada di-duplikat ke tiap company yang ada,
     * biar nggak ada company yang tiba-tiba kosong jadwalnya.
     */
    public function up(): void
    {
        Schema::table('jam_kerja', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        $jadwalLama = DB::table('jam_kerja')->whereNull('company_id')->get();
        $companies  = DB::table('companies')->pluck('id');

        foreach ($companies as $companyId) {
            foreach ($jadwalLama as $j) {
                DB::table('jam_kerja')->insert([
                    'company_id'      => $companyId,
                    'hari'            => $j->hari,
                    'jam_masuk'       => $j->jam_masuk,
                    'jam_keluar'      => $j->jam_keluar,
                    'toleransi_menit' => $j->toleransi_menit,
                    'is_libur'        => $j->is_libur,
                ]);
            }
        }

        // Baris lama yang company_id-nya masih null (template awal) dibuang,
        // biar gak nyampur sama yang udah per-company.
        DB::table('jam_kerja')->whereNull('company_id')->delete();
    }

    public function down(): void
    {
        Schema::table('jam_kerja', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
