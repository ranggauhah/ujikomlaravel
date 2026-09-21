<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AlatTest extends TestCase
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

    public function test_semua_role_dapat_melihat_daftar_alat(): void
    {
        Alat::factory()->count(3)->create();

        foreach (['admin', 'petugas', 'peminjam'] as $role) {
            $user     = User::factory()->create(['role' => $role]);
            $response = $this->actingAs($user)->getJson('/api/alat');
            $response->assertStatus(200)
                     ->assertJsonPath('success', true)
                     ->assertJsonCount(3, 'data');
        }
    }

    public function test_guest_tidak_bisa_lihat_alat(): void
    {
        $response = $this->getJson('/api/alat');
        $response->assertStatus(401);
    }

    // ─── Show ────────────────────────────────────────────────────────────────

    public function test_semua_role_dapat_lihat_detail_alat(): void
    {
        $alat = Alat::factory()->create(['nama_alat' => 'Bor Listrik']);

        $response = $this->actingAs($this->peminjam())->getJson("/api/alat/{$alat->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.nama_alat', 'Bor Listrik');
    }

    public function test_show_alat_tidak_ditemukan(): void
    {
        $response = $this->actingAs($this->admin())->getJson('/api/alat/9999');
        $response->assertStatus(404);
    }

    // ─── Store ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_tambah_alat(): void
    {
        $kategori = KategoriAlat::factory()->create();

        $response = $this->actingAs($this->admin())->postJson('/api/alat', [
            'kategori_id' => $kategori->id,
            'nama_alat'   => 'Multimeter',
            'merk'        => 'Sanwa',
            'jumlah'      => 5,
            'kondisi'     => 'baik',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.nama_alat', 'Multimeter')
                 ->assertJsonPath('data.jumlah', 5);

        $this->assertDatabaseHas('alat', ['nama_alat' => 'Multimeter']);
    }

    public function test_admin_dapat_tambah_alat_dengan_foto(): void
    {
        Storage::fake('public');
        $kategori = KategoriAlat::factory()->create();

        $response = $this->actingAs($this->admin())->postJson('/api/alat', [
            'kategori_id' => $kategori->id,
            'nama_alat'   => 'Oscilloscope',
            'merk'        => 'Rigol',
            'jumlah'      => 2,
            'kondisi'     => 'baik',
            'foto'        => UploadedFile::fake()->image('alat.jpg'),
        ]);

        $response->assertStatus(201);
        $this->assertNotNull($response->json('data.foto'));
    }

    public function test_store_alat_gagal_tanpa_field_wajib(): void
    {
        $response = $this->actingAs($this->admin())->postJson('/api/alat', []);
        $response->assertStatus(422);
    }

    public function test_petugas_tidak_bisa_tambah_alat(): void
    {
        $kategori = KategoriAlat::factory()->create();

        $response = $this->actingAs($this->petugas())->postJson('/api/alat', [
            'kategori_id' => $kategori->id,
            'nama_alat'   => 'Test',
            'merk'        => 'Test',
            'jumlah'      => 1,
            'kondisi'     => 'baik',
        ]);

        $response->assertStatus(403);
    }

    public function test_peminjam_tidak_bisa_tambah_alat(): void
    {
        $kategori = KategoriAlat::factory()->create();

        $response = $this->actingAs($this->peminjam())->postJson('/api/alat', [
            'kategori_id' => $kategori->id,
            'nama_alat'   => 'Test',
            'merk'        => 'Test',
            'jumlah'      => 1,
            'kondisi'     => 'baik',
        ]);

        $response->assertStatus(403);
    }

    // ─── Update ──────────────────────────────────────────────────────────────

    public function test_admin_dapat_update_alat(): void
    {
        $alat = Alat::factory()->create(['nama_alat' => 'Lama', 'jumlah' => 3]);

        $response = $this->actingAs($this->admin())->putJson("/api/alat/{$alat->id}", [
            'nama_alat' => 'Baru',
            'jumlah'    => 10,
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.nama_alat', 'Baru')
                 ->assertJsonPath('data.jumlah', 10);
    }

    public function test_petugas_tidak_bisa_update_alat(): void
    {
        $alat = Alat::factory()->create();

        $response = $this->actingAs($this->petugas())->putJson("/api/alat/{$alat->id}", [
            'nama_alat' => 'Diubah',
        ]);

        $response->assertStatus(403);
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function test_admin_dapat_hapus_alat(): void
    {
        $alat = Alat::factory()->create();

        $response = $this->actingAs($this->admin())->deleteJson("/api/alat/{$alat->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('alat', ['id' => $alat->id]);
    }

    public function test_peminjam_tidak_bisa_hapus_alat(): void
    {
        $alat = Alat::factory()->create();

        $response = $this->actingAs($this->peminjam())->deleteJson("/api/alat/{$alat->id}");

        $response->assertStatus(403);
    }

    public function test_hapus_alat_juga_hapus_foto_dari_storage(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('foto.jpg');
        $path = $file->store('alat', 'public');

        $alat = Alat::factory()->create(['foto' => $path]);

        $this->actingAs($this->admin())->deleteJson("/api/alat/{$alat->id}");

        Storage::disk('public')->assertMissing($path);
    }
}
