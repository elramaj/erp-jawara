<?php

namespace App\Services;

use App\Models\PermintaanProduk;
use App\Models\Tayang;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Aturan akses & notifikasi untuk alur Sales -> Produk -> Tayang (-> Desain).
 *
 * Role: 1 Direktur, 5 Sales, 8 Produk, 9 Tayang, 11 Admin, 12 Desain.
 */
class ProdukTayangService
{
    private static function role(User $u): int
    {
        return (int) $u->role_id;
    }

    /** Produk mencarikan barang & mengisi opsi. */
    public static function kelolaProduk(User $u): bool
    {
        return $u->isSuperAdmin() || in_array(self::role($u), [1, 8, 11], true);
    }

    /** Tayang menyiapkan listing & mengisi link. */
    public static function kelolaTayang(User $u): bool
    {
        return $u->isSuperAdmin() || in_array(self::role($u), [1, 9, 11], true);
    }

    /** Boleh membuka halaman Permintaan Produk. Tayang hanya baca. */
    public static function lihatPermintaan(User $u): bool
    {
        return self::kelolaProduk($u) || in_array(self::role($u), [5, 9], true);
    }

    /** Boleh membuka halaman Tayang. Sales (milik sendiri) & Produk & Desain hanya baca. */
    public static function lihatTayang(User $u): bool
    {
        return self::kelolaTayang($u) || in_array(self::role($u), [5, 8, 12], true);
    }

    public static function lihatSemuaPermintaan(User $u): bool
    {
        return self::kelolaProduk($u) || self::role($u) === 9;
    }

    public static function bisaLihatPermintaan(User $u, PermintaanProduk $p): bool
    {
        if (self::lihatSemuaPermintaan($u)) {
            return true;
        }
        return self::role($u) === 5 && (int) $p->created_by === (int) $u->id;
    }

    /** Pemilik permintaan (Sales), Direktur, atau Super Admin yang menentukan pilihan. */
    public static function bisaKonfirmasi(User $u, PermintaanProduk $p): bool
    {
        if ($u->isSuperAdmin() || self::role($u) === 1) {
            return true;
        }
        return self::role($u) === 5 && (int) $p->created_by === (int) $u->id;
    }

    /** Sales tidak boleh melihat harga beli & nama supplier. */
    public static function lihatHargaBeli(User $u): bool
    {
        return self::kelolaProduk($u);
    }

    /** Ringkasan untuk lonceng & badge sidebar. */
    public static function ringkasan(User $u): array
    {
        $kosong = collect();

        $produk = self::kelolaProduk($u)
            ? PermintaanProduk::whereIn('status', ['baru', 'dicari'])->orderBy('target_tanggal')->orderBy('created_at')->get()
            : $kosong;

        $konfirmasi = $kosong;
        if ($u->isSuperAdmin() || self::role($u) === 1 || self::role($u) === 5) {
            $q = PermintaanProduk::where('status', 'opsi_siap');
            if (self::role($u) === 5 && !$u->isSuperAdmin()) {
                $q->where('created_by', $u->id);
            }
            $konfirmasi = $q->orderBy('opsi_dikirim_at')->get();
        }

        $tayang = self::kelolaTayang($u)
            ? Tayang::whereIn('status', ['antrian', 'disiapkan'])->orderBy('created_at')->get()
            : $kosong;

        $desain = self::role($u) === 12 || $u->isSuperAdmin()
            ? Tayang::where('perlu_desain', true)->whereIn('status', ['antrian', 'disiapkan'])->orderBy('created_at')->get()
            : $kosong;

        return compact('produk', 'konfirmasi', 'tayang', 'desain');
    }
}
