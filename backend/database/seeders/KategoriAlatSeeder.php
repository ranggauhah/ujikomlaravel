<?php

namespace Database\Seeders;

use App\Models\KategoriAlat;
use Illuminate\Database\Seeder;

class KategoriAlatSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            ['nama_kategori' => 'Elektronik',   'deskripsi' => 'Peralatan elektronik seperti laptop, kamera, proyektor, dan speaker'],
            ['nama_kategori' => 'Jaringan',      'deskripsi' => 'Peralatan jaringan komputer seperti router, switch, access point, dan kabel'],
            ['nama_kategori' => 'Lab Komputer',  'deskripsi' => 'Peralatan laboratorium komputer seperti mouse, keyboard, monitor, dan storage'],
            ['nama_kategori' => 'Olahraga',      'deskripsi' => 'Peralatan olahraga seperti bola, net, dan raket'],
        ];

        foreach ($kategoris as $kategori) {
            KategoriAlat::create($kategori);
        }
    }
}
