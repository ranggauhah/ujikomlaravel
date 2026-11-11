<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            // 'menunggu_pengecekan' = peminjam sudah serahkan, petugas belum verifikasi
            // 'selesai'             = petugas sudah verifikasi kondisi + finalize stok
            $table->enum('status_pengecekan', ['menunggu_pengecekan', 'selesai'])
                  ->default('menunggu_pengecekan')
                  ->after('peminjaman_id');
        });
    }

    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropColumn('status_pengecekan');
        });
    }
};
