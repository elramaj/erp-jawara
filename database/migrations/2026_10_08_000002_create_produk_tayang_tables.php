<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Alur Produk & Tayang:
     *   Sales buat permintaan (spesifikasi dari customer)        -> baru
     *   Produk ambil & carikan opsi barang per item              -> dicari
     *   Produk kirim opsi ke Sales                               -> opsi_siap
     *   Sales pilih opsi per item & konfirmasi                   -> dikonfirmasi  (otomatis masuk antrian Tayang)
     *   Tayang siapkan listing & isi link e-catalog              -> antrian > disiapkan > tayang > turun
     * Sales bisa minta opsi lain (opsi_siap -> dicari) atau batalkan.
     *
     * proyek_id & customer_id sengaja tanpa foreign key: migration asli tabel
     * proyek/customers tidak ada di repo, tipe kolom id-nya tidak bisa dipastikan.
     */
    public function up(): void
    {
        Schema::create('permintaan_produk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('no_permintaan', 40);
            $table->unsignedBigInteger('proyek_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('judul');
            $table->string('sumber', 100)->nullable();       // mis. e-catalog, tender, penunjukan langsung
            $table->date('target_tanggal')->nullable();       // kapan opsi dibutuhkan
            $table->text('catatan')->nullable();

            $table->string('status', 20)->default('baru');   // baru, dicari, opsi_siap, dikonfirmasi, dibatalkan
            $table->text('alasan_revisi')->nullable();       // Sales minta opsi lain
            $table->text('alasan_batal')->nullable();

            $table->unsignedBigInteger('produk_pic')->nullable();
            $table->timestamp('diambil_at')->nullable();
            $table->timestamp('opsi_dikirim_at')->nullable();
            $table->unsignedBigInteger('dikonfirmasi_oleh')->nullable();
            $table->timestamp('dikonfirmasi_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'no_permintaan']);
            $table->index(['company_id', 'status']);
            $table->index('created_by');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('produk_pic')->references('id')->on('users')->onDelete('set null');
            $table->foreign('dikonfirmasi_oleh')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('permintaan_produk_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permintaan_id');
            $table->string('nama_kebutuhan');
            $table->text('spesifikasi')->nullable();
            $table->unsignedInteger('jumlah')->default(1);
            $table->string('satuan', 30)->nullable();
            $table->timestamps();

            $table->foreign('permintaan_id')->references('id')->on('permintaan_produk')->onDelete('cascade');
        });

        Schema::create('produk_opsi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('nama_barang');
            $table->string('merk', 100)->nullable();
            $table->text('spesifikasi_ditawarkan')->nullable();
            $table->decimal('harga_beli', 15, 2)->nullable();   // internal: tidak ditampilkan ke Sales
            $table->decimal('harga_jual', 15, 2)->nullable();
            $table->string('supplier_nama')->nullable();        // internal: tidak ditampilkan ke Sales
            $table->string('link_sumber', 500)->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('is_terpilih')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('permintaan_produk_item')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('tayang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('permintaan_id');
            $table->unsignedBigInteger('proyek_id')->nullable();
            $table->string('judul');
            $table->string('platform', 100)->nullable();        // e-katalog, marketplace, website, dll
            $table->string('link_tayang', 500)->nullable();
            $table->string('status', 20)->default('antrian');   // antrian, disiapkan, tayang, turun
            $table->boolean('perlu_desain')->default(false);
            $table->text('catatan_desain')->nullable();
            $table->text('catatan_tayang')->nullable();
            $table->unsignedBigInteger('ditangani_oleh')->nullable();
            $table->timestamp('tayang_at')->nullable();
            $table->timestamp('turun_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('permintaan_id')->references('id')->on('permintaan_produk')->onDelete('cascade');
            $table->foreign('ditangani_oleh')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tayang');
        Schema::dropIfExists('produk_opsi');
        Schema::dropIfExists('permintaan_produk_item');
        Schema::dropIfExists('permintaan_produk');
    }
};
