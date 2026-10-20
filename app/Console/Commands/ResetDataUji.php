<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mengosongkan data transaksi + data user supaya fitur bisa diuji ulang dari nol.
 *
 * DIHAPUS : user (kecuali Super Admin), barang & stok gudang, komplain, proyek, SO/PO/FJ/FB/SJ,
 *           customer, supplier, pengiriman, produk & tayang, absensi, izin, reimburse,
 *           beban operasional, aset (data), token API.
 * DIPERTAHANKAN : Super Admin, PT (companies) beserta logo/template, departemen, jam kerja,
 *           role, kategori (gudang & aset), lokasi kantor, dan struktur tabel (migrations).
 *
 * Default = simulasi (dry-run). Hapus sungguhan hanya dengan --yes lalu ketik nama database.
 */
class ResetDataUji extends Command
{
    protected $signature = 'erp:reset-data {--yes : Jalankan penghapusan sungguhan (tanpa ini hanya simulasi)}
                                           {--allow-production : Izinkan di APP_ENV=production}';

    protected $description = 'Kosongkan data user & transaksi untuk uji ulang (Super Admin, PT, role, dan master tetap aman)';

    /** Tabel yang dikosongkan total. Tabel yang tidak ada di database dilewati otomatis. */
    private const TABEL = [
        // proyek & dokumen penjualan/pembelian
        'proyek_milestone', 'proyek_dokumen', 'proyek_anggota', 'proyek',
        'so_detail', 'so', 'po_detail', 'po',
        'sj_detail', 'sj', 'fj_bayar', 'fj', 'fb_bayar', 'fb',
        'customers', 'suppliers',
        // pengiriman, produk, tayang
        'pengiriman', 'tayang', 'produk_opsi', 'permintaan_produk_item', 'permintaan_produk',
        // komplain
        'komplain_barang_service', 'komplain_timeline', 'komplain',
        // gudang (kategori dipertahankan)
        'gudang_serial_number', 'gudang_fifo_detail', 'gudang_stok_keluar', 'gudang_stok_masuk', 'gudang_barang',
        // SDM & keuangan
        'absensi', 'pengajuan_izin', 'reimburse', 'beban_operasional',
        // aset (kategori dipertahankan)
        'aset_riwayat', 'aset',
        // lain-lain
        'personal_access_tokens', 'notifications', 'sessions', 'cache', 'cache_locks', 'jobs', 'failed_jobs',
    ];

    public function handle(): int
    {
        if (app()->environment('production') && !$this->option('allow-production')) {
            $this->error('APP_ENV=production. Perintah ini ditolak. (Kalau memang yakin: tambahkan --allow-production, dan backup database dulu!)');
            return self::FAILURE;
        }

        $db = DB::connection()->getDatabaseName();
        $jalan = (bool) $this->option('yes');

        $this->line('');
        $this->info(($jalan ? 'HAPUS DATA' : 'SIMULASI (tidak ada yang dihapus)') . " pada database: {$db}");

        $baris = [];
        foreach (self::TABEL as $t) {
            if (Schema::hasTable($t)) {
                $baris[] = [$t, DB::table($t)->count()];
            }
        }

        $userHapus = DB::table('users')->where(function ($q) {
            $q->whereNull('is_super_admin')->orWhere('is_super_admin', 0);
        })->count();
        $superAdmin = DB::table('users')->where('is_super_admin', 1)->count();
        $baris[] = ['users (bukan Super Admin)', $userHapus];

        $this->table(['Tabel', 'Jumlah baris yang dihapus'], $baris);
        $this->line("Super Admin yang dipertahankan: {$superAdmin} akun.");

        if ($superAdmin < 1) {
            $this->error('Tidak ada akun Super Admin (is_super_admin = 1). Dibatalkan supaya kamu tidak terkunci dari aplikasi.');
            return self::FAILURE;
        }

        if (!$jalan) {
            $this->warn('Ini hanya simulasi. Untuk menghapus sungguhan: php artisan erp:reset-data --yes');
            return self::SUCCESS;
        }

        $ketik = $this->ask("Sudah backup? Ketik nama database '{$db}' untuk melanjutkan");
        if ($ketik !== $db) {
            $this->error('Nama database tidak cocok. Dibatalkan.');
            return self::FAILURE;
        }

        Schema::disableForeignKeyConstraints();
        try {
            foreach ($baris as [$t]) {
                if ($t === 'users (bukan Super Admin)') {
                    continue;
                }
                DB::table($t)->truncate();
            }

            $idUser = DB::table('users')->where(function ($q) {
                $q->whereNull('is_super_admin')->orWhere('is_super_admin', 0);
            })->pluck('id');

            // sisa relasi role (paket spatie) milik user yang dihapus
            foreach (['model_has_roles', 'model_has_permissions'] as $t) {
                if (Schema::hasTable($t)) {
                    DB::table($t)->where('model_type', 'App\\Models\\User')->whereIn('model_id', $idUser)->delete();
                }
            }

            DB::table('users')->whereIn('id', $idUser)->delete();
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $this->info('Selesai. Super Admin, PT, role, departemen, jam kerja, dan kategori tetap ada.');
        $this->line('File upload lama (foto komplain, dokumen proyek) di storage tidak ikut terhapus; bersihkan manual kalau perlu.');
        return self::SUCCESS;
    }
}
