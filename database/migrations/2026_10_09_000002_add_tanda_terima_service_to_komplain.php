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
        $kolom = [
            'tanggal_terima_service' => fn(Blueprint $t) => $t->date('tanggal_terima_service')->nullable(),
            'kelengkapan_service'    => fn(Blueprint $t) => $t->text('kelengkapan_service')->nullable(),
            'kondisi_fisik'          => fn(Blueprint $t) => $t->text('kondisi_fisik')->nullable(),
            'penyerah_nama'          => fn(Blueprint $t) => $t->string('penyerah_nama')->nullable(),
            'penyerah_kontak'        => fn(Blueprint $t) => $t->string('penyerah_kontak', 100)->nullable(),
        ];
        Schema::table('komplain', function (Blueprint $table) use ($kolom) {
            foreach ($kolom as $nama => $buat) {
                if (! Schema::hasColumn('komplain', $nama)) {
                    $buat($table);
                }
            }
        });

        if (! Schema::hasTable('komplain_barang_service')) Schema::create('komplain_barang_service', function (Blueprint $table) {
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
