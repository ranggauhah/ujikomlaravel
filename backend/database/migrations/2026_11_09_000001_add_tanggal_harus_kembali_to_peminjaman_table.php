<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom tanggal_harus_kembali (deadline) ke tabel peminjaman.
     * Kolom ini diisi saat peminjaman dibuat agar deadline bisa ditampilkan
     * sebelum alat dikembalikan dan bisa digunakan untuk deteksi keterlambatan.
     */
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Letakkan setelah tanggal_pinjam agar urutan kolom logis
            $table->date('tanggal_harus_kembali')->nullable()->after('tanggal_pinjam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn('tanggal_harus_kembali');
        });
    }
};
