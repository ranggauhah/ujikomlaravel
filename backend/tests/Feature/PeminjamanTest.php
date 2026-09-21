<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\KategoriAlat;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PeminjamanTest extends TestCase
{
    use DatabaseMigrations;

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

    /** Buat alat dengan stok yang cukup dan kembalikan data alat */
    private function buatAlat(int $jumlah = 10): Alat
    {
        return Alat::factory()->create(['jumlah' => $jumlah]);
    }

    /** Payload valid untuk store peminjaman */
    private function payloadStore(int $alatId, int $jumlah = 1): array
    {
        return [
            'tanggal_pinjam' => now()->toDateString(),
            'keperluan'      => 'Keperluan praktikum',
            'detail'         => [
                ['alat_id' => $alatId, 'jumlah_pinjam' => $jumlah],
            ],
        ];
    }

    // ─── Index ───────────────────────────────────────────────────────────────

    public function test_admin_melihat_semua_peminjaman(): void
    {
        $peminjam = $this->peminjam();
        Peminjaman::factory()->count(3)->create(['user_id' => $peminjam->id]);

        $response = $this->actingAs($this->admin())->getJson('/api/peminjaman');

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonCount(3, 'data');
    }

    public function test_peminjam_hanya_melihat_peminjaman_miliknya(): void
    {
        $peminjam1 = $this->peminjam();
        $peminjam2 = $this->peminjam();

        Peminjaman::factory()->count(2)->create(['user_id' => $peminjam1->id]);
        Peminjaman::factory()->count(3)->create(['user_id' => $peminjam2->id]);

        $response = $this->actingAs($peminjam1)->getJson('/api/peminjaman');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
    }

    public function test_guest_tidak_bisa_lihat_peminjaman(): void
    {
        $this->getJson('/api/peminjaman')->assertStatus(401);
    }

    // ─── Store ───────────────────────────────────────────────────────────────

    public function test_peminjam_dapat_ajukan_peminjaman(): void
    {
        $peminjam = $this->peminjam();
        $alat     = $this->buatAlat(10);

        $response = $this->actingAs($peminjam)->postJson('/api/peminjaman', $this->payloadStore($alat->id, 2));

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.status', 'menunggu');

        // Stok BELUM berkurang saat masih menunggu
        $this->assertEquals(10, $alat->fresh()->jumlah);
        $this->assertDatabaseHas('peminjaman', ['user_id' => $peminjam->id, 'status' => 'menunggu']);
    }

    public function test_store_gagal_jika_stok_tidak_cukup(): void
    {
        $peminjam = $this->peminjam();
        $alat     = $this->buatAlat(1);

        $response = $this->actingAs($peminjam)->postJson('/api/peminjaman', $this->payloadStore($alat->id, 5));

        $response->assertStatus(400)
                 ->assertJsonPath('success', false);
    }

    public function test_admin_tidak_bisa_ajukan_peminjaman(): void
    {
        $alat     = $this->buatAlat();
        $response = $this->actingAs($this->admin())->postJson('/api/peminjaman', $this->payloadStore($alat->id));
        $response->assertStatus(403);
    }

    public function test_petugas_tidak_bisa_ajukan_peminjaman(): void
    {
        $alat     = $this->buatAlat();
        $response = $this->actingAs($this->petugas())->postJson('/api/peminjaman', $this->payloadStore($alat->id));
        $response->assertStatus(403);
    }

    public function test_store_gagal_tanpa_detail_alat(): void
    {
        $peminjam = $this->peminjam();
        $response = $this->actingAs($peminjam)->postJson('/api/peminjaman', [
            'tanggal_pinjam' => now()->toDateString(),
            'keperluan'      => 'Test',
        ]);
        $response->assertStatus(422);
    }

    // ─── Show ────────────────────────────────────────────────────────────────

    public function test_admin_dapat_lihat_detail_peminjaman(): void
    {
        $p        = Peminjaman::factory()->create();
        $response = $this->actingAs($this->admin())->getJson("/api/peminjaman/{$p->id}");
        $response->assertStatus(200)
                 ->assertJsonPath('data.id', $p->id);
    }

    // ─── Approve ─────────────────────────────────────────────────────────────

    public function test_admin_dapat_approve_peminjaman(): void
    {
        $peminjam   = $this->peminjam();
        $alat       = $this->buatAlat(10);
        $peminjaman = Peminjaman::factory()->create([
            'user_id' => $peminjam->id,
            'status'  => 'menunggu',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id'       => $alat->id,
            'jumlah_pinjam' => 3,
        ]);

        $response = $this->actingAs($this->admin())
                         ->patchJson("/api/peminjaman/{$peminjaman->id}/approve");

        $response->assertStatus(200)
                 ->assertJsonPath('data.status', 'dipinjam');

        // Stok berkurang saat diapprove
        $this->assertEquals(7, $alat->fresh()->jumlah);
    }

    public function test_petugas_dapat_approve_peminjaman(): void
    {
        $alat       = $this->buatAlat(5);
        $peminjaman = Peminjaman::factory()->create(['status' => 'menunggu']);
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id'       => $alat->id,
            'jumlah_pinjam' => 1,
        ]);

        $response = $this->actingAs($this->petugas())
                         ->patchJson("/api/peminjaman/{$peminjaman->id}/approve");

        $response->assertStatus(200);
    }

    public function test_approve_gagal_jika_status_bukan_menunggu(): void
    {
        $peminjaman = Peminjaman::factory()->dipinjam()->create();

        $response = $this->actingAs($this->admin())
                         ->patchJson("/api/peminjaman/{$peminjaman->id}/approve");

        $response->assertStatus(400);
    }

    public function test_approve_gagal_jika_stok_habis_saat_approve(): void
    {
        $alat       = $this->buatAlat(1);
        $peminjaman = Peminjaman::factory()->create(['status' => 'menunggu']);
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id'       => $alat->id,
            'jumlah_pinjam' => 5, // lebih dari stok
        ]);

        $response = $this->actingAs($this->admin())
                         ->patchJson("/api/peminjaman/{$peminjaman->id}/approve");

        $response->assertStatus(400);
    }

    public function test_peminjam_tidak_bisa_approve(): void
    {
        $peminjaman = Peminjaman::factory()->create(['status' => 'menunggu']);

        $response = $this->actingAs($this->peminjam())
                         ->patchJson("/api/peminjaman/{$peminjaman->id}/approve");

        $response->assertStatus(403);
    }

    // ─── Tolak ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_tolak_peminjaman(): void
    {
        $peminjaman = Peminjaman::factory()->create(['status' => 'menunggu']);

        $response = $this->actingAs($this->admin())
                         ->patchJson("/api/peminjaman/{$peminjaman->id}/tolak");

        $response->assertStatus(200)
                 ->assertJsonPath('data.status', 'ditolak');
    }

    public function test_petugas_dapat_tolak_peminjaman(): void
    {
        $peminjaman = Peminjaman::factory()->create(['status' => 'menunggu']);

        $response = $this->actingAs($this->petugas())
                         ->patchJson("/api/peminjaman/{$peminjaman->id}/tolak");

        $response->assertStatus(200);
    }

    public function test_tolak_gagal_jika_status_bukan_menunggu(): void
    {
        $peminjaman = Peminjaman::factory()->dipinjam()->create();

        $response = $this->actingAs($this->admin())
                         ->patchJson("/api/peminjaman/{$peminjaman->id}/tolak");

        $response->assertStatus(400);
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function test_admin_dapat_hapus_peminjaman(): void
    {
        $alat       = $this->buatAlat(10);
        $peminjaman = Peminjaman::factory()->dipinjam()->create();
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id'       => $alat->id,
            'jumlah_pinjam' => 3,
        ]);

        $response = $this->actingAs($this->admin())->deleteJson("/api/peminjaman/{$peminjaman->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('peminjaman', ['id' => $peminjaman->id]);
        // Stok dikembalikan saat dihapus
        $this->assertEquals(13, $alat->fresh()->jumlah);
    }

    public function test_peminjam_tidak_bisa_hapus_peminjaman(): void
    {
        $peminjaman = Peminjaman::factory()->create();
        $response   = $this->actingAs($this->peminjam())->deleteJson("/api/peminjaman/{$peminjaman->id}");
        $response->assertStatus(403);
    }
}
