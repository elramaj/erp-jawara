<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_riwayat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_id')->constrained('aset')->cascadeOnDelete();
            // null = aset ditaruh di gudang / gak dipegang siapa-siapa di periode ini
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['dipakai', 'gudang', 'diperbaiki', 'hilang'])->default('dipakai');
            $table->date('tanggal_mulai');
            // null = periode ini masih berlangsung (belum ada perpindahan berikutnya)
            $table->date('tanggal_selesai')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_riwayat');
    }
};
