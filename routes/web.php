<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsensiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\ReimburseController;
use App\Http\Controllers\RekapAbsensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\AsetController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SoController;
use App\Http\Controllers\PengirimanController;
use App\Http\Controllers\PermintaanProdukController;
use App\Http\Controllers\TayangController;
use App\Http\Controllers\PoController;
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\PengeluaranController;
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

    // Absensi — redirect ke mobile (GPS + Foto wajib)
    Route::get('/absensi', function () {
        return redirect()->route('absensi.mobile');
    })->name('absensi.index');

    // Absensi Mobile (GPS + Foto)
    Route::get('/absensi/mobile', [AbsensiController::class, 'mobile'])->name('absensi.mobile');
    Route::post('/absensi/checkin-mobile', [AbsensiController::class, 'checkInMobile'])->name('absensi.checkin.mobile');
    Route::post('/absensi/checkout-mobile', [AbsensiController::class, 'checkOutMobile'])->name('absensi.checkout.mobile');

    // Absensi desktop (tetap ada untuk rekap & riwayat)
    Route::get('/absensi/riwayat', [AbsensiController::class, 'index'])->name('absensi.riwayat');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Karyawan
    Route::resource('karyawan', KaryawanController::class)->parameters(['karyawan' => 'user']);
    Route::post('/karyawan/{user}/restore', [KaryawanController::class, 'restore'])->name('karyawan.restore');

    // Izin & Cuti
    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/create', [IzinController::class, 'create'])->name('izin.create');
    Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');
    Route::get('/izin/review', [IzinController::class, 'review'])->name('izin.review');
    Route::post('/izin/{izin}/status', [IzinController::class, 'updateStatus'])->name('izin.status');

    // Reimburse
    Route::get('/reimburse', [ReimburseController::class, 'index'])->name('reimburse.index');
    Route::get('/reimburse/create', [ReimburseController::class, 'create'])->name('reimburse.create');
    Route::post('/reimburse', [ReimburseController::class, 'store'])->name('reimburse.store');
    Route::get('/reimburse/review', [ReimburseController::class, 'review'])->name('reimburse.review');
    Route::post('/reimburse/{reimburse}/status', [ReimburseController::class, 'updateStatus'])->name('reimburse.status');
    Route::post('/reimburse/{reimburse}/batalkan', [ReimburseController::class, 'batalkan'])->name('reimburse.batalkan');

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

    // Permintaan Produk (Sales -> Produk) & Tayang (e-catalog)
    Route::get('/permintaan-produk', [PermintaanProdukController::class, 'index'])->name('produk.index');
    Route::get('/permintaan-produk/buat', [PermintaanProdukController::class, 'create'])->name('produk.create');
    Route::post('/permintaan-produk', [PermintaanProdukController::class, 'store'])->name('produk.store');
    Route::get('/permintaan-produk/{permintaan}', [PermintaanProdukController::class, 'show'])->name('produk.show');
    Route::post('/permintaan-produk/{permintaan}/ambil', [PermintaanProdukController::class, 'ambil'])->name('produk.ambil');
    Route::post('/permintaan-produk/{permintaan}/item/{item}/opsi', [PermintaanProdukController::class, 'tambahOpsi'])->name('produk.opsi.store');
    Route::delete('/permintaan-produk/{permintaan}/opsi/{opsi}', [PermintaanProdukController::class, 'hapusOpsi'])->name('produk.opsi.destroy');
    Route::post('/permintaan-produk/{permintaan}/kirim', [PermintaanProdukController::class, 'kirimKeSales'])->name('produk.kirim');
    Route::post('/permintaan-produk/{permintaan}/konfirmasi', [PermintaanProdukController::class, 'konfirmasi'])->name('produk.konfirmasi');
    Route::post('/permintaan-produk/{permintaan}/opsi-lain', [PermintaanProdukController::class, 'mintaOpsiLain'])->name('produk.opsi_lain');
    Route::post('/permintaan-produk/{permintaan}/batalkan', [PermintaanProdukController::class, 'batalkan'])->name('produk.batalkan');

    Route::get('/tayang', [TayangController::class, 'index'])->name('tayang.index');
    Route::get('/tayang/{tayang}', [TayangController::class, 'show'])->name('tayang.show');
    Route::post('/tayang/{tayang}/mulai', [TayangController::class, 'mulai'])->name('tayang.mulai');
    Route::put('/tayang/{tayang}', [TayangController::class, 'update'])->name('tayang.update');
    Route::post('/tayang/{tayang}/tayangkan', [TayangController::class, 'tayangkan'])->name('tayang.tayangkan');
    Route::post('/tayang/{tayang}/turunkan', [TayangController::class, 'turunkan'])->name('tayang.turunkan');

    // Pengiriman (jadwal kirim barang proyek ke customer)
    Route::get('/pengiriman', [PengirimanController::class, 'index'])->name('pengiriman.index');
    Route::get('/pengiriman/proyek/{proyek}/buat', [PengirimanController::class, 'create'])->name('pengiriman.create');
    Route::post('/pengiriman/proyek/{proyek}', [PengirimanController::class, 'store'])->name('pengiriman.store');
    Route::get('/pengiriman/{pengiriman}/cetak', [PengirimanController::class, 'cetak'])->name('pengiriman.cetak');
    Route::get('/pengiriman/{pengiriman}', [PengirimanController::class, 'show'])->name('pengiriman.show');
    Route::get('/pengiriman/{pengiriman}/edit', [PengirimanController::class, 'edit'])->name('pengiriman.edit');
    Route::put('/pengiriman/{pengiriman}', [PengirimanController::class, 'update'])->name('pengiriman.update');
    Route::post('/pengiriman/{pengiriman}/konfirmasi', [PengirimanController::class, 'konfirmasi'])->name('pengiriman.konfirmasi');
    Route::post('/pengiriman/{pengiriman}/revisi', [PengirimanController::class, 'mintaRevisi'])->name('pengiriman.revisi');
    Route::post('/pengiriman/{pengiriman}/kirim', [PengirimanController::class, 'kirim'])->name('pengiriman.kirim');
    Route::post('/pengiriman/{pengiriman}/terima', [PengirimanController::class, 'terima'])->name('pengiriman.terima');
    Route::post('/pengiriman/{pengiriman}/batalkan', [PengirimanController::class, 'batalkan'])->name('pengiriman.batalkan');

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

    // Aset
    Route::get('/aset', [AsetController::class, 'index'])->name('aset.index');
Route::get('/aset/create', [AsetController::class, 'create'])->name('aset.create');
Route::post('/aset', [AsetController::class, 'store'])->name('aset.store');
Route::get('/aset/{aset}', [AsetController::class, 'show'])->name('aset.show');
Route::get('/aset/{aset}/edit', [AsetController::class, 'edit'])->name('aset.edit');
Route::put('/aset/{aset}', [AsetController::class, 'update'])->name('aset.update');
Route::post('/aset/{aset}/pindah-tangan', [AsetController::class, 'pindahTangan'])->name('aset.pindah');
Route::delete('/aset/{aset}', [AsetController::class, 'destroy'])->name('aset.destroy');

    // Master Data
    Route::resource('customer', CustomerController::class);
    Route::resource('supplier', SupplierController::class);

    // Penjualan (SO)
    Route::resource('so', SoController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('/so/{so}/cetak', [SoController::class, 'cetak'])->name('so.cetak');
    Route::post('/so/{so}/sj', [SoController::class, 'storeSj'])->name('so.sj.store');
    Route::post('/so/{so}/fj', [SoController::class, 'storeFj'])->name('so.fj.store');
    Route::post('/fj/{fj}/bayar', [SoController::class, 'storeBayarFj'])->name('fj.bayar');

    // Pembelian (PO)
    Route::resource('po', PoController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('/po/{po}/cetak', [PoController::class, 'cetak'])->name('po.cetak');
    Route::post('/po/{po}/barang-datang', [PoController::class, 'storeBarangDatang'])->name('po.barang_datang');
    Route::post('/po/{po}/fb', [PoController::class, 'storeFb'])->name('po.fb.store');
    Route::post('/fb/{fb}/bayar', [PoController::class, 'storeBayarFb'])->name('fb.bayar');

    // Laporan Keuangan
    Route::get('/laporan-keuangan', [LaporanKeuanganController::class, 'index'])->name('laporan.keuangan');
    Route::get('/laporan-keuangan/export-excel', [LaporanKeuanganController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/laporan-keuangan/export-pdf', [LaporanKeuanganController::class, 'exportPdf'])->name('laporan.pdf');

    // Pengeluaran / Beban Operasional
    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
    Route::post('/pengeluaran', [PengeluaranController::class, 'store'])->name('pengeluaran.store');
    Route::delete('/pengeluaran/{beban}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');

    // Pengaturan
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan/department', [PengaturanController::class, 'storeDepartment'])->name('pengaturan.department.store');
    Route::put('/pengaturan/department/{department}', [PengaturanController::class, 'updateDepartment'])->name('pengaturan.department.update');
    Route::delete('/pengaturan/department/{department}', [PengaturanController::class, 'destroyDepartment'])->name('pengaturan.department.destroy');
    Route::post('/pengaturan/jam-kerja', [PengaturanController::class, 'updateJamKerja'])->name('pengaturan.jamkerja');
    Route::post('/pengaturan/lokasi', [PengaturanController::class, 'updateLokasi'])->name('pengaturan.lokasi');

    // Komplain
    Route::resource('komplain', KomplainController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/komplain/{komplain}/tanda-terima', [KomplainController::class, 'simpanTandaTerima'])->name('komplain.tanda_terima');
    Route::get('/komplain/{komplain}/cetak-terima', [KomplainController::class, 'cetakTerima'])->name('komplain.cetak');
    Route::post('/komplain/{komplain}/status', [KomplainController::class, 'updateStatus'])->name('komplain.status');

    // Company
    Route::resource('company', CompanyController::class);
});

require __DIR__.'/auth.php';