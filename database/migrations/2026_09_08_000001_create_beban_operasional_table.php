<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel buat catat beban operasional di luar HPP/pembelian barang lewat PO
     * (mis. gaji, sewa kantor, listrik, dll). Data ini yang nantinya jadi
     * komponen "Beban Operasional" di Laporan Laba Rugi berjenjang.
     */
    public function up(): void
    {
        Schema::create('beban_operasional', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('kategori'); // gaji, sewa, listrik, transport, reimburse, lainnya
            $table->date('tanggal');
            $table->decimal('nominal', 15, 2);
            $table->string('keterangan')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beban_operasional');
    }
};
