<?php

namespace Database\Seeders;

use App\Models\Pasien;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PasienSeeder extends Seeder
{
    public function run(): void
    {
        $pasiens = [
            [
                'nama' => 'Budi Santoso',
                'usia' => 35,
                'email' => 'budi@email.com',
                'nomor_telepon' => '081234567890',
                'alamat' => 'Jalan Merdeka No. 123, Jakarta',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1988-05-15'
            ],
            [
                'nama' => 'Siti Rahma',
                'usia' => 28,
                'email' => 'siti@email.com',
                'nomor_telepon' => '082345678901',
                'alamat' => 'Jalan Ahmad Yani No. 456, Bandung',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1995-08-22'
            ],
            [
                'nama' => 'Ahmad Wijaya',
                'usia' => 45,
                'email' => 'ahmad@email.com',
                'nomor_telepon' => '083456789012',
                'alamat' => 'Jalan Diponegoro No. 789, Surabaya',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1978-12-10'
            ],
            [
                'nama' => 'Dwi Kusuma',
                'usia' => 32,
                'email' => 'dwi@email.com',
                'nomor_telepon' => '084567890123',
                'alamat' => 'Jalan Sudirman No. 321, Medan',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1991-03-18'
            ],
            [
                'nama' => 'Rina Puspita',
                'usia' => 26,
                'email' => 'rina@email.com',
                'nomor_telepon' => '085678901234',
                'alamat' => 'Jalan Gatot Subroto No. 654, Yogyakarta',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1997-07-25'
            ],
        ];

        foreach ($pasiens as $pasien) {
            Pasien::create($pasien);
        }
    }
}
