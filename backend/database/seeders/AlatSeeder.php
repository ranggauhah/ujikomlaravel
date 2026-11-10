<?php

namespace Database\Seeders;

use App\Models\Alat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AlatSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan folder storage/app/public/alat tersedia
        Storage::disk('public')->makeDirectory('alat');

        // Salin foto default dari public/images/alat ke storage/app/public/alat
        // supaya foto tetap ada setelah clone ulang tanpa perlu upload manual
        $fotoSumber = public_path('images/alat');
        $fotoDest   = storage_path('app/public/alat');

        if (is_dir($fotoSumber)) {
            foreach (glob($fotoSumber . '/*.jpg') as $file) {
                $nama = basename($file);
                if (!file_exists($fotoDest . '/' . $nama)) {
                    copy($file, $fotoDest . '/' . $nama);
                }
            }
        }

        $alats = [
            // Kategori 1 - Elektronik
            ['kategori_id' => 1, 'nama_alat' => 'Laptop Dell Inspiron',       'merk' => 'Dell',     'jumlah' => 10, 'deskripsi' => 'Laptop i5 RAM 8GB untuk pembelajaran dan praktikum',      'kondisi' => 'baik', 'foto' => 'alat/laptop_dell.jpg'],
            ['kategori_id' => 1, 'nama_alat' => 'Proyektor Epson EB-X41',     'merk' => 'Epson',    'jumlah' => 5,  'deskripsi' => 'Proyektor XGA 3600 lumen untuk presentasi',               'kondisi' => 'baik', 'foto' => 'alat/proyektor_epson.jpg'],
            ['kategori_id' => 1, 'nama_alat' => 'Kamera Canon EOS 1500D',     'merk' => 'Canon',    'jumlah' => 3,  'deskripsi' => 'Kamera DSLR 24MP untuk dokumentasi kegiatan',             'kondisi' => 'baik', 'foto' => 'alat/kamera_canon.jpg'],
            ['kategori_id' => 1, 'nama_alat' => 'Tablet Samsung Galaxy Tab',  'merk' => 'Samsung',  'jumlah' => 8,  'deskripsi' => 'Tablet Android 10 inci untuk praktikum mobile',           'kondisi' => 'baik', 'foto' => 'alat/tablet_samsung.jpg'],
            ['kategori_id' => 1, 'nama_alat' => 'Speaker Aktif Yamaha',       'merk' => 'Yamaha',   'jumlah' => 4,  'deskripsi' => 'Speaker aktif 200W untuk kegiatan audio',                 'kondisi' => 'baik', 'foto' => 'alat/speaker_yamaha.jpg'],

            // Kategori 2 - Jaringan
            ['kategori_id' => 2, 'nama_alat' => 'Router Cisco RV160',         'merk' => 'Cisco',    'jumlah' => 6,  'deskripsi' => 'Router VPN untuk praktikum jaringan',                    'kondisi' => 'baik', 'foto' => 'alat/router_cisco.jpg'],
            ['kategori_id' => 2, 'nama_alat' => 'Switch Manageable TP-Link',  'merk' => 'TP-Link',  'jumlah' => 8,  'deskripsi' => 'Switch 24 port gigabit untuk lab jaringan',               'kondisi' => 'baik', 'foto' => 'alat/switch_tplink.jpg'],
            ['kategori_id' => 2, 'nama_alat' => 'Kabel LAN CAT6 (roll)',      'merk' => 'Belden',   'jumlah' => 10, 'deskripsi' => 'Kabel jaringan 305m untuk instalasi',                    'kondisi' => 'baik', 'foto' => 'alat/kabel_lan.jpg'],
            ['kategori_id' => 2, 'nama_alat' => 'Access Point Ubiquiti UniFi','merk' => 'Ubiquiti', 'jumlah' => 5,  'deskripsi' => 'Access point WiFi 6 dual-band untuk lab',                'kondisi' => 'baik', 'foto' => 'alat/access_point.jpg'],

            // Kategori 3 - Lab Komputer
            ['kategori_id' => 3, 'nama_alat' => 'Mouse Wireless Logitech M185','merk' => 'Logitech','jumlah' => 30, 'deskripsi' => 'Mouse wireless nano receiver untuk lab komputer',         'kondisi' => 'baik', 'foto' => 'alat/mouse_logitech.jpg'],
            ['kategori_id' => 3, 'nama_alat' => 'Keyboard USB A4Tech',         'merk' => 'A4Tech',  'jumlah' => 25, 'deskripsi' => 'Keyboard USB standar untuk lab komputer',                'kondisi' => 'baik', 'foto' => 'alat/keyboard_a4tech.jpg'],
            ['kategori_id' => 3, 'nama_alat' => 'Monitor LED 22 inci LG',      'merk' => 'LG',      'jumlah' => 15, 'deskripsi' => 'Monitor LED Full HD untuk workstation',                  'kondisi' => 'baik', 'foto' => 'alat/monitor_lg.jpg'],
            ['kategori_id' => 3, 'nama_alat' => 'Hardisk External 1TB Seagate','merk' => 'Seagate', 'jumlah' => 12, 'deskripsi' => 'Hardisk eksternal USB 3.0 untuk backup data',            'kondisi' => 'baik', 'foto' => 'alat/hardisk_seagate.jpg'],

            // Kategori 4 - Olahraga
            ['kategori_id' => 4, 'nama_alat' => 'Bola Futsal Mikasa FL450',   'merk' => 'Mikasa',   'jumlah' => 20, 'deskripsi' => 'Bola futsal original size 4',                            'kondisi' => 'baik', 'foto' => 'alat/bola_futsal.jpg'],
            ['kategori_id' => 4, 'nama_alat' => 'Net Badminton Yonex',        'merk' => 'Yonex',    'jumlah' => 6,  'deskripsi' => 'Net badminton standar turnamen',                         'kondisi' => 'baik', 'foto' => 'alat/net_badminton.jpg'],
            ['kategori_id' => 4, 'nama_alat' => 'Raket Badminton Lining',     'merk' => 'Li-Ning',  'jumlah' => 16, 'deskripsi' => 'Raket badminton carbon fiber',                           'kondisi' => 'baik', 'foto' => 'alat/raket_badminton.jpg'],
        ];

        foreach ($alats as $alat) {
            Alat::create($alat);
        }
    }
}
