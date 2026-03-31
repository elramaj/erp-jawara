<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsensiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\RekapAbsensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SoController;
use App\Http\Controllers\PoController;
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\KomplainController;
use App\Http\Controllers\CompanyController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    // Absensi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/checkin', [AbsensiController::class, 'checkIn'])->name('absensi.checkin');
    Route::post('/absensi/checkout', [AbsensiController::class, 'checkOut'])->name('absensi.checkout');

    // Absensi Mobile
    Route::get('/absensi/mobile', [AbsensiController::class, 'mobile'])->name('absensi.mobile');
    Route::post('/absensi/checkin-mobile', [AbsensiController::class, 'checkInMobile'])->name('absensi.checkin.mobile');
    Route::post('/absensi/checkout-mobile', [AbsensiController::class, 'checkOutMobile'])->name('absensi.checkout.mobile');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Karyawan
    Route::resource('karyawan', KaryawanController::class)->parameters(['karyawan' => 'user']);

    // Izin & Cuti
    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/create', [IzinController::class, 'create'])->name('izin.create');
    Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');
    Route::get('/izin/review', [IzinController::class, 'review'])->name('izin.review');
    Route::post('/izin/{izin}/status', [IzinController::class, 'updateStatus'])->name('izin.status');

    // Rekap Absensi
    Route::get('/rekap-absensi', [RekapAbsensiController::class, 'index'])->name('rekap.index');
    Route::get('/rekap-absensi/{user}', [RekapAbsensiController::class, 'detail'])->name('rekap.detail');
    Route::post('/rekap-absensi/export', [RekapAbsensiController::class, 'export'])->name('rekap.export');

    // Profil
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::post('/profil/update', [ProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/ganti-password', [ProfilController::class, 'gantiPassword'])->name('profil.password');

    // Proyek
    Route::resource('proyek', ProyekController::class)->except(['edit', 'update']);
    Route::post('/proyek/{proyek}/progress', [ProyekController::class, 'updateProgress'])->name('proyek.progress');
    Route::post('/proyek/{proyek}/milestone', [ProyekController::class, 'storeMilestone'])->name('proyek.milestone');
    Route::post('/milestone/{milestone}/status', [ProyekController::class, 'updateMilestone'])->name('milestone.status');
    Route::post('/proyek/{proyek}/dokumen', [ProyekController::class, 'uploadDokumen'])->name('proyek.dokumen');

    // Gudang
    Route::get('/gudang', [GudangController::class, 'index'])->name('gudang.index');
    Route::get('/gudang/barang/create', [GudangController::class, 'createBarang'])->name('gudang.barang.create');
    Route::post('/gudang/barang', [GudangController::class, 'storeBarang'])->name('gudang.barang.store');
    Route::get('/gudang/barang/{barang}', [GudangController::class, 'showBarang'])->name('gudang.barang.show');
    Route::post('/gudang/barang/{barang}/masuk', [GudangController::class, 'storeMasuk'])->name('gudang.masuk');
    Route::post('/gudang/barang/{barang}/keluar', [GudangController::class, 'storeKeluar'])->name('gudang.keluar');
    Route::get('/gudang/opname', [GudangController::class, 'opname'])->name('gudang.opname');
    Route::post('/gudang/opname', [GudangController::class, 'storeOpname'])->name('gudang.opname.store');
    Route::delete('/gudang/barang/{barang}/hapus', [GudangController::class, 'destroyBarang'])->name('gudang.barang.destroy');

    // Master Data
    Route::resource('customer', CustomerController::class);
    Route::resource('supplier', SupplierController::class);

    // Penjualan (SO)
    Route::resource('so', SoController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/so/{so}/sj', [SoController::class, 'storeSj'])->name('so.sj.store');
    Route::post('/so/{so}/fj', [SoController::class, 'storeFj'])->name('so.fj.store');
    Route::post('/fj/{fj}/bayar', [SoController::class, 'storeBayarFj'])->name('fj.bayar');

    // Pembelian (PO)
    Route::resource('po', PoController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/po/{po}/barang-datang', [PoController::class, 'storeBarangDatang'])->name('po.barang_datang');
    Route::post('/po/{po}/fb', [PoController::class, 'storeFb'])->name('po.fb.store');
    Route::post('/fb/{fb}/bayar', [PoController::class, 'storeBayarFb'])->name('fb.bayar');

    // Laporan Keuangan
    Route::get('/laporan-keuangan', [LaporanKeuanganController::class, 'index'])->name('laporan.keuangan');
    Route::get('/laporan-keuangan/export-excel', [LaporanKeuanganController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/laporan-keuangan/export-pdf', [LaporanKeuanganController::class, 'exportPdf'])->name('laporan.pdf');

    // Pengaturan
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan/department', [PengaturanController::class, 'storeDepartment'])->name('pengaturan.department.store');
    Route::put('/pengaturan/department/{department}', [PengaturanController::class, 'updateDepartment'])->name('pengaturan.department.update');
    Route::delete('/pengaturan/department/{department}', [PengaturanController::class, 'destroyDepartment'])->name('pengaturan.department.destroy');
    Route::post('/pengaturan/jam-kerja', [PengaturanController::class, 'updateJamKerja'])->name('pengaturan.jamkerja');

    // Komplain
    Route::resource('komplain', KomplainController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/komplain/{komplain}/status', [KomplainController::class, 'updateStatus'])->name('komplain.status');

    // Company
    Route::resource('company', CompanyController::class);
});

require __DIR__.'/auth.php';