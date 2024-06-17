<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Hasil;
use App\Models\Klasifikasi;
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


        $pendudukData = [
            [
                'No_KK' => 213123,
                'NIK' => 324323,
                'Nama_lengkap' => 'Rohidin Saepulloh',
                'Hbg_kel' => 'Suami',
                'JK' => 'Laki - Laki',
                'tmpt_lahir' => 'Bandung',
                'tgl_lahir' => '2024-05-08',
                'Agama' => 'Islam',
                'Pendidikan_terakhir' => 'S1',
                'Jenis_bantuan' => 'SKTM',
                'Penerima_bantuan' => 'Ya',
                'Jenis_bantuan_lain' => 'Tidak'
            ],
            [
                'No_KK' => 213124,
                'NIK' => 324324,
                'Nama_lengkap' => 'Siti Nurjanah',
                'Hbg_kel' => 'Istri',
                'JK' => 'Perempuan',
                'tmpt_lahir' => 'Jakarta',
                'tgl_lahir' => '2023-04-10',
                'Agama' => 'Kristen',
                'Pendidikan_terakhir' => 'SMA',
                'Jenis_bantuan' => 'Bantuan Sosial',
                'Penerima_bantuan' => 'Tidak',
                'Jenis_bantuan_lain' => 'Ya'
            ],
            [
                'No_KK' => 213125,
                'NIK' => 324325,
                'Nama_lengkap' => 'Supriyanto',
                'Hbg_kel' => 'Anak',
                'JK' => 'Laki - Laki',
                'tmpt_lahir' => 'Surabaya',
                'tgl_lahir' => '2021-03-15',
                'Agama' => 'Hindu',
                'Pendidikan_terakhir' => 'SD',
                'Jenis_bantuan' => 'Bantuan Pendidikan',
                'Penerima_bantuan' => 'Ya',
                'Jenis_bantuan_lain' => 'Tidak'
            ],
            [
                'No_KK' => 213126,
                'NIK' => 324326,
                'Nama_lengkap' => 'Putra Jaya',
                'Hbg_kel' => 'Ayah',
                'JK' => 'Laki - Laki',
                'tmpt_lahir' => 'Medan',
                'tgl_lahir' => '1975-02-20',
                'Agama' => 'Buddha',
                'Pendidikan_terakhir' => 'D3',
                'Jenis_bantuan' => 'Kartu Lansia',
                'Penerima_bantuan' => 'Ya',
                'Jenis_bantuan_lain' => 'Tidak'
            ],
            [
                'No_KK' => 213127,
                'NIK' => 324327,
                'Nama_lengkap' => 'putri jaya',
                'Hbg_kel' => 'Ibu',
                'JK' => 'Perempuan',
                'tmpt_lahir' => 'Yogyakarta',
                'tgl_lahir' => '1980-07-25',
                'Agama' => 'Islam',
                'Pendidikan_terakhir' => 'S2',
                'Jenis_bantuan' => 'Bantuan Kesehatan',
                'Penerima_bantuan' => 'Tidak',
                'Jenis_bantuan_lain' => 'Ya'
            ]
        ];

        foreach ($pendudukData as $penduduk) {
            Penduduk::create($penduduk);
        }

        Pekerjaan::create([
            'id_penduduk' => 1,
            'pekerjaan' => 'Wiraswasta',
            'pendapatan' => 2000000,
            'jumlah' => 7,
            'status' => 'Tidak Bekerja'
        ]);

        Pekerjaan::create([
            'id_penduduk' => 2,
            'pekerjaan' => 'Karyawan',
            'pendapatan' => 4000000,
            'jumlah' => 1,
            'status' => 'Bekerja'
        ]);

        Pekerjaan::create([
            'id_penduduk' => 3,
            'pekerjaan' => 'Wirausaha',
            'pendapatan' => 10000000,
            'jumlah' => 3,
            'status' => 'Bekerja'
        ]);

        Pekerjaan::create([
            'id_penduduk' => 4,
            'pekerjaan' => 'Guide',
            'pendapatan' => 2000000,
            'jumlah' => 7,
            'status' => 'Bekerja'
        ]);

        Pekerjaan::create([
            'id_penduduk' => 5,
            'pekerjaan' => 'Satpam',
            'pendapatan' => 2000000,
            'jumlah' => 1,
            'status' => 'Tidak Bekerja'
        ]);

        Hasil::create([
            'id_penduduk' => 1,
            'pendapatan' => 'Rendah',
            'jumlah' => 'Banyak',
            'status' => 'Tidak Bekerja',
            'keterangan' => 'Layak'
        ]);

        Hasil::create([
            'id_penduduk' => 2,
            'pendapatan' => 'Menengah',
            'jumlah' => 'Sedikit',
            'status' => 'Bekerja',
            'keterangan' => 'Tidak Layak'
        ]);

        Hasil::create([
            'id_penduduk' => 3,
            'pendapatan' => 'Tinggi',
            'jumlah' => 'Sedang',
            'status' => 'Bekerja',
            'keterangan' => 'Tidak Layak'
        ]);

        Hasil::create([
            'id_penduduk' => 4,
            'pendapatan' => 'Rendah',
            'jumlah' => 'Banyak',
            'status' => 'Bekerja',
            'keterangan' => 'Layak'
        ]);

        Hasil::create([
            'id_penduduk' => 5,
            'pendapatan' => 'Rendah',
            'jumlah' => 'Sedikit',
            'status' => 'Tidak Bekerja',
            'keterangan' => 'Layak'
        ]);

        Klasifikasi::create([
            'id_penduduk' => 1,
            'pendapatan' => 'Rendah',
            'jumlah' => 'Banyak',
            'status' => 'Tidak Bekerja',
            'keterangan' => 'Layak'
        ]);

        Klasifikasi::create([
            'id_penduduk' => 2,
            'pendapatan' => 'Menengah',
            'jumlah' => 'Sedikit',
            'status' => 'Bekerja',
            'keterangan' => 'Tidak Layak'
        ]);

        Klasifikasi::create([
            'id_penduduk' => 3,
            'pendapatan' => 'Tinggi',
            'jumlah' => 'Sedang',
            'status' => 'Bekerja',
            'keterangan' => 'Tidak Layak'
        ]);

        Klasifikasi::create([
            'id_penduduk' => 4,
            'pendapatan' => 'Rendah',
            'jumlah' => 'Banyak',
            'status' => 'Bekerja',
            'keterangan' => 'Layak'
        ]);

        Klasifikasi::create([
            'id_penduduk' => 5,
            'pendapatan' => 'Rendah',
            'jumlah' => 'Sedikit',
            'status' => 'Tidak Bekerja',
            'keterangan' => 'Layak'
        ]);
    }
}
