<?php

namespace Database\Seeders;

use App\Models\Obat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ObatSeeder extends Seeder
{
    public function run(): void
    {
        $obats = [
            [
                'nama' => 'Promag',
                'kategori' => 'Antasida',
                'deskripsi' => 'Obat untuk mengatasi sakit perut, lambung, dan maag ringan',
                'indikasi' => 'Maag ringan',
                'durasi' => 'ringan',
                'harga' => 10000,
                'stok' => 50,
                'gambar' => 'promag.jpg'
            ],
            [
                'nama' => 'Ranitidine',
                'kategori' => 'Antasida',
                'deskripsi' => 'Obat untuk mengatasi asam lambung dan nyeri perut',
                'indikasi' => 'Maag kronis',
                'durasi' => 'lama',
                'harga' => 27000,
                'stok' => 30,
                'gambar' => 'ranitidine.jpg'
            ],
            [
                'nama' => 'Omeprazole',
                'kategori' => 'Antasida',
                'deskripsi' => 'Obat untuk mengatasi refluks asam lambung',
                'indikasi' => 'Maag berkepanjangan',
                'durasi' => 'lama',
                'harga' => 80000,
                'stok' => 20,
                'gambar' => 'omeprazole.jpg'
            ],
            [
                'nama' => 'Paracetamol',
                'kategori' => 'Analgesik',
                'deskripsi' => 'Obat pereda demam, sakit kepala, dan pusing',
                'indikasi' => 'Demam ringan',
                'durasi' => 'ringan',
                'harga' => 5000,
                'stok' => 100,
                'gambar' => 'paracetamol.jpg'
            ],
            [
                'nama' => 'Ibuprofen',
                'kategori' => 'Analgesik',
                'deskripsi' => 'Obat untuk mengatasi nyeri dan peradangan',
                'indikasi' => 'Nyeri otot',
                'durasi' => 'sedang',
                'harga' => 8000,
                'stok' => 60,
                'gambar' => 'ibuprofen.jpg'
            ],
            [
                'nama' => 'OBH Combi',
                'kategori' => 'Obat Batuk',
                'deskripsi' => 'Obat untuk mengatasi batuk, pilek, dan sakit tenggorokan',
                'indikasi' => 'Batuk ringan',
                'durasi' => 'ringan',
                'harga' => 12000,
                'stok' => 40,
                'gambar' => 'obh.jpg'
            ],
            [
                'nama' => 'Sirup Obat Batuk',
                'kategori' => 'Obat Batuk',
                'deskripsi' => 'Sirup untuk mengatasi batuk berdahak',
                'indikasi' => 'Batuk berdahak',
                'durasi' => 'sedang',
                'harga' => 15000,
                'stok' => 25,
                'gambar' => 'sirup_batuk.jpg'
            ],
            [
                'nama' => 'CTM',
                'kategori' => 'Antihistamin',
                'deskripsi' => 'Obat untuk mengatasi alergi, gatal, dan bersin',
                'indikasi' => 'Alergi',
                'durasi' => 'ringan',
                'harga' => 3000,
                'stok' => 80,
                'gambar' => 'ctm.jpg'
            ],
            [
                'nama' => 'Loratadine',
                'kategori' => 'Antihistamin',
                'deskripsi' => 'Obat untuk mengatasi alergi dan rhinitis',
                'indikasi' => 'Alergi berat',
                'durasi' => 'sedang',
                'harga' => 6000,
                'stok' => 45,
                'gambar' => 'loratadine.jpg'
            ],
            [
                'nama' => 'Vitamin C',
                'kategori' => 'Vitamin',
                'deskripsi' => 'Suplemen vitamin C untuk meningkatkan imunitas',
                'indikasi' => 'Penambah imun',
                'durasi' => 'lama',
                'harga' => 7000,
                'stok' => 120,
                'gambar' => 'vitamin_c.jpg'
            ],
        ];

        foreach ($obats as $obat) {
            Obat::create($obat);
        }
    }
}
