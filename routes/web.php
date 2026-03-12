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
});

require __DIR__.'/auth.php';