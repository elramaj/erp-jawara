<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Modul Desain (permintaan desain + file) dan modul Dokumen
     * (arsip legalitas PT bertanggal kedaluwarsa + permintaan dokumen + file).
     *
     * Kolom proyek_id / tayang_id / arsip_id sengaja tanpa foreign key: tipe id tabel asalnya
     * tidak bisa dipastikan (migration asli sebagian tabel tidak ada di repo).
     * Alur Desain : baru -> dikerjakan -> review -> (revisi -> review ...) -> selesai | dibatalkan
     * Alur Dokumen: baru -> diproses -> selesai | dibatalkan
     */
    public function up(): void
    {
        if (!Schema::hasTable('permintaan_desain')) {
            Schema::create('permintaan_desain', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->string('no_desain', 40);
                $table->string('judul', 200);
                $table->text('brief')->nullable();
                $table->string('ukuran_format', 150)->nullable();
                $table->date('deadline')->nullable();
                $table->unsignedBigInteger('proyek_id')->nullable();
                $table->unsignedBigInteger('tayang_id')->nullable();
                $table->string('status', 20)->default('baru');
                $table->text('catatan_revisi')->nullable();
                $table->text('alasan_batal')->nullable();
                $table->unsignedInteger('jumlah_revisi')->default(0);
                $table->unsignedBigInteger('desainer_id')->nullable();
                $table->timestamp('diambil_at')->nullable();
                $table->timestamp('dikirim_at')->nullable();
                $table->timestamp('selesai_at')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->unique(['company_id', 'no_desain']);
                $table->index(['company_id', 'status']);
                $table->index('tayang_id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->foreign('desainer_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('desain_file')) {
            Schema::create('desain_file', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('permintaan_id')->index();
                $table->string('jenis', 10);                    // referensi | hasil
                $table->unsignedInteger('versi')->default(1);   // hasil: pengiriman ke-n
                $table->string('path');
                $table->string('nama_asli');
                $table->string('mime', 100)->nullable();
                $table->unsignedBigInteger('ukuran')->default(0);
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('dokumen_arsip')) {
            Schema::create('dokumen_arsip', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->string('jenis', 100);                   // NIB, SIUP, Akta Pendirian, ...
                $table->string('nama', 200);
                $table->string('nomor', 100)->nullable();
                $table->string('penerbit', 150)->nullable();
                $table->date('tanggal_terbit')->nullable();
                $table->date('tanggal_kedaluwarsa')->nullable(); // kosong = berlaku tanpa batas
                $table->string('file_path');
                $table->string('nama_asli');
                $table->string('mime', 100)->nullable();
                $table->text('catatan')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->index(['company_id', 'tanggal_kedaluwarsa']);
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('permintaan_dokumen')) {
            Schema::create('permintaan_dokumen', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->string('no_permintaan', 40);
                $table->string('jenis_dokumen', 150);
                $table->text('keperluan')->nullable();
                $table->unsignedBigInteger('proyek_id')->nullable();
                $table->date('deadline')->nullable();
                $table->string('status', 20)->default('baru');
                $table->unsignedBigInteger('penyiap_id')->nullable();
                $table->unsignedBigInteger('arsip_id')->nullable();  // bila dipenuhi dari arsip tetap
                $table->text('catatan_penyiap')->nullable();
                $table->text('alasan_batal')->nullable();
                $table->timestamp('diambil_at')->nullable();
                $table->timestamp('selesai_at')->nullable();
                $table->timestamp('dilihat_peminta_at')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->unique(['company_id', 'no_permintaan']);
                $table->index(['company_id', 'status']);
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->foreign('penyiap_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('permintaan_dokumen_file')) {
            Schema::create('permintaan_dokumen_file', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('permintaan_id')->index();
                $table->string('path');
                $table->string('nama_asli');
                $table->string('mime', 100)->nullable();
                $table->unsignedBigInteger('ukuran')->default(0);
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_dokumen_file');
        Schema::dropIfExists('permintaan_dokumen');
        Schema::dropIfExists('dokumen_arsip');
        Schema::dropIfExists('desain_file');
        Schema::dropIfExists('permintaan_desain');
    }
};
