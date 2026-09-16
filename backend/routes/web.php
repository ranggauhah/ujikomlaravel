<?php

use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Protected routes (untuk yang sudah login)
Route::middleware(['web'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::get('/kategori', function () {
        return view('kategori.index');
    })->name('kategori');

    Route::get('/alat', function () {
        return view('alat.index');
    })->name('alat');

    Route::get('/peminjaman', function () {
        return view('peminjaman.index');
    })->name('peminjaman');

    Route::get('/pengembalian', function () {
        return view('pengembalian.index');
    })->name('pengembalian');

    Route::get('/users', function () {
        return view('users.index');
    })->name('users');

    Route::get('/laporan', function () {
        return view('laporan.index');
    })->name('laporan');
});
