<?php

namespace App\Support;

use App\Models\User;

/**
 * SATU-SATUNYA tempat yang menentukan siapa boleh apa (berdasarkan peran).
 *
 * Mau mengubah hak akses (mis. Finance boleh membuka Gudang)? Ubah di MATRIKS di bawah --
 * menu sidebar, lonceng notifikasi, dan pemeriksaan di controller otomatis ikut,
 * jadi tidak ada lagi menu yang tampil tapi halamannya ditolak (atau sebaliknya).
 *
 * Pemakaian:
 *   HakAkses::cek('gudang');                 // controller: tolak 403 kalau tidak berhak
 *   HakAkses::bisa('gudang')                 // true/false untuk user yang login
 *   @if(\App\Support\HakAkses::bisa('po'))   // di Blade
 *
 * Catatan:
 * - Hanya mengatur AKSES LEVEL MENU/FITUR berdasarkan peran. Aturan per-data (misalnya komplain
 *   milik siapa, proyek yang diikuti siapa) tetap di masing-masing controller/service.
 * - Modul yang punya aturan peran rumit sendiri (Pengiriman, Desain, Dokumen, Produk/Tayang)
 *   tetap memakai service-nya masing-masing.
 */
class HakAkses
{
    // Id peran (tabel roles)
    public const DIREKTUR          = 1;
    public const FINANCE           = 2;
    public const PO                = 3;
    public const GUDANG            = 4;
    public const SALES             = 5;
    public const PENGIRIMAN        = 6;
    public const TEKNISI           = 7;
    public const PRODUK            = 8;
    public const TAYANG            = 9;
    public const RESPON            = 10;
    public const ADMIN             = 11;
    public const DESAIN            = 12;
    public const DOKUMEN           = 13;
    public const FINANCE_PEMBELIAN = 14;

    /**
     * izin => peran yang boleh.
     * Super Admin hanya otomatis lolos untuk izin yang tercantum di SUPER_LOLOS.
     */
    private const MATRIKS = [
        // Absensi & karyawan
        'izin.review'        => [1, 11],
        'reimburse.review'   => [1, 2, 11],
        'rekap_absensi'      => [11],
        'karyawan.kelola'    => [11],
        'pengaturan'         => [11],
        'perusahaan'         => [11],
        'menu.admin'         => [11],
        'dashboard.admin'    => [11],

        // Operasional
        'komplain'           => [1, 4, 5, 7, 11],
        'komplain.semua'     => [1, 11],            // melihat semua komplain, bukan hanya miliknya
        'gudang'             => [1, 2, 3, 4, 11],
        'penerimaan'         => [1, 3, 4, 11],
        'aset'               => [1, 2, 3, 4, 11],
        'proyek.kelola'      => [1, 10, 11],        // membuat/mengatur/menghapus proyek
        'permintaan_produk.buat' => [1, 5, 11],

        // Keuangan
        'menu.keuangan'      => [1, 2, 3, 5, 11, 14],
        'so'                 => [1, 2, 3, 5, 11],
        'po'                 => [1, 2, 3, 11, 14],
        'customer'           => [1, 2, 3, 11, 14],
        'supplier'           => [1, 2, 3, 11, 14],
        'pengeluaran'        => [1, 2, 11],
        'laporan.keuangan'   => [1, 2, 11],
    ];

    /** Izin yang otomatis lolos untuk Super Admin (tidak terikat peran). */
    private const SUPER_LOLOS = ['karyawan.kelola', 'penerimaan', 'proyek.kelola', 'permintaan_produk.buat'];

    /** Daftar izin (dipakai tes). */
    public static function semua(): array
    {
        return self::MATRIKS;
    }

    /** Peran yang berhak atas izin ini. */
    public static function peran(string $izin): array
    {
        return self::MATRIKS[$izin] ?? throw new \InvalidArgumentException("Izin tidak dikenal: $izin");
    }

    public static function bisa(string $izin, ?User $user = null): bool
    {
        $user ??= auth()->user();
        if (!$user) {
            return false;
        }

        $peran = self::peran($izin);

        if ($user->isSuperAdmin() && in_array($izin, self::SUPER_LOLOS, true)) {
            return true;
        }

        return in_array((int) $user->role_id, $peran, true);
    }

    public static function cek(string $izin, string $pesan = 'Akses ditolak.'): void
    {
        if (!self::bisa($izin)) {
            abort(403, $pesan);
        }
    }
}
