<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('kode_aset')->unique();
            $table->string('nama_aset');
            $table->foreignId('kategori_id')->nullable()->constrained('aset_kategori')->nullOnDelete();
            $table->string('merk')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('spesifikasi')->nullable();
            $table->date('tanggal_beli')->nullable();
            $table->decimal('harga_beli', 15, 2)->nullable();
            // kondisi fisik barang saat ini
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])->default('baik');
            // status kepemilikan/pemakaian saat ini
            $table->enum('status', ['tersedia', 'dipakai', 'diperbaiki', 'hilang', 'dihapus'])->default('tersedia');
            // siapa yang lagi pegang aset ini sekarang (null = di gudang/gak dipegang siapa-siapa)
            $table->foreignId('dipegang_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset');
    }
};
