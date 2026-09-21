<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PengembalianTest extends TestCase
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

    /**
     * Buat peminjaman aktif dengan detail alat, siap dikembalikan.
     * Mengembalikan array [peminjaman, alat, peminjamUser].
     */
    private function buatPeminjamanAktif(string $status = 'dipinjam', int $stokAwal = 5): array
    {
        $peminjamUser = $this->peminjam();
        $alat         = Alat::factory()->create(['jumlah' => $stokAwal]);
        $peminjaman   = Peminjaman::factory()->create([
            'user_id' => $peminjamUser->id,
            'status'  => $status,
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id'       => $alat->id,
            'jumlah_pinjam' => 2,
        ]);

        return [$peminjaman, $alat, $peminjamUser];
    }

    private function payloadKembalikan(int $peminjamanId, string $kondisi = 'baik'): array
    {
        return [
            'peminjaman_id'   => $peminjamanId,
            'tanggal_kembali' => now()->toDateString(),
            'kondisi_alat'    => $kondisi,
            'keterangan'      => null,
            'denda'           => 0,
        ];
    }

    // ─── Index ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_lihat_semua_pengembalian(): void
    {
        Pengembalian::factory()->count(3)->create();

        $response = $this->actingAs($this->admin())->getJson('/api/pengembalian');

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonCount(3, 'data');
    }

    public function test_guest_tidak_bisa_akses_pengembalian(): void
    {
        $this->getJson('/api/pengembalian')->assertStatus(401);
    }

    // ─── Store: kondisi baik ──────────────────────────────────────────────

    public function test_admin_dapat_catat_pengembalian_kondisi_baik(): void
    {
        [$peminjaman, $alat] = $this->buatPeminjamanAktif('dipinjam', 5);

        $response = $this->actingAs($this->admin())
                         ->postJson('/api/pengembalian', $this->payloadKembalikan($peminjaman->id, 'baik'));

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.kondisi_alat', 'baik');

        // Status peminjaman berubah jadi dikembalikan
        $this->assertEquals('dikembalikan', $peminjaman->fresh()->status);

        // Stok bertambah kembali
        $this->assertEquals(7, $alat->fresh()->jumlah);
    }

    // ─── Store: kondisi rusak ─────────────────────────────────────────────

    public function test_pengembalian_kondisi_rusak_update_kondisi_alat(): void
    {
        [$peminjaman, $alat] = $this->buatPeminjamanAktif('dipinjam', 5);

        $payload              = $this->payloadKembalikan($peminjaman->id, 'rusak');
        $payload['denda']     = 200000;
        $payload['keterangan']= 'Layar retak';

        $response = $this->actingAs($this->admin())
                         ->postJson('/api/pengembalian', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.kondisi_alat', 'rusak');

        // Kondisi alat berubah jadi rusak
        $this->assertEquals('rusak', $alat->fresh()->kondisi);

        // Stok tetap bertambah meski rusak
        $this->assertEquals(7, $alat->fresh()->jumlah);

        // Denda tersimpan
        $this->assertEquals(200000, $response->json('data.denda'));
    }

    // ─── Store: status terlambat (bug fix) ───────────────────────────────

    public function test_pengembalian_berhasil_untuk_status_terlambat(): void
    {
        [$peminjaman, $alat] = $this->buatPeminjamanAktif('terlambat', 5);

        $response = $this->actingAs($this->admin())
                         ->postJson('/api/pengembalian', $this->payloadKembalikan($peminjaman->id, 'baik'));

        $response->assertStatus(201)
                 ->assertJsonPath('success', true);

        $this->assertEquals('dikembalikan', $peminjaman->fresh()->status);
    }

    // ─── Store: validasi status ───────────────────────────────────────────

    public function test_pengembalian_gagal_jika_status_menunggu(): void
    {
        [$peminjaman] = $this->buatPeminjamanAktif('menunggu');

        $response = $this->actingAs($this->admin())
                         ->postJson('/api/pengembalian', $this->payloadKembalikan($peminjaman->id));

        $response->assertStatus(400)
                 ->assertJsonPath('success', false);
    }

    public function test_pengembalian_gagal_jika_sudah_dikembalikan(): void
    {
        [$peminjaman] = $this->buatPeminjamanAktif('dikembalikan');

        // Sudah ada record pengembalian
        Pengembalian::create([
            'peminjaman_id'   => $peminjaman->id,
            'tanggal_kembali' => now()->toDateString(),
            'kondisi_alat'    => 'baik',
            'denda'           => 0,
        ]);

        $response = $this->actingAs($this->admin())
                         ->postJson('/api/pengembalian', $this->payloadKembalikan($peminjaman->id));

        $response->assertStatus(400)
                 ->assertJsonPath('success', false);
    }

    // ─── Store: validasi kepemilikan ─────────────────────────────────────

    public function test_peminjam_dapat_kembalikan_miliknya_sendiri(): void
    {
        [$peminjaman, , $peminjamUser] = $this->buatPeminjamanAktif('dipinjam');

        $response = $this->actingAs($peminjamUser)
                         ->postJson('/api/pengembalian', $this->payloadKembalikan($peminjaman->id));

        $response->assertStatus(201);
    }

    public function test_peminjam_tidak_bisa_kembalikan_milik_orang_lain(): void
    {
        [$peminjaman] = $this->buatPeminjamanAktif('dipinjam');
        $lain         = $this->peminjam(); // user lain

        $response = $this->actingAs($lain)
                         ->postJson('/api/pengembalian', $this->payloadKembalikan($peminjaman->id));

        $response->assertStatus(403)
                 ->assertJsonPath('success', false);
    }

    // ─── Store: validasi input ────────────────────────────────────────────

    public function test_pengembalian_gagal_tanpa_tanggal_kembali(): void
    {
        [$peminjaman] = $this->buatPeminjamanAktif('dipinjam');

        $response = $this->actingAs($this->admin())->postJson('/api/pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'kondisi_alat'  => 'baik',
        ]);

        $response->assertStatus(422);
    }

    public function test_pengembalian_gagal_kondisi_tidak_valid(): void
    {
        [$peminjaman] = $this->buatPeminjamanAktif('dipinjam');

        $response = $this->actingAs($this->admin())->postJson('/api/pengembalian', [
            'peminjaman_id'   => $peminjaman->id,
            'tanggal_kembali' => now()->toDateString(),
            'kondisi_alat'    => 'hancur', // bukan baik/rusak
        ]);

        $response->assertStatus(422);
    }

    // ─── Show ────────────────────────────────────────────────────────────────

    public function test_admin_dapat_lihat_detail_pengembalian(): void
    {
        $pengembalian = Pengembalian::factory()->create();

        $response = $this->actingAs($this->admin())->getJson("/api/pengembalian/{$pengembalian->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.id', $pengembalian->id);
    }

    // ─── Update ──────────────────────────────────────────────────────────────

    public function test_admin_dapat_update_pengembalian(): void
    {
        $pengembalian = Pengembalian::factory()->create(['denda' => 0]);

        $response = $this->actingAs($this->admin())
                         ->putJson("/api/pengembalian/{$pengembalian->id}", [
                             'kondisi_alat' => 'rusak',
                             'denda'        => 150000,
                             'keterangan'   => 'Ada goresan',
                         ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.kondisi_alat', 'rusak');

        $this->assertEquals(150000, (float) $response->json('data.denda'));
    }

    public function test_petugas_dapat_update_pengembalian(): void
    {
        $pengembalian = Pengembalian::factory()->create();

        $response = $this->actingAs($this->petugas())
                         ->putJson("/api/pengembalian/{$pengembalian->id}", [
                             'keterangan' => 'Dicek ulang',
                         ]);

        $response->assertStatus(200);
    }

    public function test_peminjam_tidak_bisa_update_pengembalian(): void
    {
        $pengembalian = Pengembalian::factory()->create();

        $response = $this->actingAs($this->peminjam())
                         ->putJson("/api/pengembalian/{$pengembalian->id}", [
                             'denda' => 0,
                         ]);

        $response->assertStatus(403);
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function test_admin_dapat_hapus_pengembalian(): void
    {
        $pengembalian = Pengembalian::factory()->create();

        $response = $this->actingAs($this->admin())
                         ->deleteJson("/api/pengembalian/{$pengembalian->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('pengembalian', ['id' => $pengembalian->id]);
    }

    public function test_peminjam_tidak_bisa_hapus_pengembalian(): void
    {
        $pengembalian = Pengembalian::factory()->create();

        $response = $this->actingAs($this->peminjam())
                         ->deleteJson("/api/pengembalian/{$pengembalian->id}");

        $response->assertStatus(403);
    }
}
