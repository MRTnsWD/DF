<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Pekerjaan;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create([
            'name' => 'Admin ',
            'email' => 'admin@gmail.com',
            'role' => 'kph',
            'password' => bcrypt('admin123')
        ]);

        User::create([
            'name' => 'rw1 ',
            'email' => 'rw1@gmail.com',
            'role' => 'rw',
            'password' => bcrypt('password123')
        ]);

        // Buat rw2 sampai rw10
        for ($i = 2; $i <= 10; $i++) {
            User::create([
                'name' => 'rw' . $i,
                'email' => 'rw' . $i . '@gmail.com',
                'role' => 'rw',
                'password' => bcrypt('password123')
            ]);
        }


        Penduduk::create([
            'No_KK' => 213123,
            'NIK' => 324323,
            'Nama_lengkap' => 'Warga 1',
            'Hbg_kel' => 'Suami',
            'JK' => 'Laki - Laki ',
            'tmpt_lahir' => 'bandung',
            'tgl_lahir' => '2024-05-08',
            'Agama' => 'Islam',
            'Pendidikan_terakhir' => 'S1',
            'Jenis_bantuan' => 'SKTM',
            'Penerima_bantuan' => 'Ya',
            'Jenis_bantuan_lain' => 'Tidak'
        ]);

        Pekerjaan::create([
            'id_penduduk' => 1,
            'Pekerjaan' => 'PNS',
            'Penghasilan' => 5000000,
        ]);
    }
}
