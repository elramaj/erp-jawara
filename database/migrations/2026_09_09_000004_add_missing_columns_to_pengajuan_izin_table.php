<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration asli `create_pengajuan_izin_table` cuma bikin kolom id +
     * timestamps — kolom lain (jenis, tanggal_mulai, dst) sebelumnya
     * ditambahkan manual ke database, jadi kalau ada yang setup project
     * ini dari database kosong (migrate dari nol), tabelnya bakal error
     * karena kolom-kolom ini gak pernah benar-benar dibikin lewat migration.
     *
     * Migration ini nutup celah itu. Pakai Schema::hasColumn supaya aman
     * dijalankan di database manapun — baik yang kolomnya sudah ada
     * (di-skip otomatis) maupun database baru yang benar-benar kosong.
     */
    public function up(): void
    {
        Schema::table('pengajuan_izin', function (Blueprint $table) {
            if (!Schema::hasColumn('pengajuan_izin', 'user_id')) {
                $table->foreignId('user_id')->after('company_id')->constrained('users')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('pengajuan_izin', 'jenis')) {
                $table->string('jenis')->after('user_id'); // izin | sakit | cuti | dinas_luar
            }
            if (!Schema::hasColumn('pengajuan_izin', 'tanggal_mulai')) {
                $table->date('tanggal_mulai')->after('jenis');
            }
            if (!Schema::hasColumn('pengajuan_izin', 'tanggal_selesai')) {
                $table->date('tanggal_selesai')->after('tanggal_mulai');
            }
            if (!Schema::hasColumn('pengajuan_izin', 'alasan')) {
                $table->text('alasan')->after('tanggal_selesai');
            }
            if (!Schema::hasColumn('pengajuan_izin', 'lampiran')) {
                $table->string('lampiran')->nullable()->after('alasan');
            }
            if (!Schema::hasColumn('pengajuan_izin', 'status')) {
                $table->string('status')->default('pending')->after('lampiran'); // pending | disetujui | ditolak
            }
            if (!Schema::hasColumn('pengajuan_izin', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('pengajuan_izin', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
            if (!Schema::hasColumn('pengajuan_izin', 'catatan_review')) {
                $table->string('catatan_review')->nullable()->after('reviewed_at');
            }
        });
    }

    public function down(): void
    {
        // Sengaja tidak di-drop di sini — kolom-kolom ini adalah bagian
        // inti dari tabel, bukan tambahan opsional. Rollback tabel penuh
        // cukup lewat migration create_pengajuan_izin_table yang asli.
    }
};
