<?php

namespace App\Support;

use App\Models\Company;
use Illuminate\Support\Facades\View;

/**
 * Memilih template cetak (SO, PO, DO, Tanda Terima Service) per PT.
 *
 * Struktur:  resources/views/cetak/<nama-template>/{layout,so,po,do,tanda-terima-service}.blade.php
 *
 *  - PT memilih template lewat Pengaturan > PT (kolom companies.template_cetak).
 *  - PT baru / yang belum memilih memakai template "default" (desain PT pertama).
 *  - Template boleh berisi sebagian file saja: file yang tidak ada otomatis
 *    jatuh ke versi di folder "default". Jadi membuat desain untuk PT baru cukup
 *    menyalin folder "default" ke nama baru lalu mengedit file yang ingin diubah.
 */
class CetakTemplate
{
    public const DEFAULT = 'default';

    /** Daftar template yang tersedia (nama folder => label). */
    public static function daftar(): array
    {
        $hasil = [];
        foreach (glob(resource_path('views/cetak/*'), GLOB_ONLYDIR) ?: [] as $dir) {
            $nama = basename($dir);
            $hasil[$nama] = ucwords(str_replace(['-', '_'], ' ', $nama)) . ($nama === self::DEFAULT ? ' (desain PT pertama)' : '');
        }
        ksort($hasil);
        return $hasil;
    }

    public static function namaUntuk(?Company $company): string
    {
        $pilihan = $company?->template_cetak;
        return ($pilihan && array_key_exists($pilihan, self::daftar())) ? $pilihan : self::DEFAULT;
    }

    private static function cari(string $template, string $file): string
    {
        $kandidat = "cetak.$template.$file";
        return View::exists($kandidat) ? $kandidat : "cetak." . self::DEFAULT . ".$file";
    }

    public static function view(string $dokumen, ?Company $company, array $data = [])
    {
        $template = self::namaUntuk($company);

        return view(self::cari($template, $dokumen), $data + [
            'perusahaan' => $company,
            'layoutCetak' => self::cari($template, 'layout'),
        ]);
    }
}
