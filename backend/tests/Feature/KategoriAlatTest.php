<?php

namespace Tests\Feature;

use App\Models\KategoriAlat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KategoriAlatTest extends TestCase
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

    // ─── Index ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_daftar_kategori(): void
    {
        KategoriAlat::factory()->count(3)->create();

        $response = $this->actingAs($this->admin())->getJson('/api/kategori-alat');

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonCount(3, 'data');
    }

    public function test_peminjam_tidak_bisa_akses_kategori(): void
    {
        $response = $this->actingAs($this->peminjam())->getJson('/api/kategori-alat');

        $response->assertStatus(403);
    }

    public function test_guest_tidak_bisa_akses_kategori(): void
    {
        $response = $this->getJson('/api/kategori-alat');

        $response->assertStatus(401);
    }

    // ─── Store ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_tambah_kategori(): void
    {
        $response = $this->actingAs($this->admin())->postJson('/api/kategori-alat', [
            'nama_kategori' => 'Elektronik',
            'deskripsi'     => 'Peralatan elektronik',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.nama_kategori', 'Elektronik');

        $this->assertDatabaseHas('kategori_alat', ['nama_kategori' => 'Elektronik']);
    }

    public function test_store_gagal_nama_kategori_kosong(): void
    {
        $response = $this->actingAs($this->admin())->postJson('/api/kategori-alat', [
            'nama_kategori' => '',
        ]);

        $response->assertStatus(422);
    }

    public function test_petugas_tidak_bisa_tambah_kategori(): void
    {
        $response = $this->actingAs($this->petugas())->postJson('/api/kategori-alat', [
            'nama_kategori' => 'Coba',
        ]);

        $response->assertStatus(403);
    }

    // ─── Show ────────────────────────────────────────────────────────────────

    public function test_admin_dapat_lihat_detail_kategori(): void
    {
        $kategori = KategoriAlat::factory()->create(['nama_kategori' => 'Mekanik']);

        $response = $this->actingAs($this->admin())->getJson("/api/kategori-alat/{$kategori->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.nama_kategori', 'Mekanik');
    }

    // ─── Update ──────────────────────────────────────────────────────────────

    public function test_admin_dapat_update_kategori(): void
    {
        $kategori = KategoriAlat::factory()->create(['nama_kategori' => 'Lama']);

        $response = $this->actingAs($this->admin())->putJson("/api/kategori-alat/{$kategori->id}", [
            'nama_kategori' => 'Baru',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.nama_kategori', 'Baru');

        $this->assertDatabaseHas('kategori_alat', ['nama_kategori' => 'Baru']);
    }

    public function test_petugas_tidak_bisa_update_kategori(): void
    {
        $kategori = KategoriAlat::factory()->create();

        $response = $this->actingAs($this->petugas())->putJson("/api/kategori-alat/{$kategori->id}", [
            'nama_kategori' => 'Diubah',
        ]);

        $response->assertStatus(403);
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function test_admin_dapat_hapus_kategori(): void
    {
        $kategori = KategoriAlat::factory()->create();

        $response = $this->actingAs($this->admin())->deleteJson("/api/kategori-alat/{$kategori->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('kategori_alat', ['id' => $kategori->id]);
    }

    public function test_peminjam_tidak_bisa_hapus_kategori(): void
    {
        $kategori = KategoriAlat::factory()->create();

        $response = $this->actingAs($this->peminjam())->deleteJson("/api/kategori-alat/{$kategori->id}");

        $response->assertStatus(403);
    }
}
