<?php

namespace Database\Factories;

use App\Models\Alat;
use App\Models\KategoriAlat;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlatFactory extends Factory
{
    protected $model = Alat::class;

    public function definition(): array
    {
        return [
            'kategori_id' => KategoriAlat::factory(),
            'nama_alat'   => $this->faker->words(2, true),
            'merk'        => $this->faker->company(),
            'jumlah'      => $this->faker->numberBetween(1, 20),
            'deskripsi'   => $this->faker->sentence(),
            'kondisi'     => 'baik',
            'foto'        => null,
        ];
    }
}
