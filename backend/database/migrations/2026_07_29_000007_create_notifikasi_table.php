<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel notifikasi untuk menyimpan semua notifikasi in-app.
     */
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();

            // Penerima notifikasi
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('judul');
            $table->text('pesan');

            // Tipe: pengajuan | disetujui | ditolak | jatuh_tempo | terlambat
            $table->enum('tipe', [
                'pengajuan',
                'disetujui',
                'ditolak',
                'jatuh_tempo',
                'terlambat',
            ]);

            // Relasi opsional ke peminjaman terkait
            $table->foreignId('peminjaman_id')
                  ->nullable()
                  ->constrained('peminjaman')
                  ->onDelete('cascade');

            // Status baca
            $table->boolean('dibaca')->default(false);

            $table->timestamps();

            // Index untuk query cepat (unread per user)
            $table->index(['user_id', 'dibaca']);
            $table->index(['peminjaman_id', 'tipe']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
