<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function petugas(): User
    {
        return User::factory()->create(['role' => 'petugas']);
    }

    private function peminjam(): User
    {
        return User::factory()->create(['role' => 'peminjam']);
    }

    // ─── Laporan Peminjaman ───────────────────────────────────────────────────

    public function test_admin_dapat_lihat_laporan_peminjaman(): void
    {
        $peminjam = $this->peminjam();
        Peminjaman::factory()->count(2)->create([
            'user_id'        => $peminjam->id,
            'tanggal_pinjam' => '2026-01-10',
        ]);

        $response = $this->actingAs($this->admin())->getJson('/api/laporan/peminjaman?' . http_build_query([
            'tanggal_mulai'   => '2026-01-01',
            'tanggal_selesai' => '2026-01-31',
        ]));

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonStructure([
                     'data' => ['periode', 'summary', 'peminjaman'],
                 ]);

        $this->assertEquals(2, $response->json('data.summary.total_peminjaman'));
    }

    public function test_laporan_peminjaman_summary_hitung_status_dengan_benar(): void
    {
        $peminjam = $this->peminjam();
        Peminjaman::factory()->create(['user_id' => $peminjam->id, 'tanggal_pinjam' => '2026-02-05', 'status' => 'dipinjam']);
        Peminjaman::factory()->create(['user_id' => $peminjam->id, 'tanggal_pinjam' => '2026-02-06', 'status' => 'dikembalikan']);
        Peminjaman::factory()->create(['user_id' => $peminjam->id, 'tanggal_pinjam' => '2026-02-07', 'status' => 'terlambat']);

        $response = $this->actingAs($this->admin())->getJson('/api/laporan/peminjaman?' . http_build_query([
            'tanggal_mulai'   => '2026-02-01',
            'tanggal_selesai' => '2026-02-28',
        ]));

        $response->assertStatus(200);
        $summary = $response->json('data.summary');
        $this->assertEquals(3, $summary['total_peminjaman']);
        $this->assertEquals(1, $summary['status_dipinjam']);
        $this->assertEquals(1, $summary['status_dikembalikan']);
        $this->assertEquals(1, $summary['status_terlambat']);
    }

    public function test_laporan_peminjaman_gagal_tanpa_tanggal(): void
    {
        $response = $this->actingAs($this->admin())->getJson('/api/laporan/peminjaman');
        $response->assertStatus(422);
    }

    public function test_laporan_peminjaman_gagal_tanggal_selesai_sebelum_mulai(): void
    {
        $response = $this->actingAs($this->admin())->getJson('/api/laporan/peminjaman?' . http_build_query([
            'tanggal_mulai'   => '2026-05-31',
            'tanggal_selesai' => '2026-05-01',
        ]));
        $response->assertStatus(422);
    }

    public function test_petugas_dapat_akses_laporan_peminjaman(): void
    {
        $response = $this->actingAs($this->petugas())->getJson('/api/laporan/peminjaman?' . http_build_query([
            'tanggal_mulai'   => '2026-01-01',
            'tanggal_selesai' => '2026-12-31',
        ]));
        $response->assertStatus(200);
    }

    public function test_peminjam_tidak_bisa_akses_laporan(): void
    {
        $response = $this->actingAs($this->peminjam())->getJson('/api/laporan/peminjaman?' . http_build_query([
            'tanggal_mulai'   => '2026-01-01',
            'tanggal_selesai' => '2026-12-31',
        ]));
        $response->assertStatus(403);
    }

    // ─── Laporan Pengembalian ─────────────────────────────────────────────────

    public function test_admin_dapat_lihat_laporan_pengembalian(): void
    {
        Pengembalian::factory()->count(2)->create([
            'tanggal_kembali' => '2026-03-15',
            'kondisi_alat'    => 'baik',
            'denda'           => 0,
        ]);
        Pengembalian::factory()->rusak()->create([
            'tanggal_kembali' => '2026-03-18',
            'denda'           => 100000,
        ]);

        $response = $this->actingAs($this->admin())->getJson('/api/laporan/pengembalian?' . http_build_query([
            'tanggal_mulai'   => '2026-03-01',
            'tanggal_selesai' => '2026-03-31',
        ]));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => ['periode', 'summary', 'pengembalian'],
                 ]);

        $summary = $response->json('data.summary');
        $this->assertEquals(3, $summary['total_pengembalian']);
        $this->assertEquals(2, $summary['kondisi_baik']);
        $this->assertEquals(1, $summary['kondisi_rusak']);
        $this->assertEquals(100000, $summary['total_denda']);
    }

    // ─── Laporan Stok Alat ────────────────────────────────────────────────────

    public function test_admin_dapat_lihat_laporan_stok_alat(): void
    {
        $kategori = KategoriAlat::factory()->create();
        Alat::factory()->count(3)->create(['kategori_id' => $kategori->id, 'kondisi' => 'baik']);
        Alat::factory()->create(['kategori_id' => $kategori->id, 'kondisi' => 'rusak']);

        $response = $this->actingAs($this->admin())->getJson('/api/laporan/stok-alat');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => ['summary', 'alat'],
                 ]);

        $summary = $response->json('data.summary');
        $this->assertEquals(4, $summary['total_jenis_alat']);
        $this->assertEquals(1, $summary['alat_rusak']);
    }

    public function test_petugas_dapat_lihat_laporan_stok_alat(): void
    {
        $response = $this->actingAs($this->petugas())->getJson('/api/laporan/stok-alat');
        $response->assertStatus(200);
    }

    // ─── Laporan User ─────────────────────────────────────────────────────────

    public function test_admin_dapat_lihat_laporan_user(): void
    {
        User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'petugas']);
        User::factory()->count(3)->create(['role' => 'peminjam']);

        $response = $this->actingAs($this->admin())->getJson('/api/laporan/user');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => ['summary', 'users'],
                 ]);

        $summary = $response->json('data.summary');
        // total termasuk admin yang actingAs
        $this->assertGreaterThanOrEqual(5, $summary['total_user']);
    }

    // ─── Log Aktivitas ────────────────────────────────────────────────────────

    public function test_admin_dapat_lihat_log_aktivitas(): void
    {
        $response = $this->actingAs($this->admin())->getJson('/api/laporan/log-aktivitas');
        $response->assertStatus(200)
                 ->assertJsonPath('success', true);
    }

    public function test_log_aktivitas_filter_by_aksi(): void
    {
        $response = $this->actingAs($this->admin())->getJson('/api/laporan/log-aktivitas?aksi=create');
        $response->assertStatus(200);
    }

    public function test_log_aktivitas_gagal_aksi_tidak_valid(): void
    {
        $response = $this->actingAs($this->admin())->getJson('/api/laporan/log-aktivitas?aksi=hapus');
        $response->assertStatus(422);
    }

    public function test_peminjam_tidak_bisa_akses_log_aktivitas(): void
    {
        $response = $this->actingAs($this->peminjam())->getJson('/api/laporan/log-aktivitas');
        $response->assertStatus(403);
    }
}
