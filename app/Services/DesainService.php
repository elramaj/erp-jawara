<?php

namespace App\Services;

use App\Models\PermintaanDesain;
use App\Models\Tayang;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Aturan akses, pembuatan otomatis dari Tayang, dan notifikasi modul Desain.
 *
 * Role: 1 Direktur, 5 Sales, 9 Tayang, 11 Admin, 12 Desain.
 */
class DesainService
{
    private static function role(User $u): int
    {
        return (int) $u->role_id;
    }

    /** Mengerjakan permintaan: ambil, kirim hasil. */
    public static function kerjakan(User $u): bool
    {
        return $u->isSuperAdmin() || in_array(self::role($u), [1, 11, 12], true);
    }

    /** Boleh mengajukan permintaan desain. */
    public static function bisaMeminta(User $u): bool
    {
        return $u->isSuperAdmin() || in_array(self::role($u), [1, 5, 9, 11], true);
    }

    public static function lihat(User $u): bool
    {
        return self::kerjakan($u) || self::bisaMeminta($u);
    }

    public static function lihatSemua(User $u): bool
    {
        return self::kerjakan($u) || self::role($u) === 9;
    }

    public static function bisaLihatPermintaan(User $u, PermintaanDesain $p): bool
    {
        return self::lihatSemua($u) || (int) $p->created_by === (int) $u->id;
    }

    /** Peminta, Direktur, Admin, atau Super Admin yang menyetujui / minta revisi / membatalkan. */
    public static function bisaPutuskan(User $u, PermintaanDesain $p): bool
    {
        if ($u->isSuperAdmin() || in_array(self::role($u), [1, 11], true)) {
            return true;
        }
        return (int) $p->created_by === (int) $u->id && self::bisaMeminta($u);
    }

    public static function nomorBaru(int $companyId): string
    {
        $prefix = 'DSN-' . date('Ymd') . '-';
        $urut = PermintaanDesain::withoutCompanyScope()
            ->where('company_id', $companyId)
            ->where('no_desain', 'like', $prefix . '%')
            ->count() + 1;

        return $prefix . str_pad($urut, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Dipanggil saat Tayang menyimpan data: menyelaraskan permintaan desain dengan kebutuhan Tayang.
     * - Butuh desain & belum ada permintaan aktif  -> buat permintaan baru.
     * - Butuh desain & permintaan masih "baru"      -> brief ikut diperbarui.
     * - Tidak butuh lagi & permintaan masih "baru"  -> dibatalkan.
     * Permintaan yang sudah dikerjakan Desain tidak disentuh.
     */
    public static function selaraskanDariTayang(Tayang $tayang, User $user): ?PermintaanDesain
    {
        $aktif = PermintaanDesain::where('tayang_id', $tayang->id)
            ->whereIn('status', PermintaanDesain::STATUS_AKTIF)
            ->latest('id')->first();

        if ($tayang->perlu_desain) {
            if ($aktif) {
                if ($aktif->status === 'baru') {
                    $aktif->update(['brief' => $tayang->catatan_desain]);
                }
                return $aktif;
            }
            return DB::transaction(function () use ($tayang, $user) {
                return PermintaanDesain::create([
                    'company_id' => $tayang->company_id,
                    'no_desain'  => self::nomorBaru((int) $tayang->company_id),
                    'judul'      => 'Desain tayang: ' . $tayang->judul,
                    'brief'      => $tayang->catatan_desain,
                    'proyek_id'  => $tayang->proyek_id,
                    'tayang_id'  => $tayang->id,
                    'status'     => 'baru',
                    'created_by' => $user->id,
                ]);
            });
        }

        if ($aktif && $aktif->status === 'baru') {
            $aktif->update(['status' => 'dibatalkan', 'alasan_batal' => 'Tayang tidak lagi membutuhkan desain.']);
        }
        return null;
    }

    /** Untuk lonceng & badge sidebar. 'tugas' = pekerjaan desainer, 'review' = menunggu keputusan peminta. */
    public static function ringkasan(User $u): array
    {
        $kosong = collect();

        $tugas = ($u->isSuperAdmin() || self::role($u) === 12)
            ? PermintaanDesain::where(function ($q) use ($u) {
                    $q->whereIn('status', ['baru', 'revisi'])
                      ->orWhere(fn($w) => $w->where('status', 'dikerjakan')->where('desainer_id', $u->id));
                })->orderBy('deadline')->orderBy('created_at')->get()
            : $kosong;

        $review = ($u->isSuperAdmin() || self::bisaMeminta($u))
            ? PermintaanDesain::where('status', 'review')
                ->when(!$u->isSuperAdmin(), fn($q) => $q->where('created_by', $u->id))
                ->orderBy('dikirim_at')->get()
            : $kosong;

        return compact('tugas', 'review');
    }
}
