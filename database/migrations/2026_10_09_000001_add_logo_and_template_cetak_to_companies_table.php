<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * logo          : path file logo PT (disk public). Kosong -> kop pakai public/images/logo.png.
     * template_cetak: nama folder di resources/views/cetak/. Kosong -> "default" (desain PT pertama),
     *                 jadi PT baru otomatis memakai desain yang sama dengan PT pertama.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('logo')->nullable();
            $table->string('template_cetak', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['logo', 'template_cetak']);
        });
    }
};
