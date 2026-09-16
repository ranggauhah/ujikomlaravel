<?php

use App\Http\Controllers\AlatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriAlatController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Hak Akses sesuai Tabel Modul UJIKOM:
|
| Admin   : Login, Logout, CRUD User, CRUD Alat, CRUD Kategori,
|           CRUD Peminjaman, CRUD Pengembalian, Log Aktivitas
| Petugas : Login, Logout, Menyetujui Peminjaman, Memantau Pengembalian,
|           Mencetak Laporan
| Peminjam: Login, Logout, Melihat Alat, Mengajukan Peminjaman,
|           Mengembalikan Alat (lewat fitur "ajukan pengembalian")
|--------------------------------------------------------------------------
*/

// ─── Auth Routes (Public) ────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });
});

// ─── Protected Routes ────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // ── Kategori Alat: hanya Admin ─────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('kategori-alat', KategoriAlatController::class);
    });

    // ── Alat: GET semua role, CUD hanya Admin ─────────────────────────────
    Route::get('alat',        [AlatController::class, 'index']); // semua bisa lihat
    Route::get('alat/{alat}', [AlatController::class, 'show']);  // semua bisa lihat

    Route::middleware('role:admin')->group(function () {
        Route::post('alat',              [AlatController::class, 'store']);
        Route::post('alat/{alat}',       [AlatController::class, 'update']); // via _method=PUT
        Route::put('alat/{alat}',        [AlatController::class, 'update']);
        Route::delete('alat/{alat}',     [AlatController::class, 'destroy']);
    });

    // ── Peminjaman ─────────────────────────────────────────────────────────
    // Semua authenticated bisa index & show
    Route::get('peminjaman',             [PeminjamanController::class, 'index']);
    Route::get('peminjaman/{peminjaman}', [PeminjamanController::class, 'show']);

    // Hanya Peminjam yang boleh mengajukan peminjaman baru
    Route::middleware('role:peminjam')->group(function () {
        Route::post('peminjaman', [PeminjamanController::class, 'store']);
    });

    // Admin bisa update status dan hapus
    Route::middleware('role:admin')->group(function () {
        Route::put('peminjaman/{peminjaman}',    [PeminjamanController::class, 'update']);
        Route::delete('peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy']);
    });

    // Petugas: approve / tolak peminjaman (update status)
    Route::middleware('role:admin,petugas')->group(function () {
        Route::patch('peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve']);
        Route::patch('peminjaman/{peminjaman}/tolak',   [PeminjamanController::class, 'tolak']);
    });

    // ── Pengembalian ───────────────────────────────────────────────────────
    // Semua authenticated bisa index & show
    Route::get('pengembalian',                  [PengembalianController::class, 'index']);
    Route::get('pengembalian/{pengembalian}',    [PengembalianController::class, 'show']);

    // Peminjam: ajukan pengembalian sendiri
    Route::post('pengembalian',                 [PengembalianController::class, 'store']);

    // Admin & Petugas: konfirmasi / update / hapus pengembalian
    Route::middleware('role:admin,petugas')->group(function () {
        Route::put('pengembalian/{pengembalian}',    [PengembalianController::class, 'update']);
        Route::delete('pengembalian/{pengembalian}', [PengembalianController::class, 'destroy']);
    });

    // ── Users: hanya Admin ────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });

    // ── Laporan: Admin & Petugas ──────────────────────────────────────────
    Route::middleware('role:admin,petugas')->prefix('laporan')->group(function () {
        Route::get('peminjaman',       [LaporanController::class, 'peminjaman']);
        Route::get('pengembalian',     [LaporanController::class, 'pengembalian']);
        Route::get('stok-alat',        [LaporanController::class, 'stokAlat']);
        Route::get('user',             [LaporanController::class, 'user']);
        Route::get('log-aktivitas',    [LaporanController::class, 'logAktivitas']);
        Route::get('export-peminjaman',[LaporanController::class, 'exportPeminjaman']);
    });
});
