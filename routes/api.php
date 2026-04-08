<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\GudangController;
use Illuminate\Support\Facades\Route;

// Auth
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Absensi
    Route::get('/absensi/status', [AbsensiController::class, 'status']);
    Route::post('/absensi/checkin', [AbsensiController::class, 'checkIn']);
    Route::post('/absensi/checkout', [AbsensiController::class, 'checkOut']);
    Route::get('/absensi/riwayat', [AbsensiController::class, 'riwayat']);

    // Gudang - scan harus SEBELUM /{id}
    Route::get('/gudang/scan', [GudangController::class, 'scan']);
    Route::get('/gudang', [GudangController::class, 'index']);
    Route::get('/gudang/{id}', [GudangController::class, 'show']);
    Route::post('/gudang/masuk', [GudangController::class, 'storeMasuk']);
    Route::post('/gudang/keluar', [GudangController::class, 'storeKeluar']);
});