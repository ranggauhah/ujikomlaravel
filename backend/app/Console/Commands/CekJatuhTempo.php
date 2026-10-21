<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use App\Services\NotifikasiService;
use Illuminate\Console\Command;

class CekJatuhTempo extends Command
{
    /**
     * Nama perintah artisan.
     */
    protected $signature = 'notifikasi:cek-jatuh-tempo';

    /**
     * Deskripsi singkat perintah.
     */
    protected $description = 'Cek peminjaman yang jatuh tempo besok (H-1) '
                           . 'dan yang sudah terlambat, lalu kirim notifikasi.';

    public function handle(NotifikasiService $notif): int
    {
        $hari_ini = now()->startOfDay();
        $besok    = now()->addDay()->startOfDay();

        $this->info('🔔 Mengecek jatuh tempo dan keterlambatan...');

        /* ── 1. Pengingat H-1 ──────────────────────────────────────── */
        $jatuhTempoBesok = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'dipinjam')
            ->whereDate('tanggal_kembali', $besok->toDateString())
            ->get();

        $this->info("  H-1 jatuh tempo: {$jatuhTempoBesok->count()} peminjaman");

        foreach ($jatuhTempoBesok as $p) {
            $notif->pengingatJatuhTempo($p);
            $this->line("   ✓ Pengingat dikirim → #{$p->id} ({$p->user?->name})");
        }

        /* ── 2. Notifikasi terlambat ───────────────────────────────── */
        $terlambat = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'dipinjam')
            ->whereDate('tanggal_kembali', '<', $hari_ini->toDateString())
            ->get();

        $this->info("  Terlambat: {$terlambat->count()} peminjaman");

        foreach ($terlambat as $p) {
            // Update status jadi terlambat kalau belum
            if ($p->status !== 'terlambat') {
                $p->update(['status' => 'terlambat']);
            }

            $notif->terlambat($p);
            $this->line("   ⚠ Terlambat dikirim → #{$p->id} ({$p->user?->name})");
        }

        $total = $jatuhTempoBesok->count() + $terlambat->count();
        $this->info("✅ Selesai. Total {$total} notifikasi diproses.");

        return Command::SUCCESS;
    }
}
