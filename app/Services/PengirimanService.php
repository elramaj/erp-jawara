<?php

namespace App\Services;

use App\Models\Pengiriman;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Logika "kapan proyek perlu dijadwalkan pengirimannya" + siapa yang
 * perlu diberi tahu. Dipakai oleh PengirimanController (halaman) dan
 * lonceng notifikasi di layout, supaya aturannya hanya ada di satu tempat.
 *
 * Aturan proyek perlu dijadwalkan (status proyek harus "aktif" dan belum
 * punya pengiriman yang berjalan/selesai):
 *   1. Semua barang di PO proyek sudah diterima gudang  -> "Barang lengkap"
 *   2. Atau sebagian barang sudah lengkap DAN deadline proyek
 *      <= HARI_DEADLINE hari lagi (termasuk yang sudah lewat).
 */
class PengirimanService
{
    public const HARI_DEADLINE = 14;

    private static ?Collection $cacheSiap = null;

    public static function flush(): void
    {
        self::$cacheSiap = null;
    }

    public static function bisaKelola(User $user): bool
    {
        return $user->isSuperAdmin() || in_array((int) $user->role_id, [1, 6, 11], true);
    }

    /** Melihat SEMUA jadwal di PT-nya: tim kelola + Gudang (barang fisik keluar dari gudang). */
    public static function lihatSemua(User $user): bool
    {
        return self::bisaKelola($user) || (int) $user->role_id === 4;
    }

    public static function bisaLihat(User $user): bool
    {
        return self::lihatSemua($user) || (int) $user->role_id === 5;
    }

    /**
     * Apakah user boleh mengonfirmasi jadwal milik proyek ini:
     * Sales pemilik paket, Gudang (kenyataan di lapangan sering Gudang yang
     * memastikan barang siap keluar), Direktur, atau Super Admin.
     */
    public static function bisaKonfirmasi(User $user, Proyek $proyek): bool
    {
        if ($user->isSuperAdmin() || in_array((int) $user->role_id, [1, 4], true)) {
            return true;
        }
        if ((int) $user->role_id !== 5) {
            return false;
        }
        return $proyek->anggota()->where('user_id', $user->id)->exists();
    }

    /**
     * Proyek yang sudah waktunya dijadwalkan pengirimannya.
     *
     * @return Collection<int, array{proyek:Proyek, kel:array, alasan:string, hari:?int}>
     */
    public static function perluDijadwalkan(): Collection
    {
        if (self::$cacheSiap !== null) {
            return self::$cacheSiap;
        }

        $batas = now()->startOfDay()->addDays(self::HARI_DEADLINE);

        $hasil = Proyek::where('status', 'aktif')
            ->whereDoesntHave('pengiriman', fn($q) => $q->whereIn('status', Pengiriman::STATUS_AKTIF))
            ->with(['po.detail', 'anggota.user'])
            ->get()
            ->map(function (Proyek $p) use ($batas) {
                $kel = $p->kelengkapanBarang();
                $hari = $p->deadline ? now()->startOfDay()->diffInDays($p->deadline->copy()->startOfDay(), false) : null;

                if ($kel['lengkap']) {
                    $alasan = 'Semua barang sudah lengkap';
                } elseif ($kel['item_lengkap'] >= 1 && $p->deadline && $p->deadline->copy()->startOfDay()->lte($batas)) {
                    $alasan = 'Sebagian barang lengkap & deadline mendekat';
                } else {
                    return null;
                }

                return ['proyek' => $p, 'kel' => $kel, 'alasan' => $alasan, 'hari' => $hari];
            })
            ->filter()
            ->sortBy(fn($r) => $r['hari'] ?? PHP_INT_MAX)
            ->values();

        return self::$cacheSiap = $hasil;
    }

    /** Jadwal yang menunggu konfirmasi. Sales hanya melihat proyek miliknya; Gudang/Direktur/Super Admin melihat semua. */
    public static function menungguKonfirmasi(User $user): Collection
    {
        $isSales = (int) $user->role_id === 5 && !$user->isSuperAdmin();
        if (!$isSales && !$user->isSuperAdmin() && !in_array((int) $user->role_id, [1, 4], true)) {
            return collect();
        }

        $q = Pengiriman::where('status', 'diusulkan')->with('proyek');
        if ($isSales) {
            $q->whereHas('proyek.anggota', fn($a) => $a->where('user_id', $user->id));
        }
        return $q->orderBy('tanggal_kirim')->get();
    }

    /** Jadwal yang dikembalikan Sales untuk direvisi (untuk tim Pengiriman). */
    public static function perluRevisi(User $user): Collection
    {
        if (!self::bisaKelola($user)) {
            return collect();
        }
        return Pengiriman::where('status', 'revisi')->with('proyek')->orderBy('tanggal_kirim')->get();
    }

    /** Ringkasan untuk lonceng notifikasi. */
    public static function ringkasan(User $user): array
    {
        return [
            'siap'       => self::bisaKelola($user) ? self::perluDijadwalkan() : collect(),
            'konfirmasi' => self::menungguKonfirmasi($user),
            'revisi'     => self::perluRevisi($user),
        ];
    }
}
