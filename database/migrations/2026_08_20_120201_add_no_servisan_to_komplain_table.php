<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('komplain', function (Blueprint $table) {
            $table->string('no_servisan', 100)->nullable()->after('masih_garansi');
        });
    }

    public function down(): void
    {
        Schema::table('komplain', function (Blueprint $table) {
            $table->dropColumn('no_servisan');
        });
    }
};