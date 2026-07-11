<?php

namespace App\Services;

class RuleBasedService
{
    public function filter($keluhan, $obatList)
    {
        $keluhan = strtolower($keluhan);

        // Rule berdasarkan kategori gejala
        $rules = [

            'demam' => [
                'demam',
                'panas',
                'menggigil',
                'flu',
                'suhu tinggi',
                'lemas'
            ],

            'batuk' => [
                'batuk',
                'batuk berdahak',
                'batuk kering',
                'batuk parah',
                'tenggorokan',
                'batuk terus-menerus'
            ],

            'pilek' => [
                'pilek',
                'hidung tersumbat',
                'ingus',
                'bersin',
                'mata berair',
                'hidung meler'
            ],

            'sakit kepala' => [
                'pusing',
                'sakit kepala',
                'migren',
                'kepala berdenyut'
            ],

            'sakit tenggorokan' => [
                'sakit tenggorokan',
                'tenggorokan sakit',
                'radang tenggorokan',
                'serak',
                'nyeri tenggorokan'
            ],

            'maag' => [
                'maag',
                'asam lambung',
                'nyeri lambung',
                'sakit perut',
                'mual',
                'muntah'
            ],

            'diare' => [
                'diare',
                'mencret',
                'buang air',
                'cairan'
            ],

            'alergi' => [
                'alergi',
                'gatal',
                'ruam',
                'bersin',
                'mata berair'
            ],

            'infeksi' => [
                'infeksi',
                'bakteri',
                'radang',
                'demam',
                'nyeri'
            ],

            'sesak nafas' => [
                'sesak nafas',
                'sesak napas',
                'susah napas',
                'napas pendek',
                'berat napas'
            ],

            'diabetes' => [
                'diabetes',
                'gula darah'
            ],

            'hipertensi' => [
                'hipertensi',
                'darah tinggi',
                'tensi'
            ],

            'asam urat' => [
                'asam urat',
                'nyeri sendi'
            ]

        ];

        $candidate = [];

        foreach ($obatList as $obat) {

            $text = strtolower(

                ($obat['nama'] ?? '') . ' ' .
                    ($obat['kategori'] ?? '') . ' ' .
                    ($obat['indikasi'] ?? '') . ' ' .
                    ($obat['deskripsi'] ?? '') . ' ' .
                    ($obat['jenis'] ?? '') . ' ' .
                    ($obat['efek_samping'] ?? '') . ' ' .
                    ($obat['komposisi'] ?? '') . ' ' .
                    ($obat['kontraindikasi'] ?? '')

            );

            foreach ($rules as $keywords) {

                foreach ($keywords as $keyword) {

                    if (

                        str_contains($keluhan, $keyword) &&
                        str_contains($text, $keyword)

                    ) {

                        $key = strtolower($obat['nama'] ?? '');

                        if (!isset($candidate[$key])) {

                            $candidate[$key] = $obat;
                        }

                        continue 3;
                    }
                }
            }
        }

        if (empty($candidate)) {
            return $obatList;
        }

        return array_values($candidate);
    }
}
