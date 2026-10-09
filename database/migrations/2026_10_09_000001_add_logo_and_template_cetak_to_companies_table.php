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
            // Idempotent: kolom `logo` sudah ada di database lama (dari dump), jadi cek dulu.
            if (! Schema::hasColumn('companies', 'logo')) {
                $table->string('logo')->nullable();
            }
            if (! Schema::hasColumn('companies', 'template_cetak')) {
                $table->string('template_cetak', 50)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'template_cetak')) {
                $table->dropColumn('template_cetak');
            }
        });
    }
};
