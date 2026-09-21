<?php

namespace Database\Factories;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailPinjamFactory extends Factory
{
    protected $model = DetailPinjam::class;

    public function definition(): array
    {
        return [
            'peminjaman_id' => Peminjaman::factory(),
            'alat_id'       => Alat::factory(),
            'jumlah_pinjam' => $this->faker->numberBetween(1, 3),
        ];
    }
}
