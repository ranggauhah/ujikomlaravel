<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduler — Pengingat Jatuh Tempo
|--------------------------------------------------------------------------
| Jalankan setiap hari jam 07:00 WIB untuk mengecek:
|   - Peminjaman yang jatuh tempo besok (H-1) → kirim pengingat
|   - Peminjaman yang sudah melewati batas   → kirim notifikasi terlambat
|
| Cara menjalankan manual (untuk tes):
|   php artisan notifikasi:cek-jatuh-tempo
|
| Cara mengaktifkan scheduler otomatis (di server/WSL):
|   * * * * * cd /path-ke-project && php artisan schedule:run >> /dev/null 2>&1
*/
Schedule::command('notifikasi:cek-jatuh-tempo')
    ->dailyAt('07:00')
    ->description('Cek jatuh tempo dan terlambat peminjaman alat')
    ->withoutOverlapping();   // Jangan jalankan dobel jika proses sebelumnya belum selesai
