<?php

namespace App\Services;

class RecommendationScoringService
{
    public function score(array $queryData, array $candidateObat, int $limit = 8): array
    {
        $tfidfService = new TfidfService();
        $cosineService = new CosineSimilarityService();

        $keluhan = strtolower(trim($queryData['keluhan'] ?? ''));
        $durasi = strtolower(trim($queryData['durasi'] ?? ''));
        $riwayat = strtolower(trim($queryData['riwayat'] ?? ''));

        $queryText = $keluhan . ' ' . $durasi . ' ' . $riwayat;
        $queryTokens = $tfidfService->preprocessing($queryText);
        $queryTf = $tfidfService->tf($queryTokens);

        $documents = [];
        foreach ($candidateObat as $obat) {
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
            $documents[] = $tfidfService->preprocessing($text);
        }

        if (empty($documents)) {
            return [
                'hasil' => [],
                'total' => 0,
            ];
        }

        $idf = $tfidfService->idf($documents);
        $queryVector = $tfidfService->tfidf($queryTf, $idf);

        $hasil = [];
        foreach ($candidateObat as $index => $obat) {
            if (!isset($documents[$index])) {
                continue;
            }

            $documentTf = $tfidfService->tf($documents[$index]);
            $documentVector = $tfidfService->tfidf($documentTf, $idf);
            $similarity = $cosineService->similarity($queryVector, $documentVector);
            $persentase = $cosineService->percentage($similarity);

            $status = 'Kurang Relevan';
            $badge = 'danger';
            if ($persentase >= 80) {
                $status = 'Sangat Relevan';
                $badge = 'success';
            } elseif ($persentase >= 60) {
                $status = 'Relevan';
                $badge = 'primary';
            } elseif ($persentase >= 40) {
                $status = 'Cukup Relevan';
                $badge = 'warning';
            }

            $hasil[] = [
                'nama' => $obat['nama'] ?? '-',
                'obat' => $obat,
                'gambar' => $obat['gambar'] ?? 'default.png',
                'kategori' => $obat['kategori'] ?? '-',
                'jenis' => $obat['jenis'] ?? '-',
                'deskripsi' => $obat['deskripsi'] ?? '-',
                'indikasi' => $obat['indikasi'] ?? '-',
                'similarity' => round($similarity, 4),
                'persentase' => round($persentase, 2),
                'confidence' => number_format(round($persentase, 2), 2) . '%',
                'status' => $status,
                'badge' => $badge,
                'alasan' => 'Direkomendasikan karena mempunyai tingkat kemiripan sebesar ' . round($persentase, 2) . '% terhadap gejala yang dimasukkan pengguna.',
            ];
        }

        usort($hasil, function ($a, $b) {
            return $b['persentase'] <=> $a['persentase'];
        });

        $hasil = array_slice($hasil, 0, $limit);

        foreach ($hasil as $index => &$item) {
            $item['ranking'] = $index + 1;
        }
        unset($item);

        return [
            'hasil' => $hasil,
            'total' => count($hasil),
        ];
    }
}
