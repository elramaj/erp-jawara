<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Penyimpanan file PRIVAT (disk "local" = storage/app, tidak bisa dibuka langsung lewat URL).
 * File hanya bisa diunduh lewat controller yang sudah mengecek hak akses.
 */
class BerkasPrivat
{
    private const DISK = 'local';

    /** Ekstensi yang tidak pernah diterima (bisa dieksekusi / berisi script). */
    private const DILARANG = ['php', 'phtml', 'phar', 'html', 'htm', 'js', 'exe', 'sh', 'bat', 'cmd', 'svg', 'xml'];

    /** Hanya gambar ini yang boleh ditampilkan inline (pratinjau); selain itu selalu diunduh. */
    private const INLINE = ['image/png', 'image/jpeg', 'image/webp', 'image/gif'];

    /** Pesan error bila ekstensi file dilarang, atau null bila aman. */
    public static function cekEkstensi(UploadedFile $file): ?string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        return in_array($ext, self::DILARANG, true) ? "File .{$ext} tidak diizinkan." : null;
    }

    /** @return array{path:string,nama_asli:string,mime:?string,ukuran:int} */
    public static function simpan(UploadedFile $file, string $folder): array
    {
        $mime = $file->getMimeType();
        $ukuran = (int) $file->getSize();
        $path = $file->store($folder, self::DISK);

        return [
            'path'      => $path,
            'nama_asli' => mb_substr($file->getClientOriginalName(), 0, 250),
            'mime'      => $mime ? mb_substr($mime, 0, 100) : null,
            'ukuran'    => $ukuran,
        ];
    }

    public static function hapus(?string $path): void
    {
        if ($path) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    public static function ada(?string $path): bool
    {
        return $path && Storage::disk(self::DISK)->exists($path);
    }

    /** Kirim file ke browser: gambar -> inline, lainnya -> unduh. */
    public static function kirim(string $path, string $namaAsli, ?string $mime, bool $paksaUnduh = false)
    {
        abort_unless(self::ada($path), 404, 'File tidak ditemukan.');

        $header = ['X-Content-Type-Options' => 'nosniff'];
        if (!$paksaUnduh && $mime && in_array($mime, self::INLINE, true)) {
            return Storage::disk(self::DISK)->response($path, $namaAsli, $header + ['Content-Type' => $mime], 'inline');
        }
        return Storage::disk(self::DISK)->download($path, $namaAsli, $header);
    }

    public static function ukuranLabel(int $byte): string
    {
        if ($byte >= 1048576) return number_format($byte / 1048576, 1, ',', '.') . ' MB';
        if ($byte >= 1024) return number_format($byte / 1024, 0, ',', '.') . ' KB';
        return $byte . ' B';
    }
}
