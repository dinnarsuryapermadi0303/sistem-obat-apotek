<?php

namespace Tests\Unit;

use App\Services\RecommendationScoringService;
use PHPUnit\Framework\TestCase;

class RecommendationScoringServiceTest extends TestCase
{
    public function test_it_ranks_the_best_matching_product_first()
    {
        $service = new RecommendationScoringService();

        $queryData = [
            'nama' => '',
            'usia' => '',
            'keluhan' => 'sakit kepala demam',
            'durasi' => '2 hari',
            'riwayat' => '',
        ];

        $candidateObat = [
            [
                'nama' => 'Paracetamol',
                'indikasi' => 'mengatasi demam dan sakit kepala',
                'deskripsi' => 'obat penurun demam',
            ],
            [
                'nama' => 'Amoxicillin',
                'indikasi' => 'mengatasi infeksi batuk',
                'deskripsi' => 'antibiotik untuk batuk',
            ],
            [
                'nama' => 'Vitamin C',
                'indikasi' => 'menjaga daya tahan',
                'deskripsi' => 'vitamin penambah stamina',
            ],
        ];

        $result = $service->score($queryData, $candidateObat, 3);

        $this->assertSame(3, $result['total']);
        $this->assertSame('Paracetamol', $result['hasil'][0]['nama']);
        $this->assertGreaterThan(0, $result['hasil'][0]['persentase']);
        $this->assertGreaterThanOrEqual($result['hasil'][2]['persentase'], $result['hasil'][0]['persentase']);
    }
}
