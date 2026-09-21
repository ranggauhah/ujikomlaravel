<?php

namespace Database\Factories;

use App\Models\KategoriAlat;
use Illuminate\Database\Eloquent\Factories\Factory;

class KategoriAlatFactory extends Factory
{
    protected $model = KategoriAlat::class;

    public function definition(): array
    {
        return [
            'nama_kategori' => $this->faker->unique()->words(2, true),
            'deskripsi'     => $this->faker->sentence(),
        ];
    }
}
