<?php

namespace Database\Factories;

use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeminjamanFactory extends Factory
{
    protected $model = Peminjaman::class;

    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali'=> now()->addDays(7)->toDateString(),
            'status'         => 'menunggu',
            'keperluan'      => $this->faker->sentence(),
        ];
    }

    public function dipinjam(): static
    {
        return $this->state(['status' => 'dipinjam']);
    }

    public function terlambat(): static
    {
        return $this->state([
            'status'         => 'terlambat',
            'tanggal_kembali'=> now()->subDays(3)->toDateString(),
        ]);
    }

    public function dikembalikan(): static
    {
        return $this->state(['status' => 'dikembalikan']);
    }
}
