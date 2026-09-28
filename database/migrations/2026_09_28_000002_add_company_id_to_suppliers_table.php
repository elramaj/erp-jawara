<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sama seperti customers -- Supplier sekarang per-company, tiap PT
     * punya master supplier sendiri walau namanya kebetulan sama.
     */
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
        });

        $companyPertama = DB::table('companies')->orderBy('id')->value('id');
        if ($companyPertama) {
            DB::table('suppliers')->whereNull('company_id')->update(['company_id' => $companyPertama]);
        }
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
};
