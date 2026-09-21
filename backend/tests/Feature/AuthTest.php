<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ─── Register ────────────────────────────────────────────────────────────

    public function test_user_dapat_register_berhasil(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'Budi Santoso',
            'email'                 => 'budi@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'peminjam',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonStructure([
                     'data' => ['user' => ['id', 'name', 'email', 'role'], 'token'],
                 ]);

        $this->assertDatabaseHas('users', ['email' => 'budi@example.com']);
    }

    public function test_register_default_role_peminjam(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'Ani',
            'email'                 => 'ani@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.user.role', 'peminjam');
    }

    public function test_register_gagal_jika_email_sudah_ada(): void
    {
        User::factory()->create(['email' => 'duplikat@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name'     => 'Orang Lain',
            'email'    => 'duplikat@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    public function test_register_gagal_jika_password_kurang_dari_8_karakter(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'     => 'Coba',
            'email'    => 'coba@example.com',
            'password' => '123',
        ]);

        $response->assertStatus(422);
    }

    public function test_register_gagal_jika_role_tidak_valid(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'     => 'Hacker',
            'email'    => 'hacker@example.com',
            'password' => 'password123',
            'role'     => 'superadmin',
        ]);

        $response->assertStatus(422);
    }

    // ─── Login ───────────────────────────────────────────────────────────────

    public function test_user_dapat_login_berhasil(): void
    {
        $user = User::factory()->create([
            'email'    => 'login@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'login@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonStructure([
                     'data' => ['user', 'token'],
                 ]);
    }

    public function test_login_gagal_password_salah(): void
    {
        User::factory()->create([
            'email'    => 'salah@example.com',
            'password' => bcrypt('benarnya'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'salah@example.com',
            'password' => 'salah123',
        ]);

        $response->assertStatus(401)
                 ->assertJsonPath('success', false);
    }

    public function test_login_gagal_email_tidak_terdaftar(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => 'tidakada@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
    }

    // ─── Logout ──────────────────────────────────────────────────────────────

    public function test_user_dapat_logout(): void
    {
        $user = User::factory()->create();

        // Buat token Sanctum yang nyata, bukan TransientToken dari actingAs()
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->postJson('/api/auth/logout');

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);
    }

    public function test_logout_tanpa_token_ditolak(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    // ─── Me ──────────────────────────────────────────────────────────────────

    public function test_me_mengembalikan_data_user_yang_login(): void
    {
        $user = User::factory()->create(['name' => 'Tes Saya']);

        $response = $this->actingAs($user)->getJson('/api/auth/me');

        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'Tes Saya')
                 ->assertJsonPath('data.email', $user->email);
    }

    public function test_me_tanpa_token_ditolak(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }
}
