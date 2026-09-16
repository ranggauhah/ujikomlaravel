<?php

namespace Database\Seeders;

use App\Models\Alat;
use Illuminate\Database\Seeder;

class AlatSeeder extends Seeder
{
    public function run(): void
    {
        $alats = [
            // Kategori 1 - Elektronik
            ['kategori_id' => 1, 'nama_alat' => 'Laptop Dell Inspiron', 'merk' => 'Dell',    'jumlah' => 10, 'deskripsi' => 'Laptop i5 RAM 8GB untuk pembelajaran dan praktikum', 'kondisi' => 'baik'],
            ['kategori_id' => 1, 'nama_alat' => 'Proyektor Epson EB-X41', 'merk' => 'Epson', 'jumlah' => 5,  'deskripsi' => 'Proyektor XGA 3600 lumen untuk presentasi', 'kondisi' => 'baik'],
            ['kategori_id' => 1, 'nama_alat' => 'Kamera Canon EOS 1500D', 'merk' => 'Canon', 'jumlah' => 3,  'deskripsi' => 'Kamera DSLR 24MP untuk dokumentasi kegiatan', 'kondisi' => 'baik'],
            ['kategori_id' => 1, 'nama_alat' => 'Tablet Samsung Galaxy Tab', 'merk' => 'Samsung', 'jumlah' => 8, 'deskripsi' => 'Tablet Android 10 inci untuk praktikum mobile', 'kondisi' => 'baik'],
            ['kategori_id' => 1, 'nama_alat' => 'Speaker Aktif Yamaha', 'merk' => 'Yamaha',   'jumlah' => 4,  'deskripsi' => 'Speaker aktif 200W untuk kegiatan audio', 'kondisi' => 'baik'],

            // Kategori 2 - Jaringan
            ['kategori_id' => 2, 'nama_alat' => 'Router Cisco RV160', 'merk' => 'Cisco',       'jumlah' => 6,  'deskripsi' => 'Router VPN untuk praktikum jaringan', 'kondisi' => 'baik'],
            ['kategori_id' => 2, 'nama_alat' => 'Switch Manageable TP-Link', 'merk' => 'TP-Link', 'jumlah' => 8, 'deskripsi' => 'Switch 24 port gigabit untuk lab jaringan', 'kondisi' => 'baik'],
            ['kategori_id' => 2, 'nama_alat' => 'Kabel LAN CAT6 (roll)', 'merk' => 'Belden',   'jumlah' => 10, 'deskripsi' => 'Kabel jaringan 305m untuk instalasi', 'kondisi' => 'baik'],
            ['kategori_id' => 2, 'nama_alat' => 'Access Point Ubiquiti UniFi', 'merk' => 'Ubiquiti', 'jumlah' => 5, 'deskripsi' => 'Access point WiFi 6 dual-band untuk lab', 'kondisi' => 'baik'],

            // Kategori 3 - Lab Komputer
            ['kategori_id' => 3, 'nama_alat' => 'Mouse Wireless Logitech M185', 'merk' => 'Logitech', 'jumlah' => 30, 'deskripsi' => 'Mouse wireless nano receiver untuk lab komputer', 'kondisi' => 'baik'],
            ['kategori_id' => 3, 'nama_alat' => 'Keyboard USB A4Tech', 'merk' => 'A4Tech',     'jumlah' => 25, 'deskripsi' => 'Keyboard USB standar untuk lab komputer', 'kondisi' => 'baik'],
            ['kategori_id' => 3, 'nama_alat' => 'Monitor LED 22 inci LG', 'merk' => 'LG',      'jumlah' => 15, 'deskripsi' => 'Monitor LED Full HD untuk workstation', 'kondisi' => 'baik'],
            ['kategori_id' => 3, 'nama_alat' => 'Hardisk External 1TB Seagate', 'merk' => 'Seagate', 'jumlah' => 12, 'deskripsi' => 'Hardisk eksternal USB 3.0 untuk backup data', 'kondisi' => 'baik'],

            // Kategori 4 - Olahraga
            ['kategori_id' => 4, 'nama_alat' => 'Bola Futsal Mikasa FL450', 'merk' => 'Mikasa', 'jumlah' => 20, 'deskripsi' => 'Bola futsal original size 4', 'kondisi' => 'baik'],
            ['kategori_id' => 4, 'nama_alat' => 'Net Badminton Yonex', 'merk' => 'Yonex',       'jumlah' => 6,  'deskripsi' => 'Net badminton standar turnamen', 'kondisi' => 'baik'],
            ['kategori_id' => 4, 'nama_alat' => 'Raket Badminton Lining', 'merk' => 'Li-Ning',  'jumlah' => 16, 'deskripsi' => 'Raket badminton carbon fiber', 'kondisi' => 'baik'],
        ];

        foreach ($alats as $alat) {
            Alat::create($alat);
        }
    }
}
