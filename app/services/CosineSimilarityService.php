<?php

namespace App\Services;

class CosineSimilarityService
{
    /*
    |--------------------------------------------------------------------------
    | Dot Product
    |--------------------------------------------------------------------------
    */

    public function dotProduct(array $vectorA, array $vectorB)
    {
        $dotProduct = 0;

        foreach ($vectorA as $term => $value) {

            if (isset($vectorB[$term])) {

                $dotProduct += $value * $vectorB[$term];

            }

        }

        return $dotProduct;
    }

    /*
    |--------------------------------------------------------------------------
    | Vector Length
    |--------------------------------------------------------------------------
    */

    public function vectorLength(array $vector)
    {
        $sum = 0;

        foreach ($vector as $value) {

            $sum += ($value * $value);

        }

        return sqrt($sum);
    }

    /*
    |--------------------------------------------------------------------------
    | Cosine Similarity
    |--------------------------------------------------------------------------
    */

    public function similarity(array $queryVector, array $documentVector)
    {
        $dotProduct = $this->dotProduct(
            $queryVector,
            $documentVector
        );

        $queryLength = $this->vectorLength(
            $queryVector
        );

        $documentLength = $this->vectorLength(
            $documentVector
        );

        if ($queryLength <= 0 || $documentLength <= 0) {

            return 0;

        }

        return $dotProduct / ($queryLength * $documentLength);
    }

    /*
    |--------------------------------------------------------------------------
    | Similarity Percentage
    |--------------------------------------------------------------------------
    */

    public function percentage($similarity)
    {
        return round($similarity * 100, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Ranking
    |--------------------------------------------------------------------------
    */

    public function ranking(array $hasil)
    {
        usort($hasil, function ($a, $b) {

            return $b['similarity'] <=> $a['similarity'];

        });

        return $hasil;
    }
}