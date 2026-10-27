<?php

namespace Tests\Feature\Isolasi;

use App\Models\{User, Po, So, Komplain, BebanOperasional, Proyek, ProyekMilestone};
use Illuminate\Support\Facades\{DB, Schema};
use Tests\TestCase;

/**
 * Perilaku isolasi antar-PT: user PT B mencoba menyentuh data PT A.
 *
 * Tes ini membuat tabel sementara sendiri di SQLite memori -- TIDAK menyentuh database asli,
 * dan tidak bergantung pada migrasi lama. Kalau kolom tabel di aplikasi berubah dan tes ini
 * gagal karena kolom tidak ada, tambahkan kolomnya di setUp().
 */
class IsolasiPtPerilakuTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Pastikan tidak pernah memakai database asli.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->withoutVite();
        $this->bangunSkema();
        $this->isiData();
    }

    private function tabel(string $nama, callable $kolom): void
    {
        Schema::create($nama, function ($t) use ($kolom) { $t->id(); $kolom($t); $t->timestamps(); });
    }

    private function bangunSkema(): void
    {
        $this->tabel('companies', fn($t) => $t->string('nama')->nullable());
        $this->tabel('users', function ($t) {
            $t->unsignedBigInteger('company_id')->nullable(); $t->boolean('is_super_admin')->default(0);
            $t->integer('role_id')->nullable(); $t->string('name'); $t->string('email'); $t->string('password');
            $t->boolean('is_active')->default(1); $t->timestamp('email_verified_at')->nullable(); $t->rememberToken();
        });
        $this->tabel('suppliers', function ($t) { $t->unsignedBigInteger('company_id')->nullable(); $t->string('nama')->nullable(); });
        $this->tabel('customers', function ($t) { $t->unsignedBigInteger('company_id')->nullable(); $t->string('nama')->nullable(); });
        $this->tabel('proyek', function ($t) {
            $t->unsignedBigInteger('company_id'); $t->string('kode_proyek')->nullable(); $t->string('nama_proyek');
            $t->string('klien')->nullable(); $t->string('status')->default('aktif'); $t->integer('progress')->default(0);
            $t->unsignedBigInteger('created_by')->nullable(); $t->date('deadline')->nullable();
            $t->decimal('nilai_kontrak', 14, 2)->nullable(); $t->date('tanggal_mulai')->nullable(); $t->date('tanggal_selesai')->nullable(); $t->text('deskripsi')->nullable();
        });
        $this->tabel('proyek_anggota', function ($t) { $t->unsignedBigInteger('proyek_id'); $t->unsignedBigInteger('user_id'); $t->string('peran')->nullable(); });
        $this->tabel('proyek_milestone', function ($t) {
            $t->unsignedBigInteger('proyek_id'); $t->string('judul')->nullable(); $t->text('deskripsi')->nullable();
            $t->date('tanggal_target')->nullable(); $t->timestamp('tanggal_selesai')->nullable(); $t->string('status')->default('belum'); $t->integer('urutan')->default(1);
        });
        $this->tabel('po', function ($t) {
            $t->unsignedBigInteger('company_id'); $t->string('no_po'); $t->date('tanggal')->nullable();
            $t->unsignedBigInteger('supplier_id')->nullable(); $t->unsignedBigInteger('proyek_id')->nullable();
            $t->string('status')->default('confirmed'); $t->text('catatan')->nullable(); $t->unsignedBigInteger('created_by')->nullable();
        });
        $this->tabel('po_detail', function ($t) { $t->unsignedBigInteger('po_id'); $t->unsignedBigInteger('barang_id'); $t->integer('jumlah'); $t->integer('jumlah_diterima')->default(0); $t->decimal('harga', 14, 2)->default(0); });
        $this->tabel('so', function ($t) {
            $t->unsignedBigInteger('company_id'); $t->string('no_so'); $t->date('tanggal')->nullable();
            $t->unsignedBigInteger('customer_id')->nullable(); $t->unsignedBigInteger('proyek_id')->nullable();
            $t->string('status')->default('confirmed'); $t->text('catatan')->nullable(); $t->unsignedBigInteger('created_by')->nullable();
        });
        $this->tabel('so_detail', function ($t) { $t->unsignedBigInteger('so_id'); $t->unsignedBigInteger('barang_id'); $t->integer('jumlah'); $t->decimal('harga', 14, 2)->default(0); });
        $this->tabel('sj', function ($t) { $t->string('no_sj'); $t->date('tanggal')->nullable(); $t->unsignedBigInteger('so_id'); $t->string('status')->nullable(); $t->text('catatan')->nullable(); $t->unsignedBigInteger('created_by')->nullable(); });
        $this->tabel('sj_detail', function ($t) { $t->unsignedBigInteger('sj_id'); $t->unsignedBigInteger('barang_id'); $t->integer('jumlah'); });
        $this->tabel('gudang_barang', function ($t) {
            $t->unsignedBigInteger('company_id'); $t->string('kode_barang')->nullable(); $t->string('nama_barang');
            $t->unsignedBigInteger('kategori_id')->nullable(); $t->string('satuan')->nullable(); $t->integer('stok_minimum')->default(0);
            $t->boolean('has_sn')->default(0); $t->text('deskripsi')->nullable();
        });
        $this->tabel('gudang_stok_masuk', function ($t) { $t->unsignedBigInteger('barang_id'); $t->date('tanggal'); $t->integer('jumlah'); $t->integer('sisa'); $t->decimal('harga_beli', 14, 2)->nullable(); $t->string('supplier')->nullable(); $t->string('no_dokumen')->nullable(); $t->text('keterangan')->nullable(); $t->unsignedBigInteger('created_by')->nullable(); });
        $this->tabel('gudang_stok_keluar', function ($t) { $t->unsignedBigInteger('barang_id'); $t->date('tanggal'); $t->integer('jumlah'); $t->decimal('harga_jual', 14, 2)->nullable(); $t->unsignedBigInteger('proyek_id')->nullable(); $t->string('tujuan')->nullable(); $t->string('no_dokumen')->nullable(); $t->text('keterangan')->nullable(); $t->unsignedBigInteger('created_by')->nullable(); });
        $this->tabel('gudang_fifo_detail', function ($t) { $t->unsignedBigInteger('keluar_id'); $t->unsignedBigInteger('masuk_id'); $t->integer('jumlah'); });
        $this->tabel('gudang_serial_number', function ($t) { $t->unsignedBigInteger('barang_id'); $t->unsignedBigInteger('masuk_id')->nullable(); $t->string('serial_number'); $t->string('kondisi')->nullable(); $t->string('status')->nullable(); $t->unsignedBigInteger('keluar_id')->nullable(); $t->text('keterangan')->nullable(); });
        $this->tabel('komplain', function ($t) {
            $t->unsignedBigInteger('company_id')->nullable(); $t->string('no_komplain')->nullable(); $t->unsignedBigInteger('proyek_id')->nullable();
            $t->string('jenis')->nullable(); $t->string('prioritas')->nullable(); $t->string('judul'); $t->text('deskripsi')->nullable();
            $t->string('status')->default('open'); $t->boolean('masih_garansi')->default(0); $t->unsignedBigInteger('created_by')->nullable();
            $t->unsignedBigInteger('handled_by')->nullable(); $t->timestamp('resolved_at')->nullable(); $t->string('no_servisan')->nullable();
            $t->date('tanggal_terima_service')->nullable(); $t->text('kelengkapan_service')->nullable(); $t->text('kondisi_fisik')->nullable();
            $t->string('penyerah_nama')->nullable(); $t->string('penyerah_kontak')->nullable();
        });
        $this->tabel('komplain_timeline', function ($t) { $t->unsignedBigInteger('komplain_id'); $t->text('keterangan')->nullable(); $t->string('status_baru')->nullable(); $t->unsignedBigInteger('created_by')->nullable(); });
        $this->tabel('beban_operasional', function ($t) { $t->unsignedBigInteger('company_id'); $t->unsignedBigInteger('user_id')->nullable(); $t->string('kategori'); $t->date('tanggal'); $t->decimal('nominal', 14, 2); $t->string('keterangan')->nullable(); $t->unsignedBigInteger('created_by')->nullable(); });
    }

    // id: 1 SuperAdmin(tanpa PT) | PT A: 2 PO(3), 3 Gudang(4), 4 Sales(5), 5 Respon(10), 6 Teknisi(7), 7 Finance(2), 8 Sales lain(5), 9 Gudang(4 - bukan anggota)
    // PT B: 20 PO(3), 21 Respon(10), 22 Gudang(4), 23 Finance(2)
    private function isiData(): void
    {
        DB::table('companies')->insert([['id' => 1, 'nama' => 'PT A'], ['id' => 2, 'nama' => 'PT B']]);
        $u = fn($id, $role, $co, $super = 0) => ['id' => $id, 'company_id' => $co, 'is_super_admin' => $super, 'role_id' => $role, 'name' => "U$id", 'email' => "u$id@x", 'password' => 'x', 'email_verified_at' => now()];
        DB::table('users')->insert([
            $u(1, 11, null, 1), $u(2, 3, 1), $u(3, 4, 1), $u(4, 5, 1), $u(5, 10, 1), $u(6, 7, 1), $u(7, 2, 1), $u(8, 5, 1), $u(9, 4, 1),
            $u(20, 3, 2), $u(21, 10, 2), $u(22, 4, 2), $u(23, 2, 2),
        ]);
        DB::table('suppliers')->insert([['id' => 1, 'company_id' => 1, 'nama' => 'Sup A'], ['id' => 2, 'company_id' => 2, 'nama' => 'Sup B']]);
        DB::table('customers')->insert([['id' => 1, 'company_id' => 1, 'nama' => 'Cus A'], ['id' => 2, 'company_id' => 2, 'nama' => 'Cus B']]);
        DB::table('proyek')->insert([
            ['id' => 1, 'company_id' => 1, 'kode_proyek' => 'PRJ-1', 'nama_proyek' => 'Proyek A', 'klien' => 'x', 'status' => 'aktif', 'progress' => 0, 'created_by' => 5, 'deadline' => null, 'nilai_kontrak' => null, 'tanggal_mulai' => null, 'tanggal_selesai' => null, 'deskripsi' => null],
            ['id' => 2, 'company_id' => 2, 'kode_proyek' => 'PRJ-1', 'nama_proyek' => 'Proyek B', 'klien' => 'x', 'status' => 'aktif', 'progress' => 0, 'created_by' => 21, 'deadline' => null, 'nilai_kontrak' => null, 'tanggal_mulai' => null, 'tanggal_selesai' => null, 'deskripsi' => null],
        ]);
        DB::table('proyek_anggota')->insert([['proyek_id' => 1, 'user_id' => 3, 'peran' => null]]);
        DB::table('proyek_milestone')->insert([
            ['id' => 1, 'proyek_id' => 1, 'judul' => 'M-A', 'status' => 'belum', 'urutan' => 1],
            ['id' => 2, 'proyek_id' => 2, 'judul' => 'M-B', 'status' => 'belum', 'urutan' => 1],
        ]);
        DB::table('gudang_barang')->insert([
            ['id' => 1, 'company_id' => 1, 'kode_barang' => 'BRG-1', 'nama_barang' => 'Barang A', 'has_sn' => 1],
            ['id' => 2, 'company_id' => 2, 'kode_barang' => 'BRG-1', 'nama_barang' => 'Barang B', 'has_sn' => 1],
        ]);
        DB::table('gudang_stok_masuk')->insert([
            ['id' => 1, 'barang_id' => 1, 'tanggal' => '2026-10-01', 'jumlah' => 5, 'sisa' => 5],
            ['id' => 2, 'barang_id' => 2, 'tanggal' => '2026-10-01', 'jumlah' => 5, 'sisa' => 5],
        ]);
        DB::table('gudang_serial_number')->insert([
            ['id' => 1, 'barang_id' => 1, 'masuk_id' => 1, 'serial_number' => 'A-1', 'status' => 'tersedia'],
            ['id' => 2, 'barang_id' => 2, 'masuk_id' => 2, 'serial_number' => 'B-1', 'status' => 'tersedia'],
        ]);
        DB::table('po')->insert([['id' => 1, 'company_id' => 1, 'no_po' => 'PO-20261027-001', 'supplier_id' => 1, 'proyek_id' => 1, 'status' => 'confirmed', 'tanggal' => '2026-10-27']]);
        DB::table('so')->insert([['id' => 1, 'company_id' => 1, 'no_so' => 'SO-20261027-001', 'customer_id' => 1, 'proyek_id' => 1, 'status' => 'confirmed', 'tanggal' => '2026-10-27']]);
        DB::table('so_detail')->insert([['so_id' => 1, 'barang_id' => 1, 'jumlah' => 2, 'harga' => 100]]);
        DB::table('komplain')->insert([
            ['id' => 1, 'company_id' => 1, 'no_komplain' => 'CMP-1', 'judul' => 'K milik user 4', 'status' => 'in_progress', 'created_by' => 4, 'handled_by' => 6],
            ['id' => 2, 'company_id' => 1, 'no_komplain' => 'CMP-2', 'judul' => 'K antrian teknisi', 'status' => 'open', 'created_by' => 4, 'handled_by' => null],
        ]);
    }

    private function sebagai(int $id) { return $this->actingAs(User::find($id)); }

    // ------------------------------------------------------------------ binding URL

    public function test_user_pt_lain_mendapat_404_untuk_data_pt_a(): void
    {
        $this->sebagai(20)->get(route('po.show', 1))->assertNotFound();
        $this->sebagai(23)->get(route('so.show', 1))->assertNotFound();
        $this->sebagai(21)->get(route('proyek.show', 1))->assertNotFound();
        $this->sebagai(21)->post(route('proyek.progress', 1), ['progress' => 50, 'status' => 'aktif'])->assertNotFound();
        $this->sebagai(21)->delete(route('proyek.destroy', 1))->assertNotFound();
        $this->assertSame(1, DB::table('proyek')->where('id', 1)->count());
    }

    // ------------------------------------------------------------------ milestone

    public function test_milestone_pt_lain_tidak_bisa_diubah_dan_role_dicek(): void
    {
        // PT B (Respon) mencoba mengubah milestone PT A -> 404, tidak berubah
        $this->sebagai(21)->post(route('milestone.status', 1), ['status' => 'selesai'])->assertNotFound();
        $this->assertSame('belum', DB::table('proyek_milestone')->where('id', 1)->value('status'));

        // PT A, Gudang yang BUKAN anggota proyek -> 403
        $this->sebagai(9)->post(route('milestone.status', 1), ['status' => 'selesai'])->assertForbidden();
        $this->assertSame('belum', DB::table('proyek_milestone')->where('id', 1)->value('status'));

        // PT A, Gudang yang anggota proyek -> boleh (sama seperti tombol di halaman proyek)
        $this->sebagai(3)->post(route('milestone.status', 1), ['status' => 'proses'])->assertRedirect();
        $this->assertSame('proses', DB::table('proyek_milestone')->where('id', 1)->value('status'));

        // PT A, Respon (pengelola) -> boleh
        $this->sebagai(5)->post(route('milestone.status', 1), ['status' => 'selesai'])->assertRedirect();
        $this->assertSame('selesai', DB::table('proyek_milestone')->where('id', 1)->value('status'));
    }

    public function test_hanya_pengelola_yang_boleh_membuat_atau_menghapus_proyek(): void
    {
        $this->sebagai(3)->delete(route('proyek.destroy', 1))->assertForbidden();   // Gudang
        $this->sebagai(3)->post(route('proyek.progress', 1), ['progress' => 10, 'status' => 'aktif'])->assertForbidden();
        $this->sebagai(3)->post(route('proyek.store'), ['kode_proyek' => 'X', 'nama_proyek' => 'X', 'klien' => 'x', 'status' => 'aktif'])->assertForbidden();
        $this->assertSame(1, DB::table('proyek')->where('company_id', 1)->count());

        // Kode proyek sama di PT lain tidak bentrok (unik per PT); anggota dari PT lain ditolak
        $this->sebagai(5)->post(route('proyek.store'), ['kode_proyek' => 'PRJ-1', 'nama_proyek' => 'Baru', 'klien' => 'x', 'status' => 'aktif', 'anggota' => [22]])
            ->assertSessionHasErrors('anggota.0');
        $this->sebagai(21)->post(route('proyek.store'), ['kode_proyek' => 'PRJ-2', 'nama_proyek' => 'Baru B', 'klien' => 'x', 'status' => 'aktif', 'anggota' => [22]])
            ->assertSessionHasNoErrors();
        $this->assertSame(1, DB::table('proyek')->where('company_id', 2)->where('kode_proyek', 'PRJ-2')->count());
    }

    // ------------------------------------------------------------------ PO / SO

    public function test_po_menolak_supplier_barang_proyek_milik_pt_lain(): void
    {
        $dasar = ['no_po' => 'PO-BARU', 'tanggal' => '2026-10-27', 'jumlah' => [1], 'harga' => [100]];

        $this->sebagai(2)->post(route('po.store'), $dasar + ['supplier_id' => 2, 'barang_id' => [1]])->assertSessionHasErrors('supplier_id');
        $this->sebagai(2)->post(route('po.store'), $dasar + ['supplier_id' => 1, 'barang_id' => [2]])->assertSessionHasErrors('barang_id.0');
        $this->sebagai(2)->post(route('po.store'), $dasar + ['supplier_id' => 1, 'barang_id' => [1], 'proyek_id' => 2])->assertSessionHasErrors('proyek_id');
        $this->assertSame(1, Po::withoutCompanyScope()->count());

        $this->sebagai(2)->post(route('po.store'), $dasar + ['supplier_id' => 1, 'barang_id' => [1], 'proyek_id' => 1])->assertSessionHasNoErrors();
        $this->assertSame(2, Po::withoutCompanyScope()->count());
    }

    public function test_nomor_dokumen_sama_boleh_di_pt_berbeda_tapi_tidak_di_pt_sendiri(): void
    {
        // PO-20261027-001 sudah dipakai PT A. PT B memakai nomor yang sama (penomoran otomatis per PT) -> boleh.
        $this->sebagai(20)->post(route('po.store'), ['no_po' => 'PO-20261027-001', 'tanggal' => '2026-10-27', 'supplier_id' => 2, 'barang_id' => [2], 'jumlah' => [1], 'harga' => [1]])
            ->assertSessionHasNoErrors();
        $this->assertSame(2, Po::withoutCompanyScope()->where('no_po', 'PO-20261027-001')->count());

        // Nomor ganda di PT yang sama tetap ditolak
        $this->sebagai(2)->post(route('po.store'), ['no_po' => 'PO-20261027-001', 'tanggal' => '2026-10-27', 'supplier_id' => 1, 'barang_id' => [1], 'jumlah' => [1], 'harga' => [1]])
            ->assertSessionHasErrors('no_po');
    }

    public function test_so_menolak_customer_milik_pt_lain(): void
    {
        $this->sebagai(4)->post(route('so.store'), ['no_so' => 'SO-X', 'tanggal' => '2026-10-27', 'customer_id' => 2, 'barang_id' => [1], 'jumlah' => [1], 'harga' => [1]])
            ->assertSessionHasErrors('customer_id');
        $this->assertSame(1, So::withoutCompanyScope()->count());
    }

    public function test_surat_jalan_hanya_untuk_barang_di_so_dan_tidak_mengurangi_stok_pt_lain(): void
    {
        // Barang 2 milik PT B -> bukan bagian SO PT A
        $this->sebagai(4)->post(route('so.sj.store', 1), ['tanggal' => '2026-10-27', 'barang_id' => [2], 'jumlah' => [3]])
            ->assertSessionHasErrors('barang_id');
        $this->assertSame(5, (int) DB::table('gudang_stok_masuk')->where('id', 2)->value('sisa'));
        $this->assertSame(0, DB::table('sj')->count());
        $this->assertSame(0, DB::table('gudang_stok_keluar')->count());

        // Barang yang benar-benar ada di SO -> jalan, stok PT A berkurang
        $this->sebagai(4)->post(route('so.sj.store', 1), ['tanggal' => '2026-10-27', 'barang_id' => [1], 'jumlah' => [2]])
            ->assertSessionHasNoErrors();
        $this->assertSame(3, (int) DB::table('gudang_stok_masuk')->where('id', 1)->value('sisa'));
    }

    // ------------------------------------------------------------------ gudang

    public function test_stok_keluar_menolak_serial_number_pt_lain(): void
    {
        // Gudang PT A mengirim ID serial number milik PT B (id 2)
        $this->sebagai(3)->post(route('gudang.keluar', 1), ['tanggal' => '2026-10-27', 'jumlah' => 1, 'serial_numbers' => [2]])
            ->assertSessionHasErrors('serial_numbers');
        $this->assertSame('tersedia', DB::table('gudang_serial_number')->where('id', 2)->value('status'));
        $this->assertSame(0, DB::table('gudang_stok_keluar')->count());

        // Barang milik PT B langsung lewat URL -> 404
        $this->sebagai(3)->post(route('gudang.keluar', 2), ['tanggal' => '2026-10-27', 'jumlah' => 1, 'serial_numbers' => [2]])->assertNotFound();

        // SN yang benar -> berhasil
        $this->sebagai(3)->post(route('gudang.keluar', 1), ['tanggal' => '2026-10-27', 'jumlah' => 1, 'serial_numbers' => [1]])
            ->assertSessionHasNoErrors();
        $this->assertSame('terjual', DB::table('gudang_serial_number')->where('id', 1)->value('status'));
        $this->assertSame('tersedia', DB::table('gudang_serial_number')->where('id', 2)->value('status'));
    }

    public function test_opname_dan_proyek_stok_keluar_tidak_menembus_pt(): void
    {
        $this->sebagai(3)->post(route('gudang.opname.store'), ['barang_id' => [2], 'stok_fisik' => [99]])->assertNotFound();
        $this->assertSame(1, DB::table('gudang_stok_masuk')->where('barang_id', 2)->count());

        $this->sebagai(3)->post(route('gudang.keluar', 1), ['tanggal' => '2026-10-27', 'jumlah' => 1, 'proyek_id' => 2, 'serial_numbers' => [1]])
            ->assertSessionHasErrors('proyek_id');
    }

    public function test_kode_barang_boleh_sama_di_pt_lain(): void
    {
        // BRG-1 sudah ada di kedua PT (data awal). PT A mencoba kode yang sudah dipakai PT A sendiri -> ditolak.
        $this->sebagai(3)->post(route('gudang.barang.store'), ['kode_barang' => 'BRG-1', 'nama_barang' => 'dup', 'satuan' => 'pcs', 'stok_minimum' => 0])
            ->assertSessionHasErrors('kode_barang');
        // Kode baru yang belum dipakai PT A tapi... (BRG-2) lolos
        $this->sebagai(3)->post(route('gudang.barang.store'), ['kode_barang' => 'BRG-2', 'nama_barang' => 'ok', 'satuan' => 'pcs', 'stok_minimum' => 0])
            ->assertSessionHasNoErrors();
    }

    // ------------------------------------------------------------------ komplain

    public function test_komplain_hanya_dibuka_oleh_pemilik_penangan_atau_pengelola(): void
    {
        $this->sebagai(8)->get(route('komplain.show', 1))->assertForbidden();                    // Sales lain
        $this->sebagai(8)->post(route('komplain.status', 1), ['status' => 'resolved', 'keterangan' => 'x'])->assertForbidden();
        $this->sebagai(8)->get(route('komplain.cetak', 1))->assertForbidden();
        $this->sebagai(3)->get(route('komplain.show', 1))->assertForbidden();                    // Gudang tak terkait
        $this->sebagai(20)->get(route('komplain.show', 1))->assertNotFound();                    // PT lain

        // Teknisi: boleh komplain yang ditangani dirinya & antrian (open, belum ditugaskan)
        $this->assertNotSame(403, $this->sebagai(6)->get(route('komplain.cetak', 1))->getStatusCode());
        $this->assertNotSame(403, $this->sebagai(6)->get(route('komplain.cetak', 2))->getStatusCode());
        // Pembuat sendiri
        $this->assertNotSame(403, $this->sebagai(4)->get(route('komplain.cetak', 1))->getStatusCode());
        // Direktur/Admin: semua
        $this->assertNotSame(403, $this->sebagai(1)->get(route('komplain.cetak', 1))->getStatusCode());
    }

    public function test_komplain_penanggung_jawab_harus_karyawan_pt_yang_sama(): void
    {
        $this->sebagai(1)->post(route('komplain.status', 1), ['status' => 'in_progress', 'keterangan' => 'x', 'handled_by' => 22])
            ->assertSessionHasErrors('handled_by');
        $this->assertSame(6, (int) DB::table('komplain')->where('id', 1)->value('handled_by'));
    }

    // ------------------------------------------------------------------ Super Admin tanpa PT

    public function test_super_admin_tanpa_pt_tidak_membuat_data_yatim(): void
    {
        $this->sebagai(1)->post(route('pengeluaran.store'), ['kategori' => 'lainnya', 'tanggal' => '2026-10-27', 'nominal' => 1000])
            ->assertSessionHas('error');
        $this->assertSame(0, BebanOperasional::withoutCompanyScope()->count());

        $this->sebagai(1)->post(route('komplain.store'), ['judul' => 'x', 'jenis' => 'barang', 'prioritas' => 'low'])
            ->assertSessionHas('error');
        $this->assertSame(2, Komplain::withoutCompanyScope()->count());   // tetap 2 data awal
    }

    public function test_pengeluaran_menolak_karyawan_pt_lain_untuk_reimburse(): void
    {
        $this->sebagai(7)->post(route('pengeluaran.store'), ['kategori' => 'reimburse', 'tanggal' => '2026-10-27', 'nominal' => 1000, 'user_id' => 23])
            ->assertSessionHasErrors('user_id');
        $this->assertSame(0, BebanOperasional::withoutCompanyScope()->count());
    }
}
