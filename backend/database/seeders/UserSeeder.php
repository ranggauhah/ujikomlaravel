<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Admin
            [
                'name'     => 'Administrator',
                'email'    => 'admin@example.com',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ],
            // Petugas
            [
                'name'     => 'Budi Santoso',
                'email'    => 'petugas@example.com',
                'password' => Hash::make('password'),
                'role'     => 'petugas',
            ],
            [
                'name'     => 'Siti Rahayu',
                'email'    => 'petugas2@example.com',
                'password' => Hash::make('password'),
                'role'     => 'petugas',
            ],
            // Peminjam
            [
                'name'     => 'Ahmad Fauzi',
                'email'    => 'peminjam@example.com',
                'password' => Hash::make('password'),
                'role'     => 'peminjam',
            ],
            [
                'name'     => 'Dewi Kusuma',
                'email'    => 'dewi@example.com',
                'password' => Hash::make('password'),
                'role'     => 'peminjam',
            ],
            [
                'name'     => 'Rizky Pratama',
                'email'    => 'rizky@example.com',
                'password' => Hash::make('password'),
                'role'     => 'peminjam',
            ],
            [
                'name'     => 'Nur Hidayah',
                'email'    => 'nur@example.com',
                'password' => Hash::make('password'),
                'role'     => 'peminjam',
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }
    }
}
