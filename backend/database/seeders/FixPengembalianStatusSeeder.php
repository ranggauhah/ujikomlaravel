<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder sementara untuk migrasi data lama.
 * Semua record pengembalian yang ada sebelum redesign dianggap sudah diverifikasi.
 * Jalankan: php artisan db:seed --class=FixPengembalianStatusSeeder
 */
class FixPengembalianStatusSeeder extends Seeder
{
    public function run(): void
    {
        $updated = DB::table('pengembalian')
            ->where('status_pengecekan', 'menunggu_pengecekan')
            ->update(['status_pengecekan' => 'selesai']);

        $this->command->info("Updated {$updated} pengembalian record(s) to 'selesai'.");
    }
}
