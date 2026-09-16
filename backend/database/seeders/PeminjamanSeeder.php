<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use App\Models\Pengembalian;
use App\Models\User;
use App\Models\Alat;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        $peminjam = User::where('role', 'peminjam')->first();
        $admin    = User::where('role', 'admin')->first();
        $petugas  = User::where('role', 'petugas')->first();
        $alat     = Alat::all();

        if ($alat->isEmpty()) {
            $this->command->warn('No alat found, skipping peminjaman seed.');
            return;
        }

        // --- Peminjaman 1: sudah dikembalikan ---
        $p1 = Peminjaman::create([
            'user_id'         => $peminjam->id,
            'tanggal_pinjam'  => Carbon::now()->subDays(10)->toDateString(),
            'tanggal_kembali' => Carbon::now()->subDays(3)->toDateString(),
            'status'          => 'dikembalikan',
            'keperluan'       => 'Praktikum jaringan komputer semester 4',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $p1->id,
            'alat_id'       => $alat[0]->id,
            'jumlah_pinjam' => 2,
        ]);
        if ($alat->count() > 1) {
            DetailPinjam::create([
                'peminjaman_id' => $p1->id,
                'alat_id'       => $alat[1]->id,
                'jumlah_pinjam' => 1,
            ]);
        }
        Pengembalian::create([
            'peminjaman_id'  => $p1->id,
            'tanggal_kembali'=> Carbon::now()->subDays(3)->toDateString(),
            'kondisi_alat'   => 'baik',
            'keterangan'     => 'Dikembalikan dalam kondisi baik',
            'denda'          => 0,
        ]);

        // --- Peminjaman 2: masih dipinjam ---
        $p2 = Peminjaman::create([
            'user_id'         => $peminjam->id,
            'tanggal_pinjam'  => Carbon::now()->subDays(5)->toDateString(),
            'tanggal_kembali' => Carbon::now()->addDays(2)->toDateString(),
            'status'          => 'dipinjam',
            'keperluan'       => 'Tugas akhir - pengembangan sistem embedded',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $p2->id,
            'alat_id'       => $alat->count() > 2 ? $alat[2]->id : $alat[0]->id,
            'jumlah_pinjam' => 1,
        ]);

        // --- Peminjaman 3: petugas, sudah dikembalikan ---
        $p3 = Peminjaman::create([
            'user_id'         => $petugas->id,
            'tanggal_pinjam'  => Carbon::now()->subDays(15)->toDateString(),
            'tanggal_kembali' => Carbon::now()->subDays(8)->toDateString(),
            'status'          => 'dikembalikan',
            'keperluan'       => 'Instalasi perangkat lab baru',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $p3->id,
            'alat_id'       => $alat->count() > 3 ? $alat[3]->id : $alat[0]->id,
            'jumlah_pinjam' => 3,
        ]);
        Pengembalian::create([
            'peminjaman_id'  => $p3->id,
            'tanggal_kembali'=> Carbon::now()->subDays(8)->toDateString(),
            'kondisi_alat'   => 'baik',
            'keterangan'     => 'Semua alat kembali lengkap',
            'denda'          => 0,
        ]);

        // --- Peminjaman 4: terlambat ---
        $p4 = Peminjaman::create([
            'user_id'         => $peminjam->id,
            'tanggal_pinjam'  => Carbon::now()->subDays(20)->toDateString(),
            'tanggal_kembali' => Carbon::now()->subDays(5)->toDateString(),
            'status'          => 'terlambat',
            'keperluan'       => 'Penelitian skripsi - pengukuran sinyal',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $p4->id,
            'alat_id'       => $alat->count() > 4 ? $alat[4]->id : $alat[0]->id,
            'jumlah_pinjam' => 1,
        ]);

        // --- Peminjaman 5: baru dipinjam hari ini ---
        $p5 = Peminjaman::create([
            'user_id'         => $admin->id,
            'tanggal_pinjam'  => Carbon::now()->toDateString(),
            'tanggal_kembali' => Carbon::now()->addDays(7)->toDateString(),
            'status'          => 'dipinjam',
            'keperluan'       => 'Demo presentasi kepada tamu sekolah',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $p5->id,
            'alat_id'       => $alat[0]->id,
            'jumlah_pinjam' => 1,
        ]);

        // --- Peminjaman 6: sudah dikembalikan dengan denda ---
        $p6 = Peminjaman::create([
            'user_id'         => $peminjam->id,
            'tanggal_pinjam'  => Carbon::now()->subDays(30)->toDateString(),
            'tanggal_kembali' => Carbon::now()->subDays(18)->toDateString(),
            'status'          => 'dikembalikan',
            'keperluan'       => 'Perbaikan perangkat laboratorium',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $p6->id,
            'alat_id'       => $alat->count() > 1 ? $alat[1]->id : $alat[0]->id,
            'jumlah_pinjam' => 2,
        ]);
        Pengembalian::create([
            'peminjaman_id'  => $p6->id,
            'tanggal_kembali'=> Carbon::now()->subDays(16)->toDateString(), // 2 hari terlambat
            'kondisi_alat'   => 'rusak',
            'keterangan'     => 'Terlambat 2 hari, ada lecet kecil pada kabel',
            'denda'          => 10000,
        ]);
    }
}
