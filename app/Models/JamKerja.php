<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JamKerja extends Model
{
    protected $table = 'jam_kerja';
    public $timestamps = false;
    protected $fillable = ['hari', 'jam_masuk', 'jam_keluar', 'toleransi_menit', 'is_libur'];

    protected $casts = [
        'is_libur' => 'boolean',
    ];

    // Urutan hari dalam bahasa Indonesia, index 0 = Minggu (sama seperti Carbon::dayOfWeek)
    const HARI = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

    /**
     * Ambil jadwal jam kerja untuk hari ini (atau tanggal tertentu).
     * Cocokkan kolom `hari` tanpa peduli besar/kecil huruf, biar gak
     * tergantung locale Carbon (yang butuh package tambahan buat 'id').
     */
    public static function untukTanggal($tanggal = null): ?self
    {
        $tanggal = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
        $namaHari = self::HARI[$tanggal->dayOfWeek];

        return static::whereRaw('LOWER(hari) = ?', [$namaHari])->first();
    }
}
