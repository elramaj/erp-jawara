<?php

namespace App\Services;

use App\Models\Fb;
use App\Models\Fj;
use App\Models\Komplain;
use App\Models\Po;
use App\Models\Proyek;
use App\Models\Reimburse;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Daftar tugas lintas modul untuk lonceng & badge sidebar.
 * Semua bersifat "daftar tugas": hilang sendiri begitu datanya diselesaikan.
 * Data otomatis terbatas pada PT user (scope company), Super Admin melihat semua PT.
 *
 * Role: 1 Direktur, 2 Finance, 3 PO, 4 Gudang, 7 Teknisi, 10 Respon, 11 Admin, 14 Finance Pembelian.
 */
class TugasService
{
    /** Tagihan ditampilkan bila jatuh tempo dalam sekian hari (termasuk yang sudah lewat). */
    public const HARI_TAGIHAN = 7;

    /** Deadline proyek ditampilkan bila dalam sekian hari (termasuk yang sudah lewat). */
    public const HARI_DEADLINE = 7;

    private static array $cache = [];

    private static function role(User $u): int
    {
        return (int) $u->role_id;
    }

    private static function punya(User $u, array $roles): bool
    {
        return $u->isSuperAdmin() || in_array(self::role($u), $roles, true);
    }

    /** Klaim reimburse menunggu persetujuan. Klaim milik sendiri tidak dihitung (tidak boleh disetujui sendiri). */
    public static function reimburse(User $u): Collection
    {
        if (!self::punya($u, [1, 2, 11])) {
            return collect();
        }
        return Reimburse::with('user')->where('status', 'pending')
            ->where('user_id', '!=', $u->id)
            ->orderBy('created_at')->get();
    }

    /** Faktur jual (tagihan ke customer) belum lunas yang jatuh tempo ≤ HARI_TAGIHAN hari / terlewat. Faktur tanpa jatuh tempo tidak dihitung. */
    public static function tagihanJual(User $u): Collection
    {
        if (!self::punya($u, [2])) {
            return collect();
        }
        return Fj::with('so')->where('status', '!=', 'paid')->whereNotNull('jatuh_tempo')
            ->whereDate('jatuh_tempo', '<=', now()->addDays(self::HARI_TAGIHAN)->toDateString())
            ->orderBy('jatuh_tempo')->get();
    }

    /** Faktur beli (tagihan dari supplier) belum lunas yang jatuh tempo ≤ HARI_TAGIHAN hari / terlewat. */
    public static function tagihanBeli(User $u): Collection
    {
        if (!self::punya($u, [2, 14])) {
            return collect();
        }
        return Fb::with('po.supplier')->where('status', '!=', 'paid')->whereNotNull('jatuh_tempo')
            ->whereDate('jatuh_tempo', '<=', now()->addDays(self::HARI_TAGIHAN)->toDateString())
            ->orderBy('jatuh_tempo')->get();
    }

    /** Komplain baru yang belum ditugaskan ke siapa pun. */
    public static function komplainBaru(User $u): Collection
    {
        if (!self::punya($u, [1, 7, 11])) {
            return collect();
        }
        return Komplain::where('status', 'open')->whereNull('handled_by')->orderBy('created_at')->get();
    }

    /** PO yang sudah dikonfirmasi tapi barangnya belum (lengkap) datang. */
    public static function poMenunggu(User $u): Collection
    {
        if (!self::punya($u, [3, 4])) {
            return collect();
        }
        return Po::with('supplier')->whereIn('status', ['confirmed', 'partial'])->orderBy('tanggal')->get();
    }

    /** Deadline proyek: Direktur/Respon/Admin untuk semua proyek, role lain hanya proyek yang diikutinya. */
    public static function deadlineProyek(User $u): Collection
    {
        $q = Proyek::whereNotIn('status', ['selesai', 'dibatalkan'])
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<=', now()->addDays(self::HARI_DEADLINE)->toDateString());

        if (!self::punya($u, [1, 10, 11])) {
            $q->whereHas('anggota', fn($a) => $a->where('user_id', $u->id));
        }
        return $q->orderBy('deadline')->get();
    }

    /** Semua tugas sekaligus (di-cache per request supaya sidebar + lonceng tidak query berulang). */
    public static function ringkasan(User $u): array
    {
        return self::$cache[$u->id] ??= [
            'reimburse'    => self::reimburse($u),
            'tagihan_jual' => self::tagihanJual($u),
            'tagihan_beli' => self::tagihanBeli($u),
            'komplain'     => self::komplainBaru($u),
            'po'           => self::poMenunggu($u),
            'deadline'     => self::deadlineProyek($u),
        ];
    }

    public static function jumlah(User $u, string ...$kunci): int
    {
        $r = self::ringkasan($u);
        return collect($kunci)->sum(fn($k) => $r[$k]->count());
    }

    public static function flush(): void
    {
        self::$cache = [];
    }
}
