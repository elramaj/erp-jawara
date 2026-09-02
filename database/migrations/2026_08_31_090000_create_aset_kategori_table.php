<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('deskripsi')->nullable();
        });

        // Seed kategori umum biar langsung kepake
        DB::table('aset_kategori')->insert([
            ['nama' => 'Elektronik / IT', 'deskripsi' => 'Laptop, PC, monitor, HP, printer, dll'],
            ['nama' => 'Kendaraan', 'deskripsi' => 'Motor/mobil dinas'],
            ['nama' => 'Furniture', 'deskripsi' => 'Meja, kursi, lemari, dll'],
            ['nama' => 'Peralatan Kantor', 'deskripsi' => 'AC, proyektor, alat tulis kantor, dll'],
            ['nama' => 'Lainnya', 'deskripsi' => null],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_kategori');
    }
};
