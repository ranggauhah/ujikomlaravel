<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\Peminjaman;
use App\Models\User;

/**
 * NotifikasiService
 * Satu tempat untuk membuat semua notifikasi in-app.
 * Dipanggil dari controller dan artisan command.
 */
class NotifikasiService
{
    /* ──────────────────────────────────────────────────────────────────
     |  PRIVATE HELPER
     | ────────────────────────────────────────────────────────────────*/

    /**
     * Buat satu notifikasi. Cek duplikat terlebih dahulu.
     * Duplikat = notifikasi dengan user_id + peminjaman_id + tipe yang sama
     * sudah ada dan dibuat dalam 24 jam terakhir.
     */
    private function buat(
        int    $userId,
        string $judul,
        string $pesan,
        string $tipe,
        ?int   $peminjamanId = null
    ): ?Notifikasi {
        // Cegah duplikat dalam 24 jam
        if ($peminjamanId) {
            $sudahAda = Notifikasi::where('user_id',       $userId)
                ->where('peminjaman_id', $peminjamanId)
                ->where('tipe',          $tipe)
                ->where('created_at',    '>=', now()->subDay())
                ->exists();

            if ($sudahAda) {
                return null; // jangan buat dobel
            }
        }

        return Notifikasi::create([
            'user_id'       => $userId,
            'judul'         => $judul,
            'pesan'         => $pesan,
            'tipe'          => $tipe,
            'peminjaman_id' => $peminjamanId,
            'dibaca'        => false,
        ]);
    }

    /* ──────────────────────────────────────────────────────────────────
     |  PUBLIC METHODS — dipanggil dari controller / command
     | ────────────────────────────────────────────────────────────────*/

    /**
     * Peminjam mengajukan peminjaman baru.
     * → Kirim notifikasi ke SEMUA admin dan petugas.
     */
    public function pengajuanBaru(Peminjaman $peminjaman): void
    {
        $peminjam  = $peminjaman->user;
        $namaAlat  = $peminjaman->detailPinjam
            ->map(fn($d) => $d->alat?->nama_alat ?? '-')
            ->join(', ');

        // Ambil semua admin dan petugas
        $penerima = User::whereIn('role', ['admin', 'petugas'])->get();

        foreach ($penerima as $user) {
            $this->buat(
                userId:      $user->id,
                judul:       'Pengajuan Peminjaman Baru',
                pesan:       "{$peminjam->name} mengajukan peminjaman alat: {$namaAlat} "
                             . "(#{$peminjaman->id}). Silakan tinjau dan setujui.",
                tipe:        'pengajuan',
                peminjamanId: $peminjaman->id
            );
        }
    }

    /**
     * Admin/Petugas menyetujui peminjaman.
     * → Kirim notifikasi ke peminjam yang bersangkutan.
     */
    public function disetujui(Peminjaman $peminjaman): void
    {
        $namaAlat = $peminjaman->detailPinjam
            ->map(fn($d) => $d->alat?->nama_alat ?? '-')
            ->join(', ');

        $tglKembali = $peminjaman->tanggal_kembali
            ? $peminjaman->tanggal_kembali->format('d/m/Y')
            : '-';

        $this->buat(
            userId:      $peminjaman->user_id,
            judul:       'Peminjaman Disetujui ✓',
            pesan:       "Pengajuan peminjaman #{$peminjaman->id} untuk alat \"{$namaAlat}\" "
                         . "telah disetujui. Batas pengembalian: {$tglKembali}.",
            tipe:        'disetujui',
            peminjamanId: $peminjaman->id
        );
    }

    /**
     * Admin/Petugas menolak peminjaman.
     * → Kirim notifikasi ke peminjam yang bersangkutan.
     */
    public function ditolak(Peminjaman $peminjaman): void
    {
        $namaAlat = $peminjaman->detailPinjam
            ->map(fn($d) => $d->alat?->nama_alat ?? '-')
            ->join(', ');

        $this->buat(
            userId:      $peminjaman->user_id,
            judul:       'Pengajuan Peminjaman Ditolak',
            pesan:       "Pengajuan peminjaman #{$peminjaman->id} untuk alat \"{$namaAlat}\" "
                         . "ditolak. Silakan hubungi petugas untuk informasi lebih lanjut.",
            tipe:        'ditolak',
            peminjamanId: $peminjaman->id
        );
    }

    /**
     * H-1 sebelum batas pengembalian.
     * → Kirim pengingat ke peminjam.
     * Dipanggil oleh Artisan command.
     */
    public function pengingatJatuhTempo(Peminjaman $peminjaman): void
    {
        $namaAlat = $peminjaman->detailPinjam
            ->map(fn($d) => $d->alat?->nama_alat ?? '-')
            ->join(', ');

        $tglKembali = $peminjaman->tanggal_kembali
            ? $peminjaman->tanggal_kembali->format('d/m/Y')
            : '-';

        $this->buat(
            userId:      $peminjaman->user_id,
            judul:       '⏰ Pengingat: Jatuh Tempo Besok',
            pesan:       "Peminjaman #{$peminjaman->id} untuk alat \"{$namaAlat}\" "
                         . "harus dikembalikan besok ({$tglKembali}). "
                         . "Segera kembalikan tepat waktu.",
            tipe:        'jatuh_tempo',
            peminjamanId: $peminjaman->id
        );
    }

    /**
     * Peminjaman sudah melewati batas dan belum dikembalikan.
     * → Kirim notifikasi ke peminjam, semua admin, dan petugas.
     * Dipanggil oleh Artisan command.
     */
    public function terlambat(Peminjaman $peminjaman): void
    {
        $namaAlat = $peminjaman->detailPinjam
            ->map(fn($d) => $d->alat?->nama_alat ?? '-')
            ->join(', ');

        $tglKembali = $peminjaman->tanggal_kembali
            ? $peminjaman->tanggal_kembali->format('d/m/Y')
            : '-';

        $hariTerlambat = $peminjaman->tanggal_kembali
            ? now()->startOfDay()->diffInDays($peminjaman->tanggal_kembali->startOfDay())
            : '?';

        // Kirim ke peminjam
        $this->buat(
            userId:      $peminjaman->user_id,
            judul:       '⚠️ Terlambat Mengembalikan Alat!',
            pesan:       "Peminjaman #{$peminjaman->id} untuk alat \"{$namaAlat}\" "
                         . "sudah terlambat {$hariTerlambat} hari (batas: {$tglKembali}). "
                         . "Segera kembalikan dan konfirmasi dengan petugas.",
            tipe:        'terlambat',
            peminjamanId: $peminjaman->id
        );

        // Kirim ke admin & petugas sebagai peringatan
        $staf = User::whereIn('role', ['admin', 'petugas'])->get();
        $namaPeminjam = $peminjaman->user?->name ?? 'Peminjam';

        foreach ($staf as $user) {
            $this->buat(
                userId:      $user->id,
                judul:       '⚠️ Peminjaman Terlambat Dikembalikan',
                pesan:       "{$namaPeminjam} (#{$peminjaman->id}) belum mengembalikan "
                             . "\"{$namaAlat}\" — terlambat {$hariTerlambat} hari "
                             . "dari batas {$tglKembali}.",
                tipe:        'terlambat',
                peminjamanId: $peminjaman->id
            );
        }
    }
}
