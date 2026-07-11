<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\RecommendationValidation;

class UserController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Dashboard User
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return view('user');
    }


    /*
    |--------------------------------------------------------------------------
    | Laporan User
    |--------------------------------------------------------------------------
    */

    public function laporan()
    {
        // Fetch DB records, but exclude items deleted by user
        $dbRecords = RecommendationValidation::orderByDesc('created_at')
            ->where('user_status', '!=', 'Dihapus Pengguna')
            ->get()
            ->map(function ($item) {
                $tanggal = $item->display_timestamp_formatted ?? Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s');

                return [
                    'key' => $item->kode,
                    'tanggal' => $tanggal,
                    'nama' => $item->nama,
                    'usia' => $item->usia,
                    'keluhan' => $item->keluhan,
                    'durasi' => $item->durasi,
                    'riwayat' => $item->riwayat,
                    'obat' => $item->obat,
                    'similarity' => $this->normalizeSimilarity($item->similarity ?? $item->similarity_pct ?? $item->persentase ?? 0),
                    'confidence' => $item->confidence,
                    'user_status' => $item->user_status,
                    'admin_status' => $item->admin_status,
                    'approved_by' => $item->approved_by,
                    'approved_at' => $item->approved_at ? $item->approved_at->format('d-m-Y H:i:s') : null,
                    'pdf_ready' => $item->pdf_ready,
                ];
            })->toArray();

        $combined = [];

        // start with DB records
        foreach ($dbRecords as $r) {
            if (empty($r['key'])) continue;
            $combined[$r['key']] = $r;
        }

        // include backup JSON files (created when Admin deleted)
        try {
            $backupDir = storage_path('app/user_reports');
            if (is_dir($backupDir)) {
                foreach (glob($backupDir . DIRECTORY_SEPARATOR . '*.json') as $file) {
                    $content = @file_get_contents($file);
                    if (!$content) continue;
                    $data = @json_decode($content, true);
                    if (!is_array($data)) continue;
                    $key = $data['key'] ?? ($data['kode'] ?? null);
                    if (!$key) continue;

                    $entry = [
                        'key' => $key,
                        'tanggal' => $this->formatJakartaDate($data['tanggal'] ?? $data['created_at'] ?? null),
                        'nama' => $data['nama'] ?? '-',
                        'usia' => $data['usia'] ?? null,
                        'keluhan' => $data['keluhan'] ?? '-',
                        'durasi' => $data['durasi'] ?? null,
                        'riwayat' => $data['riwayat'] ?? null,
                        'obat' => $data['obat'] ?? null,
                        'similarity' => $this->normalizeSimilarity($data['similarity'] ?? $data['similarity_pct'] ?? $data['persentase'] ?? 0),
                        'confidence' => $data['confidence'] ?? null,
                        'user_status' => $data['user_status'] ?? 'pending',
                        'admin_status' => $data['admin_status'] ?? 'Dihapus Admin',
                        'approved_by' => $data['approved_by'] ?? null,
                        'approved_at' => $data['approved_at'] ?? null,
                        'pdf_ready' => $data['pdf_ready'] ?? false,
                    ];

                    // only add if not already present from DB
                    if (!isset($combined[$key])) {
                        $combined[$key] = $entry;
                    }
                }
            }
        } catch (\Exception $e) {
            // ignore
        }

        // include session history (user's local list), but don't override DB/backups
        $sessionHistory = session('recommendation_history', []);
        foreach ($sessionHistory as $item) {
            $key = $item['key'] ?? null;
            if (!$key) continue;
            if (!isset($combined[$key])) {
                $item['similarity'] = $this->normalizeSimilarity($item['similarity'] ?? null);
                $combined[$key] = $item;
            }
        }

        // convert to array and sort by tanggal desc
        $laporan = array_values($combined);
        usort($laporan, function ($a, $b) {
            $ta = isset($a['tanggal']) ? strtotime(str_replace('/', '-', $a['tanggal'])) : 0;
            $tb = isset($b['tanggal']) ? strtotime(str_replace('/', '-', $b['tanggal'])) : 0;
            return $tb <=> $ta;
        });

        return view('user.laporan', compact('laporan'));
    }

    private function formatJakartaDate($date)
    {
        try {
            if (!$date) {
                return Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s');
            }

            $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
            return $carbon->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s');
        } catch (\Exception $e) {
            return Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s');
        }
    }

    private function normalizeSimilarity($value)
    {
        if ($value === null || $value === '') {
            return 0.00;
        }

        if (is_string($value)) {
            $value = trim($value);
            $value = str_replace('%', '', $value);
            $value = str_replace(',', '.', $value);
        }

        if (!is_numeric($value)) {
            return 0.00;
        }

        $value = (float) $value;
        if ($value >= 0 && $value <= 1) {
            return round($value * 100, 2);
        }

        return round($value, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Hapus Pilihan Obat
    |--------------------------------------------------------------------------
    */

    public function removeRecommendation()
    {

        session()->forget([
            'selected_obat',
            'selected_medicine',
            'similarity',
            'confidence'
        ]);

        return redirect()

            ->route('rekomendasi')

            ->with(

                'success',

                'Pilihan obat berhasil dihapus.'

            );
    }

    /*
    |--------------------------------------------------------------------------
    | Pilih Ulang Rekomendasi
    |--------------------------------------------------------------------------
    */

    public function chooseAgain()
    {

        session()->forget([
            'selected_obat',
            'selected_medicine',
            'similarity',
            'confidence'
        ]);

        return redirect()->route('rekomendasi');
    }

    /*
    |--------------------------------------------------------------------------
    | Detail Laporan User
    |--------------------------------------------------------------------------
    */

    public function detailLaporan($key)
    {
        $detailModel = RecommendationValidation::where('kode', $key)->first();

        if ($detailModel) {
            $tanggal = $detailModel->display_timestamp_formatted ?? Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s');

            $detail = array_merge($detailModel->toArray(), [
                'key' => $detailModel->kode,
                'tanggal' => $tanggal,
                'similarity' => $this->normalizeSimilarity($detailModel->similarity ?? $detailModel->similarity_pct ?? $detailModel->persentase ?? 0),
                'approved_at' => $detailModel->approved_at ? Carbon::parse($detailModel->approved_at)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s') : null,
            ]);
        } else {
            $laporan = session('recommendation_history', []);
            $detail = null;
            foreach ($laporan as $item) {
                if (($item['key'] ?? '') === $key) {
                    $detail = $item;
                    break;
                }
            }
        }

        if ($detail) {
            if (isset($detail['tanggal'])) {
                $detail['tanggal'] = $this->formatJakartaDate($detail['tanggal']);
            }
            if (isset($detail['approved_at'])) {
                $detail['approved_at'] = $this->formatJakartaDate($detail['approved_at']);
            }
            if (isset($detail['similarity'])) {
                $detail['similarity'] = $this->normalizeSimilarity($detail['similarity']);
            }
        }

        if (!$detail) {
            return redirect()->route('laporan')->with('error', 'Data tidak ditemukan.');
        }

        return view('user.laporan_detail', ['detail' => $detail]);
    }

    public function deleteLaporan(Request $request, $key)
    {
        $deleted = false;

        // Allow user to delete their own laporan by kode (key)
        $item = RecommendationValidation::where('kode', $key)->first();

        if ($item) {
            $item->user_status = 'Dihapus Pengguna';
            $item->save();
            $deleted = true;
        }

        // Remove session entry (user's personal list)
        $laporan = session('recommendation_history', []);
        $updatedSession = [];
        foreach ($laporan as $entry) {
            if (($entry['key'] ?? '') === $key) {
                $deleted = true;
                continue;
            }
            $updatedSession[] = $entry;
        }

        if (count($updatedSession) !== count($laporan)) {
            session(['recommendation_history' => $updatedSession]);
        }

        // Remove backup file if present as fallback
        $backupDir = storage_path('app/user_reports');
        if (is_dir($backupDir)) {
            $backupFile = $backupDir . DIRECTORY_SEPARATOR . $key . '.json';
            if (file_exists($backupFile)) {
                @unlink($backupFile);
                $deleted = true;
            }
        }

        if ($deleted) {
            return redirect()->route('laporan')->with('success', 'Laporan berhasil dihapus dari daftar Anda.');
        }

        return redirect()->route('laporan')->with('error', 'Laporan tidak ditemukan.');
    }

    /*
    |--------------------------------------------------------------------------
    | Download PDF User
    |--------------------------------------------------------------------------
    */

    public function downloadPdf($key)
    {
        $detailModel = RecommendationValidation::where('kode', $key)->first();

        if ($detailModel) {
            $detail = array_merge($detailModel->toArray(), [
                'key' => $detailModel->kode,
                'download_time' => Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s'),
                'tanggal' => $detailModel->display_timestamp_formatted ?? Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s'),
                'approved_at' => $detailModel->approved_at ? Carbon::parse($detailModel->approved_at)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s') : null,
            ]);
        } else {
            $laporan = session('recommendation_history', []);
            $detail = null;
            foreach ($laporan as $item) {
                if (($item['key'] ?? '') === $key) {
                    $detail = $item;
                    break;
                }
            }
            if ($detail) {
                $detail['download_time'] = Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s');
            }
        }

        if (!$detail) {
            return redirect()
                ->route('laporan')
                ->with(
                    'error',
                    'Laporan tidak ditemukan.'
                );
        }

        return view(
            'user.laporan-pdf',
            [
                'detail' => $detail
            ]
        );
    }

    public function submitSelectedObat(Request $request)
    {
        $request->validate([
            'selected_obat' => 'nullable|string',
            'obat' => 'nullable|string',
            'similarity' => 'nullable|string',
            'confidence' => 'nullable|string',
            'nama' => 'nullable|string',
            'usia' => 'nullable|integer',
            'keluhan' => 'nullable|string',
            'durasi' => 'nullable|string',
            'riwayat' => 'nullable|string'
        ]);

        $selectedObat = trim($request->input('selected_obat') ?? $request->input('obat') ?? session('selected_medicine'));
        $similarity = $this->normalizeSimilarity($request->input('similarity') ?? session('similarity') ?? 0);
        $confidence = $request->input('confidence') ?? session('confidence') ?? ($similarity >= 80 ? 'High' : ($similarity >= 60 ? 'Medium' : 'Low'));

        if (empty($selectedObat)) {
            $errorMessage = 'Tidak ada obat yang dipilih.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 422);
            }
            return back()->with('error', $errorMessage)->withInput();
        }

        $key = Str::uuid()->toString();

        $history = session('recommendation_history', []);

        $entry = [
            'key' => $key,
            'tanggal' => Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s'),
            'nama' => $request->nama,
            'usia' => $request->usia,
            'keluhan' => $request->keluhan,
            'durasi' => $request->durasi,
            'riwayat' => $request->riwayat,
            'obat' => $selectedObat,
            'similarity' => round((float)$similarity, 2),
            'confidence' => $confidence,
            'user_status' => 'pending',
            'admin_status' => 'Menunggu Validasi',
            'pdf_ready' => false,
        ];

        $history[] = $entry;

        session([
            'recommendation_history' => $history,
        ]);

        // Normalize confidence to numeric (remove percent sign or other chars), keep flow unchanged
        $normalizedConfidence = null;
        if (isset($entry['confidence'])) {
            $normalizedConfidence = preg_replace('/[^0-9.]/', '', (string)$entry['confidence']);
            if ($normalizedConfidence === '') {
                $normalizedConfidence = null;
            } else {
                $normalizedConfidence = (float) $normalizedConfidence;
            }
        }

        $nama = trim((string)($request->input('nama') ?? $entry['nama'] ?? ''));
        $keluhan = trim((string)($request->input('keluhan') ?? $entry['keluhan'] ?? ''));
        $usia = $request->input('usia') ?? $entry['usia'] ?? null;

        RecommendationValidation::create([
            'kode' => $key,
            'nama' => $nama !== '' ? $nama : 'Pengguna Tidak Diketahui',
            'usia' => $usia,
            'keluhan' => $keluhan !== '' ? $keluhan : 'Tidak ada keluhan',
            'durasi' => $request->input('durasi') ?? $entry['durasi'] ?? null,
            'riwayat' => $request->input('riwayat') ?? $entry['riwayat'] ?? null,
            'obat' => $entry['obat'],
            'similarity' => $entry['similarity'],
            'recommended_meds' => session('last_recommendation_results', []),
            'confidence' => $normalizedConfidence,
            'status' => 'pending',
            'user_status' => 'pending',
            'admin_status' => 'Menunggu Validasi',
            'pdf_ready' => false,
            'created_at' => Carbon::now('Asia/Jakarta'),
            'updated_at' => Carbon::now('Asia/Jakarta'),
        ]);

        // clear temporary selection and previous recommendation cache after validasi submit
        session()->forget(['selected_medicine', 'similarity', 'confidence', 'last_recommendation', 'last_recommendation_results']);

        $successMessage = 'Validasi berhasil dikirim!';
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $successMessage, 'key' => $key]);
        }

        return redirect()->route('laporan')->with('success', $successMessage);
    }

    /**
     * Store a quick selection into session (preserve current UX flow).
     */
    public function selectObat(Request $request)
    {
        $request->validate([
            'selected_obat' => 'required|string',
            'similarity' => 'nullable|numeric',
            'confidence' => 'nullable|string',
        ]);

        session([
            'selected_medicine' => $request->selected_obat,
            'similarity' => $request->similarity ?? 0,
            'confidence' => $request->confidence ?? ($request->similarity >= 80 ? 'High' : ($request->similarity >= 60 ? 'Medium' : 'Low')),
        ]);

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Obat berhasil dipilih']);
        }

        // Redirect back for regular form submissions
        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | Status Validasi User
    |--------------------------------------------------------------------------
    */

    public function status()
    {

        $history = session(

            'recommendation_history',

            []

        );

        $total = count($history);

        $pending = 0;

        $approved = 0;

        $rejected = 0;

        foreach ($history as $item) {

            if (($item['user_status'] ?? '') == 'pending') {

                $pending++;
            }

            if (($item['user_status'] ?? '') == 'approved') {

                $approved++;
            }

            if (($item['user_status'] ?? '') == 'rejected') {

                $rejected++;
            }
        }

        return view(

            'status_user',

            [

                'history' => $history,

                'total' => $total,

                'pending' => $pending,

                'approved' => $approved,

                'rejected' => $rejected

            ]

        );
    }
    /*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

    public function home()
    {
        return view('home');
    }


    /*
|--------------------------------------------------------------------------
| Riwayat
|--------------------------------------------------------------------------
*/

    public function riwayat()
    {
        $history = session('recommendation_history', []);

        return view('user.riwayat', [
            'history' => $history
        ]);
    }

    /*
|--------------------------------------------------------------------------
| Detail Riwayat
|--------------------------------------------------------------------------
*/

    public function detailRiwayat($id)
    {
        $history = session('recommendation_history', []);

        $detail = null;

        foreach ($history as $item) {

            if (($item['key'] ?? '') == $id) {
                $detail = $item;
                break;
            }
        }

        if (!$detail) {
            abort(404);
        }

        return view('user.detail_riwayat', [
            'detail' => $detail
        ]);
    }
}
