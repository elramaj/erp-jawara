<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Data Tanda Terima Barang Service disimpan di komplain (supaya tidak ditulis tangan):
     * barang yang diserahkan (bisa lebih dari satu) + kelengkapan, kondisi fisik, penyerah.
     * komplain_id sengaja tanpa foreign key: migration asli tabel komplain tidak ada di repo.
     */
    public function up(): void
    {
        Schema::table('komplain', function (Blueprint $table) {
            $table->date('tanggal_terima_service')->nullable();
            $table->text('kelengkapan_service')->nullable();
            $table->text('kondisi_fisik')->nullable();
            $table->string('penyerah_nama')->nullable();
            $table->string('penyerah_kontak', 100)->nullable();
        });

        Schema::create('komplain_barang_service', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('komplain_id')->index();
            $table->string('nama_barang');
            $table->string('serial_number')->nullable();
            $table->unsignedInteger('qty')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komplain_barang_service');
        Schema::table('komplain', function (Blueprint $table) {
            $table->dropColumn(['tanggal_terima_service', 'kelengkapan_service', 'kondisi_fisik', 'penyerah_nama', 'penyerah_kontak']);
        });
    }
};
