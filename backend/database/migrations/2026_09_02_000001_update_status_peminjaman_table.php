<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah status 'menunggu' dan 'ditolak' ke tabel peminjaman.
     * Sesuai tabel modul UJIKOM:
     *   - Peminjam mengajukan → status: menunggu
     *   - Petugas/Admin menyetujui → status: dipinjam
     *   - Petugas/Admin menolak → status: ditolak
     *   - Dikembalikan → status: dikembalikan
     *   - Melewati deadline → status: terlambat
     */
    public function up(): void
    {
        // MySQL: ubah enum dengan MODIFY COLUMN
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status 
            ENUM('menunggu','dipinjam','dikembalikan','terlambat','ditolak') 
            NOT NULL DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status 
            ENUM('dipinjam','dikembalikan','terlambat') 
            NOT NULL DEFAULT 'dipinjam'");
    }
};
