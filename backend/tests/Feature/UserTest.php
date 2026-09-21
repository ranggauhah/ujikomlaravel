<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserTest extends TestCase
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

    // ─── Index ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_semua_user(): void
    {
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->admin())->getJson('/api/users');

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        // Minimal ada 4 user (3 factory + 1 admin)
        $this->assertGreaterThanOrEqual(4, count($response->json('data')));
    }

    public function test_petugas_tidak_bisa_lihat_daftar_user(): void
    {
        $response = $this->actingAs($this->petugas())->getJson('/api/users');
        $response->assertStatus(403);
    }

    public function test_peminjam_tidak_bisa_lihat_daftar_user(): void
    {
        $response = $this->actingAs($this->peminjam())->getJson('/api/users');
        $response->assertStatus(403);
    }

    // ─── Store ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_tambah_user(): void
    {
        $response = $this->actingAs($this->admin())->postJson('/api/users', [
            'name'     => 'User Baru',
            'email'    => 'baru@example.com',
            'password' => 'password123',
            'role'     => 'petugas',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.role', 'petugas')
                 ->assertJsonPath('data.email', 'baru@example.com');

        $this->assertDatabaseHas('users', ['email' => 'baru@example.com']);
    }

    public function test_store_user_gagal_email_duplikat(): void
    {
        User::factory()->create(['email' => 'ada@example.com']);

        $response = $this->actingAs($this->admin())->postJson('/api/users', [
            'name'     => 'Lainnya',
            'email'    => 'ada@example.com',
            'password' => 'password123',
            'role'     => 'peminjam',
        ]);

        $response->assertStatus(422);
    }

    public function test_store_user_gagal_role_tidak_valid(): void
    {
        $response = $this->actingAs($this->admin())->postJson('/api/users', [
            'name'     => 'Hacker',
            'email'    => 'hack@example.com',
            'password' => 'password123',
            'role'     => 'superadmin',
        ]);

        $response->assertStatus(422);
    }

    public function test_petugas_tidak_bisa_tambah_user(): void
    {
        $response = $this->actingAs($this->petugas())->postJson('/api/users', [
            'name'     => 'Coba',
            'email'    => 'coba@example.com',
            'password' => 'password123',
            'role'     => 'peminjam',
        ]);

        $response->assertStatus(403);
    }

    // ─── Show ────────────────────────────────────────────────────────────────

    public function test_admin_dapat_lihat_detail_user(): void
    {
        $user = User::factory()->create(['name' => 'Detail User']);

        $response = $this->actingAs($this->admin())->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'Detail User');
    }

    // ─── Update ──────────────────────────────────────────────────────────────

    public function test_admin_dapat_update_user(): void
    {
        $user = User::factory()->create(['name' => 'Sebelum']);

        $response = $this->actingAs($this->admin())->putJson("/api/users/{$user->id}", [
            'name' => 'Sesudah',
            'role' => 'petugas',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'Sesudah')
                 ->assertJsonPath('data.role', 'petugas');
    }

    public function test_update_user_tidak_ubah_password_jika_tidak_diisi(): void
    {
        $user            = User::factory()->create(['password' => bcrypt('rahasia')]);
        $passwordLama    = $user->password;

        $this->actingAs($this->admin())->putJson("/api/users/{$user->id}", [
            'name' => 'Ganti Nama Saja',
        ]);

        $this->assertEquals($passwordLama, $user->fresh()->password);
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function test_admin_dapat_hapus_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin())->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_peminjam_tidak_bisa_hapus_user(): void
    {
        $target   = User::factory()->create();
        $response = $this->actingAs($this->peminjam())->deleteJson("/api/users/{$target->id}");
        $response->assertStatus(403);
    }
}
