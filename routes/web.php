<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsensiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\IzinController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Absensi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/checkin', [AbsensiController::class, 'checkIn'])->name('absensi.checkin');
    Route::post('/absensi/checkout', [AbsensiController::class, 'checkOut'])->name('absensi.checkout');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Karyawan
    Route::resource('karyawan', KaryawanController::class);

    // Izin & Cuti
    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/create', [IzinController::class, 'create'])->name('izin.create');
    Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');
    Route::get('/izin/review', [IzinController::class, 'review'])->name('izin.review');
    Route::post('/izin/{izin}/status', [IzinController::class, 'updateStatus'])->name('izin.status');
});

require __DIR__.'/auth.php';