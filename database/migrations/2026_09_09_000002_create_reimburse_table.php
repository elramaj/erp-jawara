<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Klaim reimburse/biaya karyawan. Alurnya: karyawan ajukan -> Keuangan
     * (role_id 1/2/11, sama kayak akses Laporan Keuangan & Pengeluaran)
     * setujui atau tolak. Begitu disetujui, otomatis kecatat juga sebagai
     * baris di tabel beban_operasional (kategori "reimburse") biar
     * kehitung di Laporan Laba Rugi.
     */
    public function up(): void
    {
        Schema::create('reimburse', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id'); // karyawan yang mengajukan
            $table->string('kategori'); // transport, makan, akomodasi, medis, lainnya
            $table->date('tanggal_pengeluaran');
            $table->decimal('nominal', 15, 2);
            $table->string('keterangan')->nullable();
            $table->string('bukti')->nullable(); // path foto/PDF struk

            $table->string('status')->default('pending'); // pending, disetujui, ditolak
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('catatan_approval')->nullable();

            // Kalau disetujui, ini nyimpen referensi ke baris beban_operasional
            // yang otomatis dibikin, biar gampang di-trace / dihapus barengan
            // kalau reimburse-nya dibatalkan.
            $table->unsignedBigInteger('beban_operasional_id')->nullable();

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('beban_operasional_id')->references('id')->on('beban_operasional')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reimburse');
    }
};
