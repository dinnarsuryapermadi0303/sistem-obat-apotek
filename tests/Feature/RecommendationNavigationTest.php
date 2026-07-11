<?php

namespace Tests\Feature;

use Tests\TestCase;

class RecommendationNavigationTest extends TestCase
{
    public function test_detail_page_back_link_ke_hasil_rekomendasi_dengan_input_lama(): void
    {
        $view = view('user.detail_rekomendasi', [
            'obat' => [
                'nama' => 'Paracetamol',
                'kategori' => 'Obat Bebas',
                'gambar' => 'default.png',
                'indikasi' => 'Meredakan demam',
                'deskripsi' => 'Obat penurun demam',
                'komposisi' => 'Paracetamol',
                'dosis' => '1 tablet',
                'efek_samping' => 'Tidak ada',
            ],
            'similarity' => 85,
            'key' => 'abc123',
            'nama' => 'Budi',
            'usia' => '30',
            'query' => 'demam',
            'durasi' => '2 hari',
            'riwayat' => 'tidak ada',
            'confidence' => 'High',
            'display_time' => '27-06-2026 10:00',
        ])->render();

        $this->assertStringContainsString('/rekomendasi?', $view);
        $this->assertStringContainsString('nama=Budi', $view);
        $this->assertStringContainsString('keluhan=demam', $view);
        $this->assertStringContainsString('durasi=2', $view);
    }
}
