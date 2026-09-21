<?php

namespace Database\Factories;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Database\Eloquent\Factories\Factory;

class PengembalianFactory extends Factory
{
    protected $model = Pengembalian::class;

    public function definition(): array
    {
        return [
            'peminjaman_id'  => Peminjaman::factory()->dipinjam(),
            'tanggal_kembali'=> now()->toDateString(),
            'kondisi_alat'   => 'baik',
            'keterangan'     => null,
            'denda'          => 0,
        ];
    }

    public function rusak(): static
    {
        return $this->state([
            'kondisi_alat' => 'rusak',
            'denda'        => $this->faker->numberBetween(50000, 500000),
            'keterangan'   => 'Alat mengalami kerusakan',
        ]);
    }
}
