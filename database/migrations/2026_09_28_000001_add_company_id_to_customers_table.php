<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Customer sebelumnya tidak punya company_id sama sekali, jadi semua
     * PT lihat & pakai data customer yang sama (satu master gabungan).
     * Sesuai keputusan bisnis: tiap PT punya master customer sendiri-sendiri,
     * walau kebetulan namanya sama dengan customer PT lain. Kolom ini
     * dipakai oleh trait BelongsToCompany di model Customer.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
        });

        // Data lama (sebelum multi-tenant) diarahkan ke company pertama yang
        // terdaftar, supaya tidak "menggantung" tanpa pemilik. Silakan
        // dipindah manual lewat Super Admin kalau ternyata harusnya beda PT.
        $companyPertama = DB::table('companies')->orderBy('id')->value('id');
        if ($companyPertama) {
            DB::table('customers')->whereNull('company_id')->update(['company_id' => $companyPertama]);
        }
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
};
