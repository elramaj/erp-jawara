<?php

namespace Tests\Feature\Isolasi;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use ReflectionMethod;
use Tests\TestCase;

/**
 * PENJAGA ISOLASI ANTAR-PT (tanpa database).
 *
 * Tes ini memeriksa KODE, bukan data. Tujuannya: kalau nanti ada modul/baris kode baru
 * yang berpotensi membocorkan data antar PT, tes ini gagal dan memaksa kita
 * memeriksanya dulu -- baru boleh ditambahkan ke daftar "sudah ditinjau" di bawah.
 */
class PenjagaIsolasiPtTest extends TestCase
{
    /** Model yang sengaja TIDAK memakai filter otomatis PT, beserta alasannya. */
    private const MODEL_TANPA_FILTER_SUDAH_DITINJAU = [
        'User'       => 'Sengaja tanpa global scope (dipakai saat login). Dipakai lewat scopeForCurrentCompany().',
        'Company'    => 'Entitas PT itu sendiri.',
    ];

    /**
     * Model yang bisa di-bind lewat URL ({user}, {milestone}, ...) tapi tanpa filter PT otomatis.
     * Masing-masing SUDAH dicek manual di controllernya. Model baru yang masuk daftar ini
     * harus ditinjau dulu.
     */
    private const BINDING_TANPA_FILTER_SUDAH_DITINJAU = [
        'User'                 => 'KaryawanController::cekAksesCompany & RekapAbsensiController::detail',
        'Company'              => 'CompanyController::cekAksesCompany',
        'ProyekMilestone'      => 'ProyekController::updateMilestone -> lewat Proyek (terfilter PT) + cekAksesProyek',
        'PermintaanProdukItem' => 'Selalu bersama {permintaan} (terfilter) + pastikanItem()',
        'ProdukOpsi'           => 'Selalu bersama {permintaan} (terfilter) + pastikanItem()',
        'DesainFile'           => 'Selalu bersama {desain} (terfilter) + cek permintaan_id',
        'PermintaanDokumenFile'=> 'Selalu bersama {dokumen} (terfilter) + cek permintaan_id',
        'Department'           => 'MASTER BERSAMA semua PT (belum punya company_id) -- lihat catatan audit.',
    ];

    /** File yang boleh memakai withoutCompanyScope() (penomoran urut & pengecekan lintas data). */
    private const BOLEH_TANPA_SCOPE = [
        'Services/DokumenService.php',
        'Services/DesainService.php',
        'Http/Controllers/PermintaanProdukController.php',
        'Http/Controllers/PengirimanController.php',
        'Http/Controllers/PengeluaranController.php',
        'Http/Controllers/KomplainController.php',
        'Http/Controllers/PoController.php',
        'Http/Controllers/CustomerController.php',
        'Http/Controllers/SupplierController.php',
        'Http/Controllers/SoController.php',
        'Traits/BelongsToCompany.php',
    ];

    /** Tabel yang boleh diakses lewat DB::table() mentah (tidak lewat model terfilter). */
    private const DB_TABLE_SUDAH_DITINJAU = [
        'pengaturan_lokasi', // lokasi global (fallback) -- lihat catatan audit
        'jam_kerja',         // selalu difilter company_id di PengaturanController::updateJamKerja
        'izin_attachments',  // lampiran izin milik user sendiri
    ];

    private function modelKelas(): array
    {
        $hasil = [];
        foreach (glob(app_path('Models/*.php')) as $file) {
            $cls = 'App\\Models\\' . basename($file, '.php');
            if (class_exists($cls) && is_subclass_of($cls, Model::class)) {
                $hasil[] = $cls;
            }
        }
        return $hasil;
    }

    private function tabelTenant(): array
    {
        $tabel = ['users'];
        foreach ($this->modelKelas() as $cls) {
            if (in_array(BelongsToCompany::class, class_uses_recursive($cls), true)) {
                $tabel[] = (new $cls)->getTable();
            }
        }
        return array_values(array_unique($tabel));
    }

    private function sumberPhp(): array
    {
        $out = [];
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(app_path(), \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $f) {
            if ($f->isFile() && $f->getExtension() === 'php') {
                $out[str_replace(app_path() . DIRECTORY_SEPARATOR, '', $f->getPathname())] = file_get_contents($f->getPathname());
            }
        }
        return $out;
    }

    public function test_model_dengan_company_id_wajib_memakai_filter_pt(): void
    {
        $gagal = [];
        foreach ($this->modelKelas() as $cls) {
            $nama = class_basename($cls);
            $punyaCompanyId = in_array('company_id', (new $cls)->getFillable(), true);
            $pakaiTrait = in_array(BelongsToCompany::class, class_uses_recursive($cls), true);

            if ($punyaCompanyId && !$pakaiTrait && !isset(self::MODEL_TANPA_FILTER_SUDAH_DITINJAU[$nama])) {
                $gagal[] = $nama;
            }
        }

        $this->assertSame([], $gagal,
            "Model ini punya company_id tapi tidak memakai trait BelongsToCompany (data PT bisa tercampur): " . implode(', ', $gagal));
    }

    public function test_model_yang_di_bind_lewat_url_harus_terfilter_pt_atau_sudah_ditinjau(): void
    {
        $gagal = [];
        foreach (app('router')->getRoutes() as $route) {
            $aksi = $route->getActionName();
            if (!str_contains($aksi, '@')) {
                continue;
            }
            [$kelas, $metode] = explode('@', $aksi);
            if (!class_exists($kelas) || !method_exists($kelas, $metode)) {
                continue;
            }
            foreach ((new ReflectionMethod($kelas, $metode))->getParameters() as $p) {
                $tipe = $p->getType();
                if (!$tipe || $tipe->isBuiltin() || !is_subclass_of($tipe->getName(), Model::class)) {
                    continue;
                }
                $model = $tipe->getName();
                $terfilter = in_array(BelongsToCompany::class, class_uses_recursive($model), true);
                if (!$terfilter && !isset(self::BINDING_TANPA_FILTER_SUDAH_DITINJAU[class_basename($model)])) {
                    $gagal[] = class_basename($model) . ' (' . $route->uri() . ')';
                }
            }
        }

        $this->assertSame([], array_values(array_unique($gagal)),
            "Route memakai model tanpa filter PT -- ID di URL bisa menembus PT lain. Tinjau dulu:\n" . implode("\n", array_unique($gagal)));
    }

    public function test_aturan_exists_dan_unique_tidak_boleh_polos_pada_tabel_milik_pt(): void
    {
        $tenant = $this->tabelTenant();
        $gagal = [];

        foreach ($this->sumberPhp() as $file => $isi) {
            if (!str_starts_with($file, 'Http' . DIRECTORY_SEPARATOR)) {
                continue;
            }
            if (preg_match_all("/['\"][^'\"]*\\b(exists|unique):([a-z_]+)[,'\"|]/", $isi, $m, PREG_SET_ORDER)) {
                foreach ($m as $hit) {
                    $tabel = $hit[2];
                    // email pengguna memang unik se-sistem (untuk login).
                    if ($hit[1] === 'unique' && $tabel === 'users') {
                        continue;
                    }
                    if (in_array($tabel, $tenant, true)) {
                        $gagal[] = "$file -> {$hit[1]}:$tabel";
                    }
                }
            }
        }

        $this->assertSame([], $gagal,
            "Aturan 'exists:'/'unique:' polos TIDAK ikut filter PT (bisa memilih ID milik PT lain). "
            . "Pakai Rule::exists(...)->where('company_id', ...):\n" . implode("\n", $gagal));
    }

    public function test_withoutCompanyScope_hanya_di_tempat_yang_sudah_ditinjau(): void
    {
        $gagal = [];
        foreach ($this->sumberPhp() as $file => $isi) {
            $rel = str_replace(DIRECTORY_SEPARATOR, '/', $file);
            if (str_contains($isi, 'withoutCompanyScope(') && !in_array($rel, self::BOLEH_TANPA_SCOPE, true)) {
                $gagal[] = $rel;
            }
        }

        $this->assertSame([], $gagal,
            "withoutCompanyScope() melewati filter PT. Tinjau dulu lalu daftarkan di tes ini:\n" . implode("\n", $gagal));
    }

    public function test_DB_table_mentah_tidak_menyentuh_tabel_milik_pt(): void
    {
        $tenant = $this->tabelTenant();
        $gagal = [];

        foreach ($this->sumberPhp() as $file => $isi) {
            if (str_starts_with($file, 'Console')) {
                continue; // perintah artisan bekerja lintas PT secara sadar
            }
            if (preg_match_all("/DB::table\\(\\s*['\"]([a-z_]+)['\"]/", $isi, $m)) {
                foreach ($m[1] as $tabel) {
                    if (in_array($tabel, self::DB_TABLE_SUDAH_DITINJAU, true)) {
                        continue;
                    }
                    if (in_array($tabel, $tenant, true)) {
                        $gagal[] = "$file -> $tabel";
                    }
                }
            }
        }

        $this->assertSame([], $gagal,
            "DB::table() pada tabel milik PT melewati filter otomatis. Pakai model, atau tambahkan where company_id:\n" . implode("\n", $gagal));
    }

    public function test_penyimpanan_data_baru_tidak_memakai_company_id_mentah_user(): void
    {
        // Super Admin tidak punya PT -> 'company_id' => auth()->user()->company_id menghasilkan NULL
        // (data "yatim"). Gunakan $this->companyIdUntukSimpan() di Controller.
        $gagal = [];
        foreach ($this->sumberPhp() as $file => $isi) {
            $rel = str_replace(DIRECTORY_SEPARATOR, '/', $file);
            if (!str_starts_with($rel, 'Http/Controllers/') || str_starts_with($rel, 'Http/Controllers/Api/')) {
                continue;
            }
            if (preg_match("/'company_id'\\s*=>\\s*auth\\(\\)->user\\(\\)->company_id/", $isi)) {
                $gagal[] = $rel;
            }
        }

        $this->assertSame([], $gagal,
            "Gunakan companyIdUntukSimpan() pada penyimpanan data baru:\n" . implode("\n", $gagal));
    }
}
