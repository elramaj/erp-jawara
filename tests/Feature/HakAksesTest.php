<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\HakAkses;
use Illuminate\Support\Facades\{DB, Schema};
use Tests\TestCase;

/**
 * Menjaga agar hak akses berdasarkan peran tetap TERPUSAT di App\Support\HakAkses,
 * dan menu/halaman selalu sepakat (tidak ada menu tampil tapi halaman 403, atau sebaliknya).
 */
class HakAksesTest extends TestCase
{
    /** izin => nama route halaman utamanya (cukup untuk mengecek 403 atau tidak). */
    private const RUTE = [
        'izin.review'      => 'izin.review',
        'reimburse.review' => 'reimburse.review',
        'rekap_absensi'    => 'rekap.index',
        'karyawan.kelola'  => 'karyawan.index',
        'pengaturan'       => 'pengaturan.index',
        'perusahaan'       => 'company.index',
        'komplain'         => 'komplain.index',
        'gudang'           => 'gudang.index',
        'penerimaan'       => 'penerimaan.index',
        'aset'             => 'aset.index',
        'proyek.kelola'    => 'proyek.create',
        'so'               => 'so.index',
        'po'               => 'po.index',
        'customer'         => 'customer.index',
        'supplier'         => 'supplier.index',
        'pengeluaran'      => 'pengeluaran.index',
        'laporan.keuangan' => 'laporan.keuangan',
    ];

    private function pakaiDbSementara(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->withoutVite();
        Schema::create('users', function ($t) {
            $t->id(); $t->unsignedBigInteger('company_id')->nullable(); $t->boolean('is_super_admin')->default(0);
            $t->integer('role_id')->nullable(); $t->string('name'); $t->string('email'); $t->string('password');
            $t->boolean('is_active')->default(1); $t->timestamp('email_verified_at')->nullable(); $t->rememberToken(); $t->timestamps();
        });
        for ($r = 1; $r <= 14; $r++) {
            DB::table('users')->insert(['id' => $r, 'company_id' => 1, 'is_super_admin' => 0, 'role_id' => $r, 'name' => "R$r", 'email' => "r$r@x", 'password' => 'x', 'email_verified_at' => now()]);
        }
        DB::table('users')->insert(['id' => 50, 'company_id' => null, 'is_super_admin' => 1, 'role_id' => 5, 'name' => 'Super', 'email' => 's@x', 'password' => 'x', 'email_verified_at' => now()]);
    }

    public function test_semua_izin_mengacu_ke_peran_yang_valid(): void
    {
        foreach (HakAkses::semua() as $izin => $peran) {
            $this->assertNotEmpty($peran, $izin);
            foreach ($peran as $id) {
                $this->assertContains($id, range(1, 14), "Izin $izin memuat peran tak dikenal: $id");
            }
        }
    }

    public function test_kunci_izin_yang_dipakai_di_kode_semuanya_terdaftar(): void
    {
        $dikenal = array_keys(HakAkses::semua());
        $salah = [];
        $dirs = [app_path(), resource_path('views')];
        foreach ($dirs as $dir) {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $f) {
                if (!$f->isFile() || !preg_match('/\.php$/', $f->getFilename())) {
                    continue;
                }
                if (preg_match_all("/HakAkses::(?:bisa|cek|peran)\\(\\s*'([^']+)'/", file_get_contents($f->getPathname()), $m)) {
                    foreach ($m[1] as $k) {
                        if (!in_array($k, $dikenal, true)) {
                            $salah[] = $f->getFilename() . " -> $k";
                        }
                    }
                }
            }
        }
        $this->assertSame([], $salah, "Izin tidak terdaftar di HakAkses::MATRIKS:\n" . implode("\n", $salah));
    }

    public function test_tidak_ada_lagi_pengecekan_peran_mentah_di_controller_dan_tampilan(): void
    {
        // Pola yang dilarang: in_array(...role_id...) / role_id ==/!= <angka>  (di controller & Blade).
        // Pengecualian: form karyawan (memilih peran), dan Services yang punya aturan modul sendiri.
        $pola = ['/in_array\([^;\n]*role_id/', '/role_id\s*[!=]=+\s*\d/'];
        $gagal = [];
        $cek = function (string $dir, string $ekstensi) use ($pola, &$gagal) {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $f) {
                $nama = str_replace('\\', '/', $f->getPathname());
                if (!$f->isFile() || !str_ends_with($nama, $ekstensi)) {
                    continue;
                }
                if (str_contains($nama, 'views/karyawan/') || str_contains($nama, '/Api/')) {
                    continue;
                }
                foreach (file($nama) as $i => $baris) {
                    foreach ($pola as $p) {
                        if (preg_match($p, $baris)) {
                            $gagal[] = basename($nama) . ':' . ($i + 1) . '  ' . trim($baris);
                        }
                    }
                }
            }
        };
        $cek(app_path('Http/Controllers'), '.php');
        $cek(resource_path('views'), '.blade.php');

        $this->assertSame([], $gagal,
            "Pengecekan peran mentah ditemukan. Pindahkan ke App\\Support\\HakAkses:\n" . implode("\n", $gagal));
    }

    public function test_halaman_dijaga_sesuai_matriks_untuk_setiap_peran(): void
    {
        $this->pakaiDbSementara();
        $salah = [];

        foreach (self::RUTE as $izin => $rute) {
            for ($role = 1; $role <= 14; $role++) {
                $user = User::find($role);
                $status = $this->actingAs($user)->get(route($rute))->getStatusCode();
                $seharusnya = HakAkses::bisa($izin, $user);

                if ($seharusnya === false && $status !== 403) {
                    $salah[] = "$rute role $role: seharusnya 403, dapat $status";
                }
                if ($seharusnya === true && $status === 403) {
                    $salah[] = "$rute role $role: seharusnya boleh, tapi 403";
                }
            }
        }

        $this->assertSame([], $salah, implode("\n", $salah));
    }

    public function test_super_admin_hanya_lolos_otomatis_pada_izin_tertentu(): void
    {
        $this->pakaiDbSementara();
        $super = User::find(50);   // is_super_admin, role 5 (Sales)

        $this->assertTrue(HakAkses::bisa('penerimaan', $super));
        $this->assertTrue(HakAkses::bisa('proyek.kelola', $super));
        $this->assertTrue(HakAkses::bisa('karyawan.kelola', $super));
        $this->assertFalse(HakAkses::bisa('gudang', $super));        // tidak otomatis
        $this->assertFalse(HakAkses::bisa('laporan.keuangan', $super));
    }

    public function test_izin_tidak_dikenal_ditolak_keras(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->pakaiDbSementara();
        HakAkses::bisa('izin.yang.tidak.ada', User::find(1));
    }
}
