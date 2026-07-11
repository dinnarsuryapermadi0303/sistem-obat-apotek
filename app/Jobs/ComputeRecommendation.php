<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use App\Helpers\ExcelHelper;
use App\Services\RuleBasedService;
use App\Services\RecommendationScoringService;

class ComputeRecommendation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $params;
    protected $jobId;

    public function __construct(array $params, string $jobId)
    {
        $this->params = $params;
        $this->jobId = $jobId;
    }

    public function handle()
    {
        $startTime = microtime(true);

        $nama = trim($this->params['nama'] ?? '');
        $usia = trim($this->params['usia'] ?? '');
        $keluhan = strtolower(trim($this->params['keluhan'] ?? ''));
        $durasi = strtolower(trim($this->params['durasi'] ?? ''));
        $riwayat = strtolower(trim($this->params['riwayat'] ?? ''));

        // get obat list from cache
        $cacheKey = 'obat_list_v1';
        $obatList = Cache::remember($cacheKey, 300, function () {
            return ExcelHelper::readObatData(ExcelHelper::resolveExcelPath());
        });

        $totalObat = count($obatList);

        $ruleService = new RuleBasedService();
        $scoringService = new RecommendationScoringService();

        $candidateObat = $ruleService->filter($keluhan, $obatList);
        $totalCandidate = count($candidateObat);

        $hasil = [];

        if ($totalCandidate > 0) {
            $scored = $scoringService->score([
                'keluhan' => $keluhan,
                'durasi' => $durasi,
                'riwayat' => $riwayat,
            ], $candidateObat, 8);

            $hasil = $scored['hasil'];
            foreach ($hasil as $index => &$item) {
                $item['nama_pasien'] = $nama;
                $item['usia'] = $usia;
                $item['keluhan'] = $keluhan;
                $item['durasi'] = $durasi;
                $item['riwayat'] = $riwayat;
                $item['similarity_pct'] = $item['persentase'];
                $item['ranking'] = $index + 1;
            }
            unset($item);
        }

        $processTime = round(microtime(true) - $startTime, 4);

        Cache::put('rekomendasi_result_' . $this->jobId, [
            'status' => 'done',
            'hasil' => $hasil,
            'processTime' => $processTime,
            'total' => count($hasil),
        ], 600);
    }
}
