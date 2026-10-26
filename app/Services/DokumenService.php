<?php

namespace App\Services;

use App\Models\DokumenArsip;
use App\Models\PermintaanDokumen;
use App\Models\User;

/**
 * Aturan akses & notifikasi modul Dokumen.
 *
 * Role: 1 Direktur, 2 Finance, 5 Sales, 13 Dokumen, 14 Finance Pembelian.
 * Admin (11) sengaja TIDAK termasuk: dokumen legalitas hanya untuk role yang ditetapkan.
 */
class DokumenService
{
    private static function role(User $u): int
    {
        return (int) $u->role_id;
    }

    /** Menyiapkan dokumen & mengelola arsip legalitas (tambah/ubah/hapus). */
    public static function kelola(User $u): bool
    {
        return $u->isSuperAdmin() || in_array(self::role($u), [1, 13, 14], true);
    }

    /** Penyiap permintaan dokumen (mengambil & menyelesaikan). Direktur hanya meminta/memantau. */
    public static function siapkan(User $u): bool
    {
        return $u->isSuperAdmin() || in_array(self::role($u), [13, 14], true);
    }

    /** Melihat & mengunduh arsip legalitas PT. */
    public static function lihatArsip(User $u): bool
    {
        return self::kelola($u) || in_array(self::role($u), [2, 5], true);
    }

    public static function bisaMeminta(User $u): bool
    {
        return $u->isSuperAdmin() || in_array(self::role($u), [1, 5], true);
    }

    public static function lihatPermintaan(User $u): bool
    {
        return self::bisaMeminta($u) || self::siapkan($u);
    }

    public static function lihatSemuaPermintaan(User $u): bool
    {
        return self::siapkan($u) || $u->isSuperAdmin() || self::role($u) === 1;
    }

    public static function bisaLihatPermintaan(User $u, PermintaanDokumen $p): bool
    {
        return self::lihatSemuaPermintaan($u) || (int) $p->created_by === (int) $u->id;
    }

    public static function bisaBatalkan(User $u, PermintaanDokumen $p): bool
    {
        return self::siapkan($u) || $u->isSuperAdmin() || self::role($u) === 1
            || ((int) $p->created_by === (int) $u->id && self::bisaMeminta($u));
    }

    public static function nomorBaru(int $companyId): string
    {
        $prefix = 'DOK-' . date('Ymd') . '-';
        $urut = PermintaanDokumen::withoutCompanyScope()
            ->where('company_id', $companyId)
            ->where('no_permintaan', 'like', $prefix . '%')
            ->count() + 1;

        return $prefix . str_pad($urut, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Untuk lonceng & badge sidebar.
     * 'siapkan' = permintaan menunggu penyiap, 'selesai' = hasil siap & belum dilihat peminta,
     * 'arsip'   = arsip legalitas kedaluwarsa / segera habis (untuk pengelola arsip).
     */
    public static function ringkasan(User $u): array
    {
        $kosong = collect();

        $siapkan = self::siapkan($u)
            ? PermintaanDokumen::whereIn('status', ['baru', 'diproses'])->orderBy('deadline')->orderBy('created_at')->get()
            : $kosong;

        $selesai = self::bisaMeminta($u)
            ? PermintaanDokumen::where('status', 'selesai')->whereNull('dilihat_peminta_at')
                ->when(!$u->isSuperAdmin(), fn($q) => $q->where('created_by', $u->id))
                ->orderBy('selesai_at')->get()
            : $kosong;

        $arsip = self::kelola($u)
            ? DokumenArsip::whereNotNull('tanggal_kedaluwarsa')
                ->whereDate('tanggal_kedaluwarsa', '<=', now()->addDays(DokumenArsip::HARI_PERINGATAN)->toDateString())
                ->orderBy('tanggal_kedaluwarsa')->get()
            : $kosong;

        return compact('siapkan', 'selesai', 'arsip');
    }
}
