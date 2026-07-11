<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class KeluhanValidationTest extends TestCase
{
    /**
     * Validasi input keluhan - format validation
     */
    private function validateKeluhan($keluhan)
    {
        $words = array_filter(explode(' ', $keluhan));
        $wordCount = count($words);
        $maxWords = 6;

        // Check max words limit
        if ($wordCount > $maxWords) {
            return ['valid' => false, 'message' => "Sudah mencapai batas maksimal 6 kata. Kata saat ini: {$wordCount}", 'wordCount' => $wordCount];
        }

        if (strlen($keluhan) < 3) {
            return ['valid' => false, 'message' => 'Keluhan terlalu pendek. Masukkan minimal 3 karakter.'];
        }

        if (preg_match('/(.)\1{3,}/', $keluhan)) {
            return ['valid' => false, 'message' => 'Keluhan tidak valid. Jangan menggunakan karakter berulang.'];
        }

        $uniqueWords = array_unique($words);

        if (count($words) > 2 && count($uniqueWords) == 1) {
            return ['valid' => false, 'message' => 'Keluhan tidak valid. Jangan menggunakan kata yang berulang.'];
        }

        $meaninglessPatterns = [
            '/^(halo|hai|hi|ya|yes|no|ok|okay|wkwk|haha|hihi|asdf|zxcv|qwer)+(\s+(halo|hai|hi|ya|yes|no|ok|okay|wkwk|haha|hihi|asdf|zxcv|qwer)+)*$/i',
        ];

        foreach ($meaninglessPatterns as $pattern) {
            if (preg_match($pattern, $keluhan)) {
                return ['valid' => false, 'message' => 'Keluhan tidak valid. Masukkan deskripsi yang bermakna.'];
            }
        }

        if (!preg_match('/[a-z]/i', $keluhan)) {
            return ['valid' => false, 'message' => 'Keluhan tidak valid. Harus mengandung minimal satu huruf.'];
        }

        $shortWords = array_filter($words, function ($word) {
            return strlen($word) <= 1;
        });

        if (count($shortWords) > count($words) * 0.7) {
            return ['valid' => false, 'message' => 'Keluhan tidak valid. Gunakan kata-kata yang lebih bermakna.'];
        }

        return ['valid' => true];
    }

    /**
     * Validasi semantic - keluhan harus terkait kesehatan/obat
     */
    private function validateKeluhanSemantic($keluhan)
    {
        $healthKeywords = [
            'batuk',
            'demam',
            'sakit',
            'nyeri',
            'pusing',
            'kepala',
            'perut',
            'mual',
            'muntah',
            'diare',
            'sembelit',
            'konstipasi',
            'flu',
            'pilek',
            'hidung',
            'bersin',
            'tenggorokan',
            'radang',
            'luka',
            'jerawat',
            'gatal',
            'ruam',
            'kulit',
            'jamur',
            'infeksi',
            'sesak',
            'asma',
            'bronkitis',
            'pneumonia',
            'tuberkulosis',
            'tb',
            'maag',
            'asam',
            'lambung',
            'ulser',
            'gastritis',
            'kolesterol',
            'hipertensi',
            'tekanan darah',
            'jantung',
            'stroke',
            'diabetes',
            'gula darah',
            'kanker',
            'tumor',
            'antibiotik',
            'antivirus',
            'vitamin',
            'suplemen',
            'obat'
        ];

        $keluhanLower = strtolower($keluhan);
        $words = preg_split('/[\s\-.,;:!?\/]+/', $keluhanLower, -1, PREG_SPLIT_NO_EMPTY);

        $matchedCount = 0;
        foreach ($words as $word) {
            if (in_array(trim($word), $healthKeywords)) {
                $matchedCount++;
            }
        }

        if ($matchedCount === 0) {
            return ['valid' => false, 'message' => 'Keluhan tidak terkait dengan kesehatan atau obat.'];
        }

        return ['valid' => true];
    }

    // ===== FORMAT VALIDATION TESTS =====

    public function test_reject_too_short()
    {
        $result = $this->validateKeluhan('ab');
        $this->assertFalse($result['valid']);
    }

    public function test_reject_repeated_characters()
    {
        $result = $this->validateKeluhan('aaaa');
        $this->assertFalse($result['valid']);
    }

    public function test_reject_repeated_words()
    {
        $result = $this->validateKeluhan('halo halo halo');
        $this->assertFalse($result['valid']);
    }

    public function test_reject_meaningless_greeting()
    {
        $result = $this->validateKeluhan('halo');
        $this->assertFalse($result['valid']);
    }

    public function test_reject_only_numbers()
    {
        $result = $this->validateKeluhan('12345');
        $this->assertFalse($result['valid']);
    }

    public function test_reject_special_chars_only()
    {
        $result = $this->validateKeluhan('!!!@@##');
        $this->assertFalse($result['valid']);
    }

    public function test_accept_valid_symptom()
    {
        $result = $this->validateKeluhan('batuk');
        $this->assertTrue($result['valid']);
    }

    public function test_accept_multiple_symptoms()
    {
        $result = $this->validateKeluhan('batuk dan demam');
        $this->assertTrue($result['valid']);
    }

    public function test_accept_symptom_with_duration()
    {
        $result = $this->validateKeluhan('sakit kepala 2 hari');
        $this->assertTrue($result['valid']);
    }

    public function test_accept_detailed_symptom()
    {
        $result = $this->validateKeluhan('gatal-gatal di tangan dan kaki');
        $this->assertTrue($result['valid']);
    }

    public function test_accept_minimum_length()
    {
        $result = $this->validateKeluhan('flu');
        $this->assertTrue($result['valid']);
    }

    // ===== SEMANTIC VALIDATION TESTS =====

    public function test_semantic_reject_random_text()
    {
        $result = $this->validateKeluhanSemantic('mobil motor sepeda');
        $this->assertFalse($result['valid']);
    }

    public function test_semantic_reject_unrelated_content()
    {
        $result = $this->validateKeluhanSemantic('beli buku di toko');
        $this->assertFalse($result['valid']);
    }

    public function test_semantic_reject_technology_keywords()
    {
        $result = $this->validateKeluhanSemantic('laptop komputer internet');
        $this->assertFalse($result['valid']);
    }

    public function test_semantic_accept_health_keyword()
    {
        $result = $this->validateKeluhanSemantic('batuk');
        $this->assertTrue($result['valid']);
    }

    public function test_semantic_accept_symptom_with_unrelated()
    {
        $result = $this->validateKeluhanSemantic('batuk dan mobil');
        $this->assertTrue($result['valid']);
    }

    public function test_semantic_accept_multiple_symptoms()
    {
        $result = $this->validateKeluhanSemantic('batuk pilek demam');
        $this->assertTrue($result['valid']);
    }

    public function test_semantic_accept_obat_keyword()
    {
        $result = $this->validateKeluhanSemantic('butuh obat untuk sakit');
        $this->assertTrue($result['valid']);
    }

    // ===== WORD LIMIT TESTS =====

    public function test_reject_more_than_6_words()
    {
        $result = $this->validateKeluhan('batuk pilek demam sakit kepala pusing ngilu');
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('batas maksimal 6 kata', $result['message']);
    }

    public function test_accept_exactly_6_words()
    {
        $result = $this->validateKeluhan('batuk pilek demam sakit kepala pusing');
        $this->assertTrue($result['valid']);
    }

    public function test_accept_less_than_6_words()
    {
        $result = $this->validateKeluhan('batuk pilek demam');
        $this->assertTrue($result['valid']);
    }

    public function test_accept_single_word()
    {
        $result = $this->validateKeluhan('batuk');
        $this->assertTrue($result['valid']);
    }
}
