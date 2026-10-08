<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadwal pengiriman barang proyek ke customer.
     *
     * Alur status:
     *   diusulkan -> (Sales konfirmasi) -> terjadwal -> dikirim -> diterima
     *   diusulkan -> (Sales minta ubah)  -> revisi -> (tim Pengiriman ubah) -> diusulkan
     *   status mana pun yang belum final bisa -> dibatalkan
     */
    public function up(): void
    {
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('proyek_id');
            $table->string('no_pengiriman', 40);

            $table->date('tanggal_kirim');
            $table->string('jam', 5)->nullable();          // "09:30"
            $table->text('alamat_tujuan');
            $table->string('penerima_nama')->nullable();
            $table->string('penerima_kontak')->nullable();
            $table->string('kendaraan')->nullable();
            $table->string('driver')->nullable();
            $table->text('catatan')->nullable();

            $table->string('status', 20)->default('diusulkan');
            $table->text('alasan_revisi')->nullable();     // diisi Sales saat minta ubah jadwal
            $table->text('alasan_batal')->nullable();

            $table->unsignedBigInteger('dikonfirmasi_oleh')->nullable();
            $table->timestamp('dikonfirmasi_at')->nullable();
            $table->timestamp('dikirim_at')->nullable();
            $table->timestamp('diterima_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'no_pengiriman']);
            $table->index(['proyek_id', 'status']);
            $table->index(['company_id', 'status']);

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            // proyek_id sengaja tanpa foreign key: migration asli tabel `proyek` tidak ada di
            // repo, jadi tipe kolom id-nya tidak bisa dipastikan sama (risiko error 150 di MariaDB).
            $table->foreign('dikonfirmasi_oleh')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengiriman');
    }
};
