<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ExcelHelper;
use Illuminate\Support\Facades\Cache;
use App\Services\RuleBasedService;
use App\Services\TfidfService;
use App\Services\CosineSimilarityService;
use App\Services\RecommendationScoringService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Jobs\ComputeRecommendation;

class RecommendationController extends Controller
{
    private $excelPath;

    public function __construct()
    {
        $this->excelPath = ExcelHelper::resolveExcelPath();
    }

    public function index(Request $request)
    {
        // Implement Post-Redirect-Get to avoid browser resubmit/looping
        if ($request->isMethod('post')) {
            $params = $request->only(['nama', 'usia', 'keluhan', 'durasi', 'riwayat']);
            // Use 303 See Other to ensure the browser performs a GET
            return redirect()->route('rekomendasi', $params)->setStatusCode(303);
        }
        /*
        |--------------------------------------------------------------------------
        | Waktu Proses
        |--------------------------------------------------------------------------
        */

        $startTime = microtime(true);

        /*
        |--------------------------------------------------------------------------
        | Input User
        |--------------------------------------------------------------------------
        */

        $nama = trim($request->input('nama', ''));

        $usia = trim($request->input('usia', ''));

        $keluhan = strtolower(trim($request->input('keluhan', '')));

        $durasi = strtolower(trim($request->input('durasi', '')));

        $riwayat = strtolower(trim($request->input('riwayat', '')));

        $totalObat = 0;

        // Ambil daftar keluhan historis dari DB (recent/terpopuler) untuk suggestions
        $keluhanOptions = collect([]);
        try {
            $keluhanOptions = DB::table('riwayat_penyakits')
                ->select('keluhan', DB::raw('count(*) as cnt'))
                ->whereNotNull('keluhan')
                ->groupBy('keluhan')
                ->orderByDesc('cnt')
                ->limit(500)
                ->pluck('keluhan')
                ->map(fn($v) => trim($v))
                ->filter()
                ->unique()
                ->values();
        } catch (\Throwable $e) {
            // jika DB belum tersedia atau error, tetap lanjut dengan list kosong
            $keluhanOptions = collect([]);
        }

        /*
        |--------------------------------------------------------------------------
        | Halaman Awal
        |--------------------------------------------------------------------------
        */
        if ($request->filled('nama')) {

            $user = session('user_data', []);

            $user[] = [

                'nama' => $request->nama,

                'waktu' => now()->format('d-m-Y H:i')

            ];

            session([

                'user_data' => $user

            ]);
        }

        if (empty($keluhan)) {
            return view('user.rekomendasi', [
                'hasil' => [],
                'query' => $keluhan,
                'nama' => $nama,
                'usia' => $usia,
                'durasi' => $durasi,
                'riwayat' => $riwayat,
                'total' => 0,
                'totalObat' => 0,
                'totalCandidate' => 0,
                'processTime' => 0,
                'keluhanOptions' => $keluhanOptions,
            ]);
        }

        // Validasi keluhan bukan input asal-asalan
        $keluhanValidation = $this->validateKeluhan($keluhan);
        if (!$keluhanValidation['valid']) {
            return view('user.rekomendasi', [
                'hasil' => [],
                'query' => $keluhan,
                'nama' => $nama,
                'usia' => $usia,
                'durasi' => $durasi,
                'riwayat' => $riwayat,
                'total' => 0,
                'totalObat' => 0,
                'totalCandidate' => 0,
                'processTime' => 0,
                'pesan' => $keluhanValidation['message'],
                'keluhanOptions' => $keluhanOptions,
            ]);
        }

        // Validasi semantic - keluhan harus terkait kesehatan/obat
        $semanticValidation = $this->validateKeluhanSemantic($keluhan);
        if (!$semanticValidation['valid']) {
            return view('user.rekomendasi', [
                'hasil' => [],
                'query' => $keluhan,
                'nama' => $nama,
                'usia' => $usia,
                'durasi' => $durasi,
                'riwayat' => $riwayat,
                'total' => 0,
                'totalObat' => 0,
                'totalCandidate' => 0,
                'processTime' => 0,
                'pesan' => 'Keluhan tidak ditemukan',
                'keluhanOptions' => $keluhanOptions,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Membaca Data Excel
        |--------------------------------------------------------------------------
        */

        // Cache obat list to reduce expensive Excel reads
        $cacheKey = 'obat_list_v1';
        $obatList = Cache::remember($cacheKey, 300, function () {
            return ExcelHelper::readObatData(
                ExcelHelper::resolveExcelPath()
            );
        });

        $totalObat = count($obatList);

        /*
        |--------------------------------------------------------------------------
        | Service
        |--------------------------------------------------------------------------
        */

        $ruleService = new RuleBasedService();

        $tfidfService = new TfidfService();

        $cosineService = new CosineSimilarityService();

        $scoringService = new RecommendationScoringService();

        /*
        |--------------------------------------------------------------------------
        | Rule Based Filtering
        |--------------------------------------------------------------------------
        */

        $candidateObat = $ruleService->filter(

            $keluhan,

            $obatList

        );

        $totalCandidate = count($candidateObat);

        /*
        |--------------------------------------------------------------------------
        | Tidak Ada Candidate
        |--------------------------------------------------------------------------
        */

        if ($totalCandidate == 0) {

            return view('user.rekomendasi', [

                'hasil' => [],

                'nama' => $nama,

                'usia' => $usia,

                'query' => $keluhan,

                'durasi' => $durasi,

                'riwayat' => $riwayat,

                'total' => 0,

                'totalObat' => $totalObat,

                'totalCandidate' => 0,

                'processTime' => round(

                    microtime(true) - $startTime,

                    4

                ),

                'pesan' =>

                'Tidak ditemukan obat yang sesuai dengan gejala yang dimasukkan.'

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Query User
        |--------------------------------------------------------------------------
        */

        $queryText =

            $keluhan . ' ' .

            $durasi . ' ' .

            $riwayat;

        $queryToken =

            $tfidfService->preprocessing(

                $queryText

            );

        $queryTF =

            $tfidfService->tf(

                $queryToken

            );

        /*
        |--------------------------------------------------------------------------
        | Preprocessing Dokumen Obat
        |--------------------------------------------------------------------------
        */

        $documents = [];

        foreach ($candidateObat as $obat) {

            $text = strtolower(

                ($obat['nama'] ?? '') . ' ' .
                    ($obat['indikasi'] ?? '') . ' ' .
                    ($obat['deskripsi'] ?? '')

            );

            $documents[] = $tfidfService->preprocessing(

                $text

            );
        }



        session([

            'last_recommendation' => [

                'nama' => $nama,

                'usia' => $usia,

                'keluhan' => $keluhan,

                'durasi' => $durasi,

                'riwayat' => $riwayat

            ]

        ]);
        /*
        |--------------------------------------------------------------------------
        | Data Kosong
        |--------------------------------------------------------------------------
        */

        if (empty($documents)) {


            return view('user.rekomendasi', [

                'hasil' => [],

                'nama' => $nama,

                'usia' => $usia,

                'query' => $keluhan,

                'durasi' => $durasi,

                'riwayat' => $riwayat,

                'total' => 0,

                'totalObat' => $totalObat,

                'totalCandidate' => 0,

                'processTime' => round(

                    microtime(true) - $startTime,

                    4

                ),

                'pesan' =>

                'Data obat tidak dapat diproses.',

                'keluhanOptions' => $keluhanOptions,

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | IDF
        |--------------------------------------------------------------------------
        */

        $idf = $tfidfService->idf(

            $documents

        );

        /*
        |--------------------------------------------------------------------------
        | Query Vector
        |--------------------------------------------------------------------------
        */

        $queryVector =

            $tfidfService->tfidf(

                $queryTF,

                $idf

            );

        /*
        |--------------------------------------------------------------------------
        | Similarity
        |--------------------------------------------------------------------------
        */

        /*
|--------------------------------------------------------------------------
| Hasil Similarity
|--------------------------------------------------------------------------
*/

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

        // store last results in session to allow saving full recommendation list
        session(['last_recommendation_results' => $hasil]);

        $totalHasil =

            count(

                $hasil

            );

        /*
        |--------------------------------------------------------------------------
        | Waktu Proses
        |--------------------------------------------------------------------------
        */

        $processTime = round(

            microtime(true) - $startTime,

            4

        );

        /*
        |--------------------------------------------------------------------------
        | Disclaimer
        |--------------------------------------------------------------------------
        */

        $disclaimer =

            "Rekomendasi dihitung menggunakan metode Rule Based Filtering, TF-IDF dan Cosine Similarity. Hasil ini digunakan sebagai referensi awal dan tidak menggantikan konsultasi dengan tenaga medis.";


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(

            'user.rekomendasi',

            [
                'hasil'            => $hasil,
                'query'            => $keluhan,
                'nama'             => $nama,
                'usia'             => $usia,
                'durasi'           => $durasi,
                'riwayat'          => $riwayat,
                'total'            => count($hasil),
                'processTime'      => $processTime,
                'totalObat'        => $totalObat,
                'totalCandidate'   => $totalCandidate,
                'disclaimer'       => $disclaimer,
                'keluhanOptions'   => $keluhanOptions,

            ]


        );
    }

    /**
     * AJAX: search keluhan suggestions from riwayat_penyakits
     */
    public function searchKeluhan(Request $request)
    {
        $q = trim($request->query('q', ''));
        try {
            $query = DB::table('riwayat_penyakits')
                ->select('keluhan', DB::raw('count(*) as cnt'))
                ->whereNotNull('keluhan');

            if ($q !== '') {
                $query = $query->where('keluhan', 'like', "%{$q}%");
            }

            $rows = $query->groupBy('keluhan')
                ->orderByDesc('cnt')
                ->limit(200)
                ->pluck('keluhan')
                ->map(fn($v) => trim($v))
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            return response()->json(['data' => $rows]);
        } catch (\Throwable $e) {
            return response()->json(['data' => []]);
        }
    }
    /**
     * Start async recommendation job and return job id
     */
    public function asyncStart(Request $request)
    {
        $params = $request->only(['nama', 'usia', 'keluhan', 'durasi', 'riwayat']);
        $jobId = (string) Str::uuid();

        // mark pending
        Cache::put('rekomendasi_result_' . $jobId, ['status' => 'pending'], 600);

        // dispatch job
        dispatch(new ComputeRecommendation($params, $jobId))->onQueue('default');

        return response()->json(['jobId' => $jobId]);
    }

    /**
     * Poll async job status
     */
    public function asyncStatus($jobId)
    {
        $key = 'rekomendasi_result_' . $jobId;
        $data = Cache::get($key);
        if (!$data) {
            return response()->json(['status' => 'not_found'], 404);
        }
        return response()->json($data);
    }
    public function detail(Request $request, $namaObat)
    {
        $namaObat = urldecode(trim($namaObat));

        $obat = ExcelHelper::findObatByName(
            $this->excelPath,
            $namaObat
        );

        if (!$obat) {
            return redirect()
                ->route('rekomendasi')
                ->with('error', 'Obat tidak ditemukan.');
        }


        $similarity = null;
        $reportKey = $request->input('key');

        if (is_numeric($request->input('similarity'))) {
            $similarity = round((float)$request->input('similarity'), 2);
        }

        if ($similarity === null) {
            $recommendations = session('last_recommendation_results', []);
            foreach ($recommendations as $item) {
                if (isset($item['nama']) && strtolower(trim($item['nama'])) === strtolower($namaObat)) {
                    if (isset($item['similarity_pct'])) {
                        $similarity = round((float)$item['similarity_pct'], 2);
                    } elseif (isset($item['persentase'])) {
                        $similarity = round((float)$item['persentase'], 2);
                    } elseif (isset($item['similarity'])) {
                        $similarity = round((float)$item['similarity'] > 1 ? $item['similarity'] : $item['similarity'] * 100, 2);
                    }
                    break;
                }
            }
        }

        if ($similarity === null) {
            $similarity = 0;
        }

        $obat = array_merge([

            'nama' => '-',
            'gambar' => 'default.png',
            'kategori' => '-',
            'jenis' => '-',
            'indikasi' => '-',
            'deskripsi' => '-',
            'komposisi' => '-',
            'dosis' => '-',
            'efek_samping' => '-',

        ], $obat);

        $user = session('last_recommendation', []);

        return view('user.detail_rekomendasi', [
            'obat'       => $obat,
            'similarity' => $similarity,
            'key'        => $reportKey,
            'nama'       => $user['nama'] ?? '',
            'usia'       => $user['usia'] ?? '',
            'query'      => $user['keluhan'] ?? '',
            'durasi'     => $user['durasi'] ?? '',
            'riwayat'    => $user['riwayat'] ?? '',
            'confidence' => $similarity >= 80 ? 'High'
                : ($similarity >= 60 ? 'Medium' : 'Low'),
            'display_time' => now()->format('d F Y H:i'),
        ]);
    }

    /**
     * Validasi input keluhan untuk memastikan bukan input asal-asalan
     * 
     * @param string $keluhan
     * @return array ['valid' => bool, 'message' => string]
     */
    private function validateKeluhan($keluhan)
    {
        // Minimum length
        if (strlen($keluhan) < 3) {
            return [
                'valid' => false,
                'message' => 'Keluhan terlalu pendek. Masukkan minimal 3 karakter.'
            ];
        }

        // Check word count limit (max 6 words)
        $words = array_filter(explode(' ', $keluhan));
        $wordCount = count($words);
        $maxWords = 6;

        if ($wordCount > $maxWords) {
            return [
                'valid' => false,
                'message' => "Sudah mencapai batas maksimal {$maxWords} kata. Kata saat ini: {$wordCount}"
            ];
        }

        // Check for repeated characters (like "aaaa", "hhhh")
        if (preg_match('/(.)\1{3,}/', $keluhan)) {
            return [
                'valid' => false,
                'message' => 'Keluhan tidak valid. Jangan menggunakan karakter berulang (contoh: aaaa, hhhh).'
            ];
        }

        // Count unique words
        $words = array_filter(explode(' ', $keluhan));
        $uniqueWords = array_unique($words);

        // Check if mostly repeated words (like "halo halo halo")
        if (count($words) > 2 && count($uniqueWords) == 1) {
            return [
                'valid' => false,
                'message' => 'Keluhan tidak valid. Jangan menggunakan kata yang berulang.'
            ];
        }

        // Check for common meaningless patterns
        $meaninglessPatterns = [
            '/^(halo|hai|hi|ya|yes|no|ok|okay|wkwk|haha|hihi|asdf|zxcv|qwer)+(\s+(halo|hai|hi|ya|yes|no|ok|okay|wkwk|haha|hihi|asdf|zxcv|qwer)+)*$/i',
            '/^(a|e|i|o|u|a|b|c)+(\s+(a|e|i|o|u|a|b|c)+)*$/',
            '/^[^a-z0-9]+$/i', // Only special characters
        ];

        foreach ($meaninglessPatterns as $pattern) {
            if (preg_match($pattern, $keluhan)) {
                return [
                    'valid' => false,
                    'message' => 'Keluhan tidak valid. Masukkan deskripsi yang bermakna terkait gejala atau penyakit.'
                ];
            }
        }

        // Check if input contains only numbers/special chars without letters
        if (!preg_match('/[a-z]/i', $keluhan)) {
            return [
                'valid' => false,
                'message' => 'Keluhan tidak valid. Harus mengandung minimal satu huruf.'
            ];
        }

        // Check word length - ensure not all words are too short
        $words = array_filter(explode(' ', $keluhan));
        $shortWords = array_filter($words, function ($word) {
            return strlen($word) <= 1;
        });

        if (count($shortWords) > count($words) * 0.7) { // More than 70% single chars
            return [
                'valid' => false,
                'message' => 'Keluhan tidak valid. Gunakan kata-kata yang lebih bermakna.'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Validasi semantic - memastikan keluhan terkait kesehatan/obat
     * 
     * @param string $keluhan
     * @return array ['valid' => bool, 'message' => string]
     */
    private function validateKeluhanSemantic($keluhan)
    {
        // List kata-kata kesehatan/obat yang valid dalam Bahasa Indonesia
        $healthKeywords = [
            // Gejala/Penyakit umum
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
            'batu ginjal',
            'batu empedu',
            'kolesterol',
            'hipertensi',
            'tekanan darah',
            'jantung',
            'stroke',
            'diabetes',
            'gula darah',
            'kanker',
            'tumor',

            // Sistem tubuh
            'mata',
            'telinga',
            'gigi',
            'gusi',
            'lidah',
            'mulut',
            'tenggorokan',
            'paru',
            'jantung',
            'liver',
            'hati',
            'ginjal',
            'kandung kemih',
            'usus',
            'lambung',
            'kerongkongan',
            'esofagus',
            'pankreas',
            'limpa',
            'otot',
            'tulang',
            'sendi',
            'ligamen',

            // Jenis-jenis obat
            'antibiotik',
            'antivirus',
            'antifungi',
            'ibuprofen',
            'paracetamol',
            'aspirin',
            'amoxicillin',
            'CTM',
            'antihistamin',
            'dekongestan',
            'antasida',
            'loperamid',
            'omeprazol',
            'metformin',
            'insulin',
            'atorvastatin',
            'amlodipine',
            'simvastatin',
            'lisinopril',
            'metoprolol',
            'chlorpromazine',
            'vitamin',
            'suplemen',

            // Kondisi/Status
            'alergi',
            'sensitivitas',
            'intoleransi',
            'keracunan',
            'luka bakar',
            'memar',
            'bengkak',
            'pembengkakan',
            'peradangan',
            'inflamasi',
            'jenis kulit',
            'bau mulut',
            'halitosis',
            'anemia',
            'leukosit',
            'trombosit',
            'darah tinggi',
            'darah rendah',
            'kolesterol tinggi',
            'berat badan',
            'obesitas',
            'gemuk',
            'kurus',
            'kelelahan',

            // Durasi/Waktu
            'hari',
            'minggu',
            'bulan',
            'tahun',
            'jam',
            'menit',
            'akut',
            'kronis',
            'mendadak',
            'perlahan',
            'progresif',
            'intermiten',
            'terus-menerus',
            'hilang timbul',

            // Obat keras
            'resep',
            'dokter',
            'medis',
            'terapi',
            'pengobatan',
            'treatment',
            'kesehatan',
            'penyakit',
            'kesehatan',
            'gejala',
            'keluhan',
        ];

        // Konversi keluhan ke lowercase dan split into words
        $keluhanLower = strtolower($keluhan);
        $words = preg_split('/[\s\-.,;:!?\/]+/', $keluhanLower, -1, PREG_SPLIT_NO_EMPTY);

        // Hitung berapa banyak kata yang cocok dengan health keywords
        $matchedCount = 0;
        $totalWords = count($words) ?: 1;

        foreach ($words as $word) {
            if (in_array(trim($word), $healthKeywords)) {
                $matchedCount++;
            }
        }

        // Require at least 30% of words to match health keywords
        $ratio = $matchedCount / $totalWords;

        if ($ratio < 0.3) {
            return [
                'valid' => false,
                'message' => 'Tolong isi keluhan dengan benar.'
            ];
        }

        return ['valid' => true];
    }
}
