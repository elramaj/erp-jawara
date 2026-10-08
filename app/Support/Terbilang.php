<?php

namespace App\Support;

/** Angka -> kalimat Bahasa Indonesia, mis. 1500000 -> "satu juta lima ratus ribu rupiah". */
class Terbilang
{
    private const SATUAN = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

    public static function rupiah(float|int|string|null $angka): string
    {
        $n = (int) round((float) $angka);
        if ($n === 0) {
            return 'nol rupiah';
        }
        $teks = ($n < 0 ? 'minus ' : '') . trim(self::baca(abs($n)));
        return trim(preg_replace('/\s+/', ' ', $teks)) . ' rupiah';
    }

    private static function baca(int $n): string
    {
        if ($n < 12) return self::SATUAN[$n];
        if ($n < 20) return self::baca($n - 10) . ' belas';
        if ($n < 100) return self::baca(intdiv($n, 10)) . ' puluh ' . self::baca($n % 10);
        if ($n < 200) return 'seratus ' . self::baca($n - 100);
        if ($n < 1000) return self::baca(intdiv($n, 100)) . ' ratus ' . self::baca($n % 100);
        if ($n < 2000) return 'seribu ' . self::baca($n - 1000);
        if ($n < 1000000) return self::baca(intdiv($n, 1000)) . ' ribu ' . self::baca($n % 1000);
        if ($n < 1000000000) return self::baca(intdiv($n, 1000000)) . ' juta ' . self::baca($n % 1000000);
        if ($n < 1000000000000) return self::baca(intdiv($n, 1000000000)) . ' miliar ' . self::baca($n % 1000000000);
        return self::baca(intdiv($n, 1000000000000)) . ' triliun ' . self::baca($n % 1000000000000);
    }
}
