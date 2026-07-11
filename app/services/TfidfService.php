<?php

namespace App\Services;

class TfidfService
{
    /*
    |--------------------------------------------------------------------------
    | Case Folding
    |--------------------------------------------------------------------------
    */

    public function caseFolding($text)
    {
        return strtolower(trim($text));
    }

    /*
    |--------------------------------------------------------------------------
    | Tokenizing
    |--------------------------------------------------------------------------
    */

    public function tokenizing($text)
    {
        $text = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $text);

        return array_values(array_filter(
            preg_split('/\s+/', trim($text))
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Stopword Removal
    |--------------------------------------------------------------------------
    */

    public function stopword($tokens)
    {
        $stopwords = [

            'dan',
            'yang',
            'atau',
            'di',
            'ke',
            'dari',
            'untuk',
            'pada',
            'dengan',
            'adalah',
            'itu',
            'ini',
            'karena',
            'sebagai',
            'agar',
            'jadi',
            'oleh',
            'dalam',
            'akan',
            'bagi'

        ];

        return array_values(array_filter($tokens, function ($word) use ($stopwords) {

            return !in_array($word, $stopwords);

        }));
    }

    /*
    |--------------------------------------------------------------------------
    | Stemming Sederhana
    |--------------------------------------------------------------------------
    */

    public function stemming($tokens)
    {
        $hasil = [];

        foreach ($tokens as $token) {

            $token = preg_replace(
                '/(lah|kah|nya|pun|ku|mu|kan|an|i)$/',
                '',
                $token
            );

            $hasil[] = $token;
        }

        return $hasil;
    }

    /*
    |--------------------------------------------------------------------------
    | Preprocessing Lengkap
    |--------------------------------------------------------------------------
    */

    public function preprocessing($text)
    {
        $text = $this->caseFolding($text);

        $tokens = $this->tokenizing($text);

        $tokens = $this->stopword($tokens);

        $tokens = $this->stemming($tokens);

        return $tokens;
    }

    /*
    |--------------------------------------------------------------------------
    | Term Frequency (TF)
    |--------------------------------------------------------------------------
    */

    public function tf($tokens)
    {
        $tf = [];

        foreach ($tokens as $token) {

            if (!isset($tf[$token])) {

                $tf[$token] = 0;
            }

            $tf[$token]++;
        }

        $total = max(count($tokens), 1);

        foreach ($tf as $term => $value) {

            $tf[$term] = $value / $total;
        }

        return $tf;
    }

    /*
    |--------------------------------------------------------------------------
    | Inverse Document Frequency (IDF)
    |--------------------------------------------------------------------------
    */

    public function idf($documents)
    {
        $idf = [];

        $jumlahDokumen = count($documents);

        $documentFrequency = [];

        foreach ($documents as $document) {

            foreach (array_unique($document) as $term) {

                if (!isset($documentFrequency[$term])) {

                    $documentFrequency[$term] = 0;
                }

                $documentFrequency[$term]++;
            }
        }

        foreach ($documentFrequency as $term => $df) {

            $idf[$term] = log(($jumlahDokumen + 1) / ($df + 1)) + 1;
        }

        return $idf;
    }

    /*
    |--------------------------------------------------------------------------
    | TF-IDF
    |--------------------------------------------------------------------------
    */

    public function tfidf($tf, $idf)
    {
        $vector = [];

        foreach ($tf as $term => $value) {

            $vector[$term] = $value * ($idf[$term] ?? 0);
        }

        return $vector;
    }
}