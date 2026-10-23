<?php

use App\Http\Controllers\AlatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriAlatController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ─── Auth Routes (Public) ────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout',           [AuthController::class, 'logout']);
        Route::get('/me',                [AuthController::class, 'me']);
        Route::put('/profile',           [AuthController::class, 'updateProfile']);
        Route::put('/change-password',   [AuthController::class, 'changePassword']);
    });
});

// ─── Protected Routes ────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // ── Kategori Alat: hanya Admin ─────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('kategori-alat', KategoriAlatController::class);
    });

    // ── Alat: GET semua role, CUD hanya Admin ─────────────────────────────
    Route::get('alat',        [AlatController::class, 'index']);
    Route::get('alat/{alat}', [AlatController::class, 'show']);

    Route::middleware('role:admin')->group(function () {
        Route::post('alat',          [AlatController::class, 'store']);
        Route::post('alat/{alat}',   [AlatController::class, 'update']);
        Route::put('alat/{alat}',    [AlatController::class, 'update']);
        Route::delete('alat/{alat}', [AlatController::class, 'destroy']);
    });

    // ── Peminjaman ─────────────────────────────────────────────────────────
    Route::get('peminjaman',             [PeminjamanController::class, 'index']);
    Route::get('peminjaman/{peminjaman}', [PeminjamanController::class, 'show']);

    Route::middleware('role:peminjam')->group(function () {
        Route::post('peminjaman', [PeminjamanController::class, 'store']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::put('peminjaman/{peminjaman}',    [PeminjamanController::class, 'update']);
        Route::delete('peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy']);
    });

    Route::middleware('role:admin,petugas')->group(function () {
        Route::patch('peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve']);
        Route::patch('peminjaman/{peminjaman}/tolak',   [PeminjamanController::class, 'tolak']);
    });

    // ── Pengembalian ───────────────────────────────────────────────────────
    Route::get('pengembalian',               [PengembalianController::class, 'index']);
    Route::get('pengembalian/{pengembalian}', [PengembalianController::class, 'show']);

    Route::middleware('role:peminjam')->group(function () {
        Route::post('pengembalian', [PengembalianController::class, 'store']);
    });

    Route::middleware('role:petugas,admin')->group(function () {
        Route::put('pengembalian/{pengembalian}',    [PengembalianController::class, 'update']);
        Route::delete('pengembalian/{pengembalian}', [PengembalianController::class, 'destroy']);
    });

    // ── Users: hanya Admin ────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });

    // ── Laporan: Admin & Petugas ──────────────────────────────────────────
    Route::middleware('role:admin,petugas')->prefix('laporan')->group(function () {
        Route::get('peminjaman',        [LaporanController::class, 'peminjaman']);
        Route::get('pengembalian',      [LaporanController::class, 'pengembalian']);
        Route::get('stok-alat',         [LaporanController::class, 'stokAlat']);
        Route::get('user',              [LaporanController::class, 'user']);
        Route::get('log-aktivitas',     [LaporanController::class, 'logAktivitas']);
        Route::get('export-peminjaman', [LaporanController::class, 'exportPeminjaman']);
    });
});
