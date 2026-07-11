<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\ExcelHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\RecommendationValidation;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DATA LOGIN ADMIN
    |--------------------------------------------------------------------------
    */
    // Admin credentials are read from environment for secrecy. Defaults kept for local convenience.
    private $adminEmail;
    private $adminPassword;

    public function __construct()
    {
        $this->adminEmail = env('ADMIN_EMAIL', 'admin24@gmail.com');
        $this->adminPassword = env('ADMIN_PASSWORD', 'admin123');
    }

    /*
    |--------------------------------------------------------------------------
    | FORM LOGIN
    |--------------------------------------------------------------------------
    */
    public function loginForm()
    {
        return view('admin.login');
    }

    /*
    |--------------------------------------------------------------------------
    | PROSES LOGIN
    |--------------------------------------------------------------------------
    */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $adminEmail = session('admin_settings.email', $this->adminEmail);
        $adminPassword = session('admin_settings.password', $this->adminPassword);

        if (
            $request->email === $adminEmail &&
            $request->password === $adminPassword
        ) {

            session([
                'admin_logged_in' => true,
                'admin_email' => $request->email
            ]);

            /*
            |--------------------------------------------------------------------------
            | LOAD DATA PRODUK DARI EXCEL
            |--------------------------------------------------------------------------
            */
            if (!session()->has('produk_data')) {

                $produkExcel = ExcelHelper::readObatData(
                    ExcelHelper::resolveExcelPath()
                );

                session([
                    'produk_data' => $produkExcel
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | DATA USER
            |--------------------------------------------------------------------------
            */
            if (!session()->has('user_data')) {

                session([
                    'user_data' => []
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | DATA REKOMENDASI
            |--------------------------------------------------------------------------
            */
            if (!session()->has('recommendation_history')) {

                session([
                    'recommendation_history' => []
                ]);
            }

            return redirect()->route('admin.dashboard');
        }

        return back()
            ->withErrors([
                'login' => 'Email atau password salah'
            ])
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        if (!session('admin_logged_in')) {

            return redirect()
                ->route('admin.login');
        }
        // Prefer authoritative counts from the database so dashboard
        // metrics do not disappear when session data is cleared.
        $dbProdukCount = Product::count();
        $dbUserCount = User::count();
        $dbRekomCount = RecommendationValidation::count();

        // Include any legacy session data that has not yet been migrated to DB
        $sessionProduk = session('produk_data', []);
        $sessionUsers = session('user_data', []);
        $sessionRekom = session('recommendation_history', []);

        // For products, try to avoid double-counting by checking names against DB
        $extraProduk = 0;
        if (!empty($sessionProduk)) {
            foreach ($sessionProduk as $p) {
                $name = trim(strtolower($p['nama'] ?? ''));
                if ($name === '') continue;
                $exists = Product::whereRaw('LOWER(TRIM(nama)) = ?', [$name])->exists();
                if (!$exists) $extraProduk++;
            }
        }

        // For users, session entries may be only names; count unique session names
        $extraUsers = 0;
        if (!empty($sessionUsers)) {
            $names = array_filter(array_map(fn($u) => strtolower(trim($u['nama'] ?? '')), $sessionUsers));
            $names = array_values(array_unique($names));
            foreach ($names as $n) {
                if ($n === '') continue;
                // if a DB user with same name exists, skip counting it as extra
                $exists = User::whereRaw('LOWER(TRIM(name)) = ?', [$n])->exists();
                if (!$exists) $extraUsers++;
            }
        }

        // For recommendation history in session, count entries that are not present in DB (by key)
        $extraRekom = 0;
        if (!empty($sessionRekom)) {
            foreach ($sessionRekom as $r) {
                $key = $r['key'] ?? null;
                if (!$key) {
                    $extraRekom++;
                    continue;
                }
                $exists = RecommendationValidation::where('kode', $key)->exists();
                if (!$exists) $extraRekom++;
            }
        }

        $totalProduk = $dbProdukCount + $extraProduk;
        $totalUser = $dbUserCount + $extraUsers;
        $totalRekomendasi = $dbRekomCount + $extraRekom;

        $statusWebsite = session(
            'status_website',
            'Aktif'
        );

        return view(
            'admin.dashboard',
            compact(
                'totalProduk',
                'totalUser',
                'totalRekomendasi',
                'statusWebsite'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    public function logout()
    {
        session()->forget('admin_logged_in');
        session()->forget('admin_email');

        return redirect()->route('admin.login');
    }

    /*
    |--------------------------------------------------------------------------
    | HALAMAN PRODUK
    |--------------------------------------------------------------------------
    */
    public function produk(\Illuminate\Http\Request $request)
    {
        if (!session('admin_logged_in')) {

            return redirect()
                ->route('admin.login');
        }

        // If DB has no products yet, seed from Excel for initial data.
        if (Product::count() === 0) {
            $produkExcel = ExcelHelper::readObatData(
                ExcelHelper::resolveExcelPath()
            );

            foreach ($produkExcel as $row) {
                // avoid empty names
                if (trim($row['nama'] ?? '') === '') continue;

                Product::create([
                    'nama' => $row['nama'] ?? '-',
                    'kategori' => $row['kategori'] ?? null,
                    'jenis' => $row['jenis'] ?? null,
                    'deskripsi' => $row['deskripsi'] ?? null,
                    'indikasi' => $row['indikasi'] ?? null,
                    'komposisi' => $row['komposisi'] ?? null,
                    'dosis' => $row['dosis'] ?? null,
                    'efek_samping' => $row['efek_samping'] ?? null,
                    'kontraindikasi' => $row['kontraindikasi'] ?? null,
                    'harga' => ExcelHelper::parsePrice($row['harga'] ?? ''),
                ]);
            }
        }

        // Migrate legacy session products if present
        $sessionProduk = session('produk_data', []);
        if (!empty($sessionProduk)) {
            foreach ($sessionProduk as $p) {
                $name = trim($p['nama'] ?? '');
                if ($name === '') continue;

                $exists = Product::whereRaw('LOWER(TRIM(nama)) = ?', [strtolower($name)])->exists();
                if ($exists) continue;

                Product::create([
                    'nama' => $p['nama'] ?? $name,
                    'kategori' => $p['kategori'] ?? null,
                    'jenis' => $p['jenis'] ?? null,
                    'deskripsi' => $p['deskripsi'] ?? null,
                    'indikasi' => $p['indikasi'] ?? null,
                    'komposisi' => $p['komposisi'] ?? null,
                    'dosis' => $p['dosis'] ?? null,
                    'efek_samping' => $p['efek_samping'] ?? null,
                    'kontraindikasi' => $p['kontraindikasi'] ?? null,
                    'harga' => ExcelHelper::parsePrice($p['harga'] ?? ''),
                ]);
            }

            session()->forget('produk_data');
        }

        // Build base query and compute stats
        $baseQuery = Product::query();
        $allProduk = $baseQuery->orderBy('nama')->get()->toArray();

        $totalProduk = count($allProduk);
        $categoryCounts = [];
        $categories = [];
        foreach ($allProduk as $it) {
            $cat = $it['kategori'] ?? 'Obat Umum';
            $categories[] = $cat;
            $categoryCounts[$cat] = ($categoryCounts[$cat] ?? 0) + 1;
        }
        ksort($categoryCounts);
        $categories = array_values(array_unique($categories));

        // Apply filters for display
        $queryText = trim($request->query('q', ''));
        $filterKategori = $request->query('kategori', '');

        $displayQuery = Product::orderBy('nama');
        if ($queryText !== '') {
            $displayQuery->where(function ($q) use ($queryText) {
                // match nama by prefix (starts with typed letters)
                $q->where('nama', 'like', "{$queryText}%")
                    ->orWhere('deskripsi', 'like', "%{$queryText}%")
                    ->orWhere('indikasi', 'like', "%{$queryText}%");
            });
        }
        if ($filterKategori !== '') {
            $displayQuery->where('kategori', $filterKategori);
        }

        $produk = $displayQuery->get()->toArray();

        return view('admin.produk', compact('produk', 'totalProduk', 'categories', 'categoryCounts', 'queryText', 'filterKategori'));
    }

    /**
     * Import products from an uploaded Excel file or from storage/imports.
     * This operation backs up current products, upserts new rows by name,
     * and clears legacy session produk_data to avoid conflicts.
     */
    public function importProduk(\Illuminate\Http\Request $request)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        // Backup existing products table to storage/app/backups
        try {
            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            $backupFile = $backupDir . DIRECTORY_SEPARATOR . 'products_backup_' . date('Ymd_His') . '.json';
            file_put_contents($backupFile, Product::all()->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (\Exception $e) {
            $backupFile = null;
        }

        // Determine Excel file path: prefer uploaded file, then storage/imports, then base path
        $filePath = null;
        if ($request->hasFile('file')) {
            $uploaded = $request->file('file');
            $destDir = storage_path('app/imports');
            if (!is_dir($destDir)) mkdir($destDir, 0755, true);
            $dest = $destDir . DIRECTORY_SEPARATOR . $uploaded->getClientOriginalName();
            $uploaded->move($destDir, basename($dest));
            $filePath = $dest;
        } else {
            $candidates = [
                storage_path('app/imports/Daftar obat Keras dan obat Umum.xlsx'),
                base_path('Daftar obat Keras dan obat Umum.xlsx'),
                ExcelHelper::resolveExcelPath(),
            ];
            foreach ($candidates as $c) {
                if (file_exists($c)) {
                    $filePath = $c;
                    break;
                }
            }
        }

        if (!$filePath || !file_exists($filePath)) {
            return redirect()->route('admin.produk')->with('error', 'File Excel untuk import tidak ditemukan. Upload atau taruh file di storage/app/imports.');
        }

        $rows = ExcelHelper::readObatData($filePath);

        // Replace the existing product catalog with the latest Excel data.
        Product::withTrashed()->forceDelete();
        session()->forget('produk_data');

        $processed = 0;
        foreach ($rows as $row) {
            $name = trim($row['nama'] ?? '');
            if ($name === '') continue;

            $data = [
                'nama' => $name,
                'kategori' => $row['kategori'] ?? ($row['kategori_raw'] ?? 'Obat Umum'),
                'jenis' => $row['jenis'] ?? null,
                'deskripsi' => $row['deskripsi'] ?? null,
                'indikasi' => $row['indikasi'] ?? null,
                'komposisi' => $row['komposisi'] ?? null,
                'dosis' => $row['dosis'] ?? null,
                'efek_samping' => $row['efek_samping'] ?? null,
                'kontraindikasi' => $row['kontraindikasi'] ?? null,
                'harga' => ExcelHelper::parsePrice($row['harga'] ?? ''),
            ];

            $lowerName = strtolower(trim(preg_replace('/[^a-z0-9]+/', ' ', $name)));
            $existing = Product::whereRaw('LOWER(TRIM(nama)) = ?', [$lowerName])->first();

            if (!$existing) {
                $existing = Product::where(function ($query) use ($lowerName) {
                    $query->whereRaw('LOWER(TRIM(nama)) = ?', [$lowerName])
                        ->orWhereRaw('? LIKE CONCAT("%", LOWER(TRIM(nama)), "%")', [$lowerName])
                        ->orWhereRaw('LOWER(TRIM(nama)) LIKE ?', ["%{$lowerName}%"]);
                })->first();
            }

            if (!$existing && !empty($row['deskripsi'])) {
                $existing = Product::whereRaw('LOWER(TRIM(deskripsi)) = ?', [strtolower(trim($row['deskripsi']))])->first();
            }
            if (!$existing && !empty($row['indikasi'])) {
                $existing = Product::whereRaw('LOWER(TRIM(indikasi)) = ?', [strtolower(trim($row['indikasi']))])->first();
            }

            if ($existing) {
                $existing->update($data);
            } else {
                Product::create($data);
            }

            $processed++;
        }

        // Clear legacy session product list to avoid conflicts
        session()->forget('produk_data');

        $msg = "Import selesai. Produk diproses: {$processed}.";
        if (!empty($backupFile)) $msg .= " Backup: " . basename($backupFile);

        return redirect()->route('admin.produk')->with('success', $msg);
    }

    /*
    |--------------------------------------------------------------------------
    | FORM TAMBAH PRODUK
    |--------------------------------------------------------------------------
    */
    public function tambahProduk()
    {
        if (!session('admin_logged_in')) {

            return redirect()
                ->route('admin.login');
        }

        $categories = Product::whereNotNull('kategori')
            ->orderBy('kategori')
            ->distinct()
            ->pluck('kategori')
            ->filter()
            ->values()
            ->toArray();

        return view('admin.tambah-produk', compact('categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN PRODUK
    |--------------------------------------------------------------------------
    */
    public function storeProduk(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required',
                'kategori' => ['required', Rule::in(['Obat Umum', 'Obat Keras'])],
                'harga' => 'required',
                'jenis' => 'nullable|string',
                'indikasi' => 'nullable|string',
                'komposisi' => 'nullable|string',
                'dosis' => 'nullable|string',
                'efek_samping' => 'nullable|string',
                'kontraindikasi' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            throw $e;
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA PRODUK
        |--------------------------------------------------------------------------
        */

        Product::create([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'harga' => $request->harga,
            'deskripsi' => $request->deskripsi,
            'jenis' => $request->jenis,
            'indikasi' => $request->indikasi,
            'komposisi' => $request->komposisi,
            'dosis' => $request->dosis,
            'efek_samping' => $request->efek_samping,
            'kontraindikasi' => $request->kontraindikasi,
        ]);

        // If AJAX/json request, return json payload
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Produk berhasil ditambahkan'], 201);
        }

        return redirect('/admin/produk')
            ->with(
                'success',
                'Produk berhasil ditambahkan'
            );
    }

    /**
     * Return partial list of produk for AJAX updates
     */
    public function produkList(\Illuminate\Http\Request $request)
    {
        if (!session('admin_logged_in')) {
            return response('Unauthorized', 401);
        }

        $queryText = trim($request->query('q', ''));
        $filterKategori = $request->query('kategori', '');

        $displayQuery = Product::orderBy('nama');
        if ($queryText !== '') {
            $displayQuery->where(function ($q) use ($queryText) {
                $q->where('nama', 'like', "{$queryText}%")
                    ->orWhere('deskripsi', 'like', "%{$queryText}%")
                    ->orWhere('indikasi', 'like', "%{$queryText}%");
            });
        }
        if ($filterKategori !== '') {
            $displayQuery->where('kategori', $filterKategori);
        }

        $produk = $displayQuery->get()->toArray();

        return view('admin._produk_list', compact('produk'));
    }

    /*
    |--------------------------------------------------------------------------
    | FORM EDIT PRODUK
    |--------------------------------------------------------------------------
    */
    public function editProduk($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return redirect('/admin/produk')->with('error', 'Produk tidak ditemukan');
        }

        $selected = $product->toArray();
        $categories = Product::whereNotNull('kategori')
            ->orderBy('kategori')
            ->distinct()
            ->pluck('kategori')
            ->filter()
            ->values()
            ->toArray();

        return view('admin.edit-produk', compact('selected', 'id', 'categories'));
    }

    public function detailProduk($id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $product = Product::find($id);
        if (!$product) {
            return redirect('/admin/produk')->with('error', 'Produk tidak ditemukan');
        }

        $item = $product->toArray();

        return view('admin.detail-produk', compact('item'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PRODUK
    |--------------------------------------------------------------------------
    */
    public function updateProduk(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'kategori' => ['required', Rule::in(['Obat Umum', 'Obat Keras'])],
            'harga' => 'required',
            'jenis' => 'nullable|string',
            'indikasi' => 'nullable|string',
            'komposisi' => 'nullable|string',
            'dosis' => 'nullable|string',
            'efek_samping' => 'nullable|string',
            'kontraindikasi' => 'nullable|string',
        ]);

        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA PRODUK
        |--------------------------------------------------------------------------
        */
        $product = Product::find($id);
        if (!$product) {
            return redirect('/admin/produk')->with('error', 'Produk tidak ditemukan');
        }

        $product->nama = $request->nama;
        $product->kategori = $request->kategori;
        $product->harga = $request->harga;
        $product->deskripsi = $request->deskripsi;
        $product->jenis = $request->jenis;
        $product->indikasi = $request->indikasi;
        $product->komposisi = $request->komposisi;
        $product->dosis = $request->dosis;
        $product->efek_samping = $request->efek_samping;
        $product->kontraindikasi = $request->kontraindikasi;
        $product->save();

        return redirect('/admin/produk')
            ->with(
                'success',
                'Produk berhasil diupdate'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | HAPUS PRODUK
    |--------------------------------------------------------------------------
    */
    public function hapusProduk($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return redirect('/admin/produk')->with('success', 'Produk berhasil dihapus');
        }

        return redirect('/admin/produk')->with('error', 'Produk tidak ditemukan');
    }

    /*
    |--------------------------------------------------------------------------
    | PENGATURAN
    |--------------------------------------------------------------------------
    */
    public function pengaturan()
    {
        if (!session('admin_logged_in')) {

            return redirect()
                ->route('admin.login');
        }

        return view('admin.pengaturan');
    }

    public function updatePengaturan(Request $request)
    {
        if (!session('admin_logged_in')) {

            return redirect()
                ->route('admin.login');
        }

        $request->validate([
            'nama_website' => 'required|string|max:255',
            'email_admin' => 'required|email|max:255',
            'password_admin' => 'nullable|string|min:6|confirmed',
            'status_website' => 'required|in:Aktif,Tidak Aktif',
        ]);

        session([
            'nama_website' => $request->nama_website,
            'email_admin' => $request->email_admin,
            'status_website' => $request->status_website,
            'admin_settings' => [
                'email' => $request->email_admin,
                'password' => $request->password_admin ?: session('admin_settings.password', $this->adminPassword),
            ],
        ]);

        return back()->with(
            'success',
            'Pengaturan admin berhasil disimpan.'
        );
    }

    /*
|--------------------------------------------------------------------------
| LAPORAN ADMIN
|--------------------------------------------------------------------------
*/
    public function laporan()
    {
        if (!session('admin_logged_in')) {
            return redirect()
                ->route('admin.login');
        }
        // Migrate any legacy session-based recommendation_history into DB
        $legacy = session('recommendation_history', []);
        if (!empty($legacy)) {
            foreach ($legacy as $entry) {
                if (empty($entry['key'])) {
                    continue;
                }
                $exists = RecommendationValidation::where('kode', $entry['key'])->first();
                if ($exists) {
                    continue;
                }

                try {
                    $createdAt = isset($entry['tanggal']) ? \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $entry['tanggal']) : now();
                } catch (\Exception $e) {
                    $createdAt = now();
                }

                // Normalize confidence if present (strip percent sign etc.)
                $normalizedConfidence = null;
                if (isset($entry['confidence'])) {
                    $normalizedConfidence = preg_replace('/[^0-9.]/', '', (string)$entry['confidence']);
                    if ($normalizedConfidence === '') {
                        $normalizedConfidence = null;
                    } else {
                        $normalizedConfidence = (float) $normalizedConfidence;
                    }
                }

                RecommendationValidation::create([
                    'kode' => $entry['key'],
                    // ensure nama is not null to satisfy DB NOT NULL constraint
                    'nama' => trim($entry['nama'] ?? '') !== '' ? $entry['nama'] : 'Pengguna Tidak Diketahui',
                    'usia' => $entry['usia'] ?? null,
                    'keluhan' => trim($entry['keluhan'] ?? '') !== '' ? $entry['keluhan'] : 'Tidak ada keluhan',
                    'durasi' => $entry['durasi'] ?? null,
                    'riwayat' => $entry['riwayat'] ?? null,
                    'obat' => $entry['obat'] ?? null,
                    'similarity' => $entry['similarity'] ?? 0,
                    'confidence' => $normalizedConfidence,
                    'status' => 'pending',
                    'user_status' => $entry['user_status'] ?? 'pending',
                    'admin_status' => $entry['admin_status'] ?? 'Menunggu Validasi',
                    'pdf_ready' => $entry['pdf_ready'] ?? false,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }

            // clear legacy session data after migration
            session()->forget('recommendation_history');
            session()->forget('validation_approvals');
        }

        $records = RecommendationValidation::where('admin_status', 'Disetujui Admin')
            ->orderByDesc('created_at')
            ->get();

        $laporan = $records->map(function ($item) {
            $tanggal = $item->display_timestamp_formatted ?? now()->format('d-m-Y H:i:s');

            return [
                'id' => $item->id,
                'key' => $item->kode,
                'tanggal' => $tanggal,
                'nama' => $item->nama,
                'usia' => $item->usia,
                'keluhan' => $item->keluhan,
                'durasi' => $item->durasi,
                'riwayat' => $item->riwayat,
                'obat' => $item->obat,
                'similarity' => $item->similarity,
                'confidence' => $item->confidence,
                'user_status' => $item->user_status,
                'admin_status' => $item->admin_status,
                'approved_by' => $item->approved_by,
                'approved_at' => $item->approved_at ? $item->approved_at->format('d-m-Y H:i:s') : null,
                'pdf_ready' => $item->pdf_ready,
            ];
        })->toArray();

        return view('admin.laporan', compact('laporan'));
    }

    public function detailLaporan($key)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $detailModel = RecommendationValidation::where('kode', $key)->first();

        if (!$detailModel) {
            return redirect()->route('admin.laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        $tanggal = $detailModel->display_timestamp_formatted ?? now()->format('d-m-Y H:i:s');

        $detail = array_merge($detailModel->toArray(), [
            'key' => $detailModel->kode,
            'tanggal' => $tanggal,
            'approved_at' => $detailModel->approved_at ? $detailModel->approved_at->format('d-m-Y H:i:s') : null,
        ]);

        return view('admin.laporan-detail', compact('detail'));
    }

    public function editLaporan($index)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $reportData = session('recommendation_history', []);

        if (!isset($reportData[$index])) {
            return redirect()->route('admin.laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        $selected = $reportData[$index];

        // Ambil daftar keluhan historis untuk suggestions di dropdown
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
            $keluhanOptions = collect([]);
        }

        return view('admin.laporan-edit', compact('selected', 'index'))->with('keluhanOptions', $keluhanOptions);
    }

    public function updateLaporan(Request $request, $index)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'keluhan' => 'required|string|max:1000',
            'durasi' => 'nullable|string|max:255',
            'riwayat' => 'nullable|string|max:1000',
        ]);

        $reportData = session('recommendation_history', []);

        if (!isset($reportData[$index])) {
            return redirect()->route('admin.laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        $reportData[$index] = array_merge($reportData[$index], [
            'nama' => $data['nama'],
            'keluhan' => $data['keluhan'],
            'durasi' => $data['durasi'] ?: '-',
            'riwayat' => $data['riwayat'] ?: '-',
        ]);

        session(['recommendation_history' => $reportData]);

        return redirect()->route('admin.laporan')->with('success', 'Laporan berhasil diperbarui.');
    }

    public function deleteLaporan(Request $request, $id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        // If there is a DB record, mark it as deleted for admin only.
        $item = RecommendationValidation::find($id);

        if ($item) {
            // Create storage folder for backups if not exists
            try {
                $backupDir = storage_path('app/user_reports');
                if (!is_dir($backupDir)) {
                    mkdir($backupDir, 0755, true);
                }

                $backupFile = $backupDir . DIRECTORY_SEPARATOR . ($item->kode ?: $item->id) . '.json';

                $backupData = array_merge($item->toArray(), [
                    'key' => $item->kode,
                    'tanggal' => $item->display_timestamp_formatted ?? Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s'),
                ]);

                file_put_contents($backupFile, json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } catch (\Exception $e) {
                // ignore backup failure but continue to delete DB record
            }

            // Permanently delete DB record
            $item->delete();

            return redirect()->route('admin.laporan')->with('success', 'Laporan dihapus permanen dari database. Salinan disimpan agar tetap dapat diakses oleh pengguna.');
        }

        return redirect()->route('admin.laporan')->with('error', 'Laporan tidak ditemukan di basis data.');
    }

    /**
     * Delete a backup JSON file for a report previously removed from DB.
     */
    public function deleteBackup(Request $request, $key)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $backupFile = storage_path('app/user_reports/' . $key . '.json');
        if (file_exists($backupFile)) {
            try {
                unlink($backupFile);
                return redirect()->route('admin.laporan')->with('success', 'Backup laporan berhasil dihapus.');
            } catch (\Exception $e) {
                return redirect()->route('admin.laporan')->with('error', 'Gagal menghapus backup laporan.');
            }
        }

        return redirect()->route('admin.laporan')->with('error', 'Backup laporan tidak ditemukan.');
    }

    /**
     * Bulk delete laporan (DB records or backup files). Expects `selected[]` array with values like "id:123" or "key:uuid".
     */
    public function bulkDelete(Request $request)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $selected = $request->input('selected', []);
        if (!is_array($selected) || empty($selected)) {
            return redirect()->route('admin.laporan')->with('error', 'Tidak ada laporan terpilih.');
        }

        $deleted = 0;
        foreach ($selected as $val) {
            if (strpos($val, 'id:') === 0) {
                $id = intval(substr($val, 3));
                $item = RecommendationValidation::find($id);
                if ($item) {
                    // attempt backup as before
                    try {
                        $backupDir = storage_path('app/user_reports');
                        if (!is_dir($backupDir)) {
                            mkdir($backupDir, 0755, true);
                        }
                        $backupFile = $backupDir . DIRECTORY_SEPARATOR . ($item->kode ?: $item->id) . '.json';
                        $backupData = array_merge($item->toArray(), ['key' => $item->kode]);
                        file_put_contents($backupFile, json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    } catch (\Exception $e) {
                        // ignore
                    }
                    $item->delete();
                    $deleted++;
                }
            } elseif (strpos($val, 'key:') === 0) {
                $key = substr($val, 4);
                $backupFile = storage_path('app/user_reports/' . $key . '.json');
                if (file_exists($backupFile)) {
                    try {
                        unlink($backupFile);
                        $deleted++;
                    } catch (\Exception $e) {
                        // ignore
                    }
                }
            }
        }

        if ($deleted > 0) {
            return redirect()->route('admin.laporan')->with('success', "Berhasil menghapus {$deleted} laporan.");
        }

        return redirect()->route('admin.laporan')->with('error', 'Tidak ada laporan yang berhasil dihapus.');
    }

    public function validasi()
    {
        if (!session('admin_logged_in')) {
            return redirect()
                ->route('admin.login');
        }

        $validationHistory = collect(session('recommendation_history', []))
            ->filter(
                fn($item) => ($item['admin_status'] ?? '') === 'Menunggu Validasi'
            )
            ->values()
            ->all();

        return view('admin.validasi', compact('validationHistory'));
    }

    public function deleteValidation($id)
    {
        $history = session('recommendation_history', []);

        foreach ($history as $index => $item) {

            if (($item['key'] ?? '') == $id) {

                unset($history[$index]);

                break;
            }
        }

        session([
            'recommendation_history' => array_values($history)
        ]);

        return redirect()
            ->route('admin.validasi')
            ->with(
                'success',
                'Data validasi berhasil dihapus.'
            );
    }

    public function validasiDetail($key)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $validationHistory = session('recommendation_history', []);
        $selected = null;

        foreach ($validationHistory as $item) {
            if (($item['key'] ?? '') === $key) {
                $selected = $item;
                break;
            }
        }

        if (!$selected) {
            return redirect()->route('admin.validasi')->with('error', 'Data validasi tidak ditemukan');
        }

        $recommendedProduk = $this->buildRecommendedProducts($selected);

        return view('admin.validasi-detail', compact('selected', 'recommendedProduk'));
    }

    public function validasiApprove(Request $request, $key)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $data = $request->validate([
            'approved' => 'nullable|array',
            'approved.*' => 'string',
            'conditions' => 'nullable|string|max:2000',
            'status' => 'required|in:Disetujui,Ditolak',
        ]);

        $validationHistory = session('recommendation_history', []);
        $found = false;

        foreach ($validationHistory as $idx => $item) {
            if (($item['key'] ?? '') === $key) {
                $validationHistory[$idx]['status'] = $data['status'];
                $validationHistory[$idx]['admin_status'] =
                    $data['status'] == 'Disetujui' ? 'Disetujui Admin' : 'Ditolak Admin';
                $validationHistory[$idx]['user_status'] =
                    $data['status'] == 'Disetujui' ? 'approved' : 'rejected';
                $validationHistory[$idx]['pdf_ready'] =
                    $data['status'] === 'Disetujui';
                $validationHistory[$idx]['admin_conditions'] =
                    $data['conditions'] ?? '';
                $validationHistory[$idx]['approved_meds'] =
                    $data['approved'] ?? [];
                $found = true;
                break;
            }
        }

        if (!$found) {
            return redirect()->route('admin.validasi')->with('error', 'Data validasi tidak ditemukan');
        }

        session([
            'recommendation_history' => $validationHistory
        ]);

        $approvals = session('validation_approvals', []);
        $approvals[$key] = [

            'approved' => $data['approved'] ?? [],

            'conditions' => $data['conditions'] ?? '',

            'status' => $data['status'],

            'admin_status' => $validationHistory[$idx]['admin_status'],

            'approved_at' => now()->format('Y-m-d H:i:s'),

        ];

        session(['validation_approvals' => $approvals]);

        return redirect()->route('admin.validasi')->with('success', 'Konfirmasi validasi tersimpan');
    }

    private function buildRecommendedProducts(array $validation): array
    {
        $produk = Product::all()->toArray();

        $queryText = implode(' ', array_filter([
            $validation['keluhan'] ?? '',
            $validation['riwayat'] !== '-' ? $validation['riwayat'] : '',
            $validation['durasi'] !== '-' ? $validation['durasi'] : '',
        ]));

        if (trim($queryText) === '') {
            return [];
        }

        $queryTokens = $this->tokenize($queryText);
        $documentTokens = [];

        foreach ($produk as $index => $item) {
            $documentTokens[$index] = $this->tokenize($this->buildDocumentText($item));
        }

        $idf = $this->computeIdf($documentTokens);
        $queryVector = $this->computeTfIdfVector($queryTokens, $idf);
        $targetCategory = $this->detectTargetCategory($queryText);

        $recommended = [];

        foreach ($produk as $index => $item) {
            $docVector = $this->computeTfIdfVector($documentTokens[$index], $idf);
            $similarity = $this->cosineSimilarity($queryVector, $docVector);

            if ($similarity <= 0) {
                continue;
            }

            if ($targetCategory === 'Obat Keras' && ($item['kategori'] ?? '') === 'Obat Umum') {
                $similarity *= 0.85;
            } elseif ($targetCategory === 'Obat Umum' && ($item['kategori'] ?? '') === 'Obat Keras') {
                $similarity *= 0.90;
            } elseif ($targetCategory === ($item['kategori'] ?? '')) {
                $similarity *= 1.05;
            }

            $similarity = max(0, min(1, $similarity));
            $percentage = (int) round($similarity * 100);
            if ($percentage < 10) {
                continue;
            }

            $item['score'] = $similarity;
            $item['similarity_pct'] = $percentage;
            $recommended[] = $item;
        }

        usort($recommended, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $recommended;
    }

    private function tokenize(string $text): array
    {
        $text = preg_replace('/[^\p{L}0-9]+/u', ' ', strtolower($text));
        $tokens = array_filter(
            preg_split('/\s+/u', $text),
            fn($word) => mb_strlen($word) >= 2
        );

        return array_values($tokens);
    }

    private function buildDocumentText(array $obat): string
    {
        return implode(' ', [
            $obat['nama'] ?? '',
            $obat['kategori'] ?? '',
            $obat['kategori_raw'] ?? '',
            $obat['deskripsi'] ?? '',
            $obat['indikasi'] ?? '',
            $obat['cara_pakai'] ?? '',
            $obat['efek_samping'] ?? '',
        ]);
    }

    private function computeIdf(array $documents): array
    {
        $totalDocs = count($documents);
        $documentFrequency = [];

        foreach ($documents as $tokens) {
            $uniqueTerms = array_unique($tokens);
            foreach ($uniqueTerms as $term) {
                $documentFrequency[$term] = ($documentFrequency[$term] ?? 0) + 1;
            }
        }

        $idf = [];
        foreach ($documentFrequency as $term => $count) {
            $idf[$term] = log(($totalDocs + 1) / ($count + 1)) + 1;
        }

        return $idf;
    }

    private function computeTfIdfVector(array $tokens, array $idf): array
    {
        $frequency = [];
        foreach ($tokens as $term) {
            $frequency[$term] = ($frequency[$term] ?? 0) + 1;
        }

        $vector = [];
        $fallbackIdf = log((count($idf) + 1) / 1) + 1;

        foreach ($frequency as $term => $count) {
            $tf = 1 + log($count);
            $vector[$term] = $tf * ($idf[$term] ?? $fallbackIdf);
        }

        return $vector;
    }

    private function cosineSimilarity(array $a, array $b): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $term => $value) {
            $normA += $value * $value;
            if (isset($b[$term])) {
                $dotProduct += $value * $b[$term];
            }
        }

        foreach ($b as $value) {
            $normB += $value * $value;
        }

        if ($normA === 0.0 || $normB === 0.0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }

    private function detectTargetCategory(string $text): string
    {
        $lowerText = strtolower($text);
        $kerasKeywords = [
            'diabetes',
            'tensi',
            'darah tinggi',
            'hipertensi',
            'kanker',
            'asma',
            'resep',
            'obat keras',
            'steroid',
            'antibiotik',
        ];

        foreach ($kerasKeywords as $keyword) {
            if (str_contains($lowerText, $keyword)) {
                return 'Obat Keras';
            }
        }

        return 'Obat Umum';
    }
}
