<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah nilai 'terlambat' ke enum kondisi_alat pada tabel pengembalian.
     */
    public function up(): void
    {
        // Ubah kolom enum langsung via raw SQL karena Laravel tidak support
        // modifikasi enum secara native di semua driver.
        DB::statement("ALTER TABLE pengembalian MODIFY COLUMN kondisi_alat ENUM('baik', 'rusak', 'terlambat') NOT NULL DEFAULT 'baik'");
    }

    public function down(): void
    {
        // Kembalikan ke dua nilai semula (data 'terlambat' akan jadi '' jika ada)
        DB::statement("ALTER TABLE pengembalian MODIFY COLUMN kondisi_alat ENUM('baik', 'rusak') NOT NULL DEFAULT 'baik'");
    }
};
