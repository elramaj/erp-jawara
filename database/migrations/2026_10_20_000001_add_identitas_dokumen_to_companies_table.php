<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Identitas PT yang tampil di dokumen cetak (kop, rekening, tanda tangan, footer). */
    private const KOLOM = [
        'npwp'                  => ['string', 30],
        'bank_nama'             => ['string', 100],
        'bank_rekening'         => ['string', 50],
        'bank_atas_nama'        => ['string', 150],
        'penandatangan_nama'    => ['string', 150],
        'penandatangan_jabatan' => ['string', 100],
        'footer_dokumen'        => ['text', null],
    ];

    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            foreach (self::KOLOM as $nama => [$tipe, $panjang]) {
                if (Schema::hasColumn('companies', $nama)) {
                    continue;
                }
                $kol = $panjang ? $table->{$tipe}($nama, $panjang) : $table->{$tipe}($nama);
                $kol->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $ada = array_filter(array_keys(self::KOLOM), fn($k) => Schema::hasColumn('companies', $k));
            if ($ada) {
                $table->dropColumn(array_values($ada));
            }
        });
    }
};
