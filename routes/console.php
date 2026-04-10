<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;
use App\Models\Absensi;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $batasWaktu = now()->subMonths(2);

    $absensiLama = Absensi::where('tanggal', '<', $batasWaktu)->get();

    foreach ($absensiLama as $a) {
        if ($a->foto_masuk) {
            Storage::disk('public')->delete($a->foto_masuk);
            $a->update(['foto_masuk' => null]);
        }
        if ($a->foto_keluar) {
            Storage::disk('public')->delete($a->foto_keluar);
            $a->update(['foto_keluar' => null]);
        }
    }
})->monthly()->description('Auto-delete foto absensi > 2 bulan');