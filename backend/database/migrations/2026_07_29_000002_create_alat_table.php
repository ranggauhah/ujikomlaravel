<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori_alat')->onDelete('cascade');
            $table->string('nama_alat');
            $table->string('merk')->nullable();
            $table->integer('jumlah')->default(0);
            $table->text('deskripsi')->nullable();
            $table->enum('kondisi', ['baik', 'rusak', 'dalam_perbaikan'])->default('baik');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
