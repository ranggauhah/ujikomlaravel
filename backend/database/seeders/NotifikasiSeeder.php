<?php

namespace Database\Seeders;

use App\Models\Notifikasi;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * NotifikasiSeeder
 * Buat contoh notifikasi untuk tiap role agar fitur bisa langsung dites.
 *
 * Jalankan dengan:
 *   php artisan db:seed --class=NotifikasiSeeder
 */
class NotifikasiSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user per role (ambil yang pertama ditemukan)
        $admin    = User::where('role', 'admin')->first();
        $petugas  = User::where('role', 'petugas')->first();
        $peminjam = User::where('role', 'peminjam')->first();

        // Ambil peminjaman pertama yang ada (jika ada)
        $peminjaman = Peminjaman::with('detailPinjam.alat')->latest()->first();
        $pId = $peminjaman?->id;

        $now = now();

        $data = [];

        /* ── Notifikasi untuk Admin ──────────────────────────────── */
        if ($admin) {
            $data[] = [
                'user_id'       => $admin->id,
                'judul'         => 'Pengajuan Peminjaman Baru',
                'pesan'         => 'Ahmad Fauzi mengajukan peminjaman alat: Laptop Dell Inspiron (#7). Silakan tinjau dan setujui.',
                'tipe'          => 'pengajuan',
                'peminjaman_id' => $pId,
                'dibaca'        => false,
                'created_at'    => $now->copy()->subMinutes(5),
                'updated_at'    => $now->copy()->subMinutes(5),
            ];
            $data[] = [
                'user_id'       => $admin->id,
                'judul'         => '⚠️ Peminjaman Terlambat Dikembalikan',
                'pesan'         => 'Nur Hidayah (#17) belum mengembalikan "Bola Futsal Mikasa FL450" — terlambat 2 hari dari batas 20/09/2026.',
                'tipe'          => 'terlambat',
                'peminjaman_id' => $pId,
                'dibaca'        => false,
                'created_at'    => $now->copy()->subHours(3),
                'updated_at'    => $now->copy()->subHours(3),
            ];
            $data[] = [
                'user_id'       => $admin->id,
                'judul'         => 'Pengajuan Peminjaman Baru',
                'pesan'         => 'Angga mengajukan peminjaman alat: Kamera Canon EOS 1500D (#8). Silakan tinjau.',
                'tipe'          => 'pengajuan',
                'peminjaman_id' => $pId,
                'dibaca'        => true,
                'created_at'    => $now->copy()->subDays(1),
                'updated_at'    => $now->copy()->subDays(1),
            ];
        }

        /* ── Notifikasi untuk Petugas ────────────────────────────── */
        if ($petugas) {
            $data[] = [
                'user_id'       => $petugas->id,
                'judul'         => 'Pengajuan Peminjaman Baru',
                'pesan'         => 'Ahmad Fauzi mengajukan peminjaman alat: Laptop Dell Inspiron (#7). Silakan tinjau dan setujui.',
                'tipe'          => 'pengajuan',
                'peminjaman_id' => $pId,
                'dibaca'        => false,
                'created_at'    => $now->copy()->subMinutes(5),
                'updated_at'    => $now->copy()->subMinutes(5),
            ];
            $data[] = [
                'user_id'       => $petugas->id,
                'judul'         => '⚠️ Peminjaman Terlambat Dikembalikan',
                'pesan'         => 'Nur Hidayah (#17) belum mengembalikan "Bola Futsal Mikasa FL450" — terlambat 2 hari.',
                'tipe'          => 'terlambat',
                'peminjaman_id' => $pId,
                'dibaca'        => true,
                'created_at'    => $now->copy()->subHours(6),
                'updated_at'    => $now->copy()->subHours(6),
            ];
        }

        /* ── Notifikasi untuk Peminjam ───────────────────────────── */
        if ($peminjam) {
            $data[] = [
                'user_id'       => $peminjam->id,
                'judul'         => 'Peminjaman Disetujui ✓',
                'pesan'         => 'Pengajuan peminjaman #7 untuk alat "Laptop Dell Inspiron" telah disetujui. Batas pengembalian: 02/08/2026.',
                'tipe'          => 'disetujui',
                'peminjaman_id' => $pId,
                'dibaca'        => false,
                'created_at'    => $now->copy()->subHours(1),
                'updated_at'    => $now->copy()->subHours(1),
            ];
            $data[] = [
                'user_id'       => $peminjam->id,
                'judul'         => '⏰ Pengingat: Jatuh Tempo Besok',
                'pesan'         => 'Peminjaman #8 untuk alat "Kamera Canon EOS 1500D" harus dikembalikan besok (23/09/2026). Segera kembalikan tepat waktu.',
                'tipe'          => 'jatuh_tempo',
                'peminjaman_id' => $pId,
                'dibaca'        => false,
                'created_at'    => $now->copy()->subHours(2),
                'updated_at'    => $now->copy()->subHours(2),
            ];
            $data[] = [
                'user_id'       => $peminjam->id,
                'judul'         => 'Pengajuan Peminjaman Ditolak',
                'pesan'         => 'Pengajuan peminjaman #18 untuk alat "mose" ditolak. Silakan hubungi petugas untuk informasi lebih lanjut.',
                'tipe'          => 'ditolak',
                'peminjaman_id' => $pId,
                'dibaca'        => true,
                'created_at'    => $now->copy()->subDays(2),
                'updated_at'    => $now->copy()->subDays(2),
            ];
            $data[] = [
                'user_id'       => $peminjam->id,
                'judul'         => '⚠️ Terlambat Mengembalikan Alat!',
                'pesan'         => 'Peminjaman #15 untuk alat "mose" sudah terlambat 1 hari (batas: 16/09/2026). Segera kembalikan.',
                'tipe'          => 'terlambat',
                'peminjaman_id' => $pId,
                'dibaca'        => true,
                'created_at'    => $now->copy()->subDays(3),
                'updated_at'    => $now->copy()->subDays(3),
            ];
        }

        // Insert semua sekaligus
        Notifikasi::insert($data);

        $this->command->info('✅ NotifikasiSeeder: ' . count($data) . ' notifikasi berhasil dibuat.');
    }
}
