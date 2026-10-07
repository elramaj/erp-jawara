<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ganti nama role_id 1 dari "bos" menjadi "direktur".
     * Role_id 11 tetap "admin" (hak aksesnya memang setara admin operasional
     * satu PT). Yang lintas-PT hanya akun dengan flag is_super_admin.
     *
     * Murni label tampilan: semua logic akses di kode memakai role_id angka.
     */
    public function up(): void
    {
        DB::table('roles')->where('id', 1)->update(['name' => 'direktur']);
    }

    public function down(): void
    {
        DB::table('roles')->where('id', 1)->update(['name' => 'bos']);
    }
};
