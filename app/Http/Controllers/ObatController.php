<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ExcelHelper;
use App\Models\Product;

class ObatController extends Controller
{
    /**
     * Path File Excel
     */
    private $excelPath;

    public function __construct()
    {
        $this->excelPath = ExcelHelper::resolveExcelPath();
    }

    /*
    |--------------------------------------------------------------------------
    | Halaman Produk
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $obatList = $this->getDbObatList();

        $categories = $this->getCategories($obatList);

        $totalObat = count($obatList);

        $categoryCounts = $this->getCategoryCounts($obatList);

        return view(

            'produk',

            compact(

                'obatList',
                'categories',
                'totalObat',
                'categoryCounts'

            )

        );
    }

    /*
    |--------------------------------------------------------------------------
    | Detail Obat
    |--------------------------------------------------------------------------
    */

    public function detail($nama)
    {

        $searchName = urldecode($nama);
        $normalizedSearch = strtolower(trim($searchName));

        // Prefer admin-saved products in DB
        $dbMatch = Product::whereRaw('LOWER(TRIM(nama)) = ?', [$normalizedSearch])->first();
        if (!$dbMatch) {
            $dbMatch = Product::whereRaw('LOWER(TRIM(nama)) LIKE ?', ["%{$normalizedSearch}%"])
                ->orWhereRaw('LOWER(TRIM(nama)) LIKE ?', ["%{$normalizedSearch}%"])
                ->first();
        }

        if ($dbMatch) {
            $obat = $dbMatch->toArray();
        } else {
            $obat = ExcelHelper::findObatByName(
                $this->excelPath,
                $searchName
            );
        }

        if (!$obat) {

            return redirect('/produk')

                ->with(

                    'error',

                    'Obat tidak ditemukan.'

                );
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $default = [

            'nama' => '-',

            'gambar' => 'default.png',

            'kategori' => 'Obat Bebas',

            'jenis' => 'Obat Umum',

            'indikasi' => '-',

            'deskripsi' => '-',

            'similarity' => 0,

            'confidence' => '0%',

            'status' => 'Belum Dinilai',

            'admin_conditions' => '',

            'admin_confirmed' => false

        ];

        $obat = array_merge(

            $default,

            $obat

        );

        /*
        |--------------------------------------------------------------------------
        | Similarity
        |--------------------------------------------------------------------------
        */

        $similarity = request()->query(

            'similarity',

            0

        );

        if (is_numeric($similarity)) {

            $similarity = round(

                (float) $similarity,

                2

            );

            $obat['similarity'] = $similarity;

            $obat['confidence'] =

                $similarity . '%';

            if ($similarity >= 80) {

                $obat['status'] =

                    'Sangat Relevan';
            } elseif ($similarity >= 60) {

                $obat['status'] =

                    'Relevan';
            } elseif ($similarity >= 40) {

                $obat['status'] =

                    'Cukup Relevan';
            } else {

                $obat['status'] =

                    'Kurang Relevan';
            }
        }
        /*
        |--------------------------------------------------------------------------
        | Validasi Admin
        |--------------------------------------------------------------------------
        */

        $validationKey = request()->query('validation_key');

        if (!empty($validationKey)) {

            $approvals = session('validation_approvals', []);

            if (isset($approvals[$validationKey])) {

                $approved = $approvals[$validationKey]['approved'] ?? [];

                $conditions = $approvals[$validationKey]['conditions'] ?? '';

                $obat['admin_conditions'] = $conditions;

                $obat['admin_confirmed'] = in_array(

                    $obat['nama'],

                    $approved,

                    true

                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(

            'detail_obat',

            [

                'obat' => $obat

            ]

        );
    }

    /*
    |--------------------------------------------------------------------------
    | Filter Berdasarkan Kategori
    |--------------------------------------------------------------------------
    */

    public function filterByCategory(Request $request)
    {
        $kategori = $request->query('kategori');
        $allObat = $this->getDbObatList();

        if (empty($kategori)) {
            $obatList = $allObat;
        } else {
            $obatList = array_filter($allObat, fn($o) => (($o['kategori'] ?? '') == $kategori));
        }

        $categories = $this->getCategories($allObat);

        $totalObat = count($allObat);

        $categoryCounts = $this->getCategoryCounts($allObat);

        return view(

            'produk',

            compact(

                'obatList',

                'kategori',

                'categories',

                'totalObat',

                'categoryCounts'

            )

        );
    }

    /*
    |--------------------------------------------------------------------------
    | Search Obat
    |--------------------------------------------------------------------------
    */

    public function search(Request $request)
    {
        $keyword = trim(

            $request->query('q', '')

        );

        $allObat = $this->getDbObatList();

        // Prefix-match search on `nama` (case-insensitive).
        // If keyword is empty, return all.
        $obatList = array_filter($allObat, function ($o) use ($keyword) {
            if ($keyword === '') return true;
            $name = strtolower($o['nama'] ?? '');
            $k = strtolower($keyword);
            return substr($name, 0, strlen($k)) === $k;
        });

        $categories = $this->getCategories($allObat);

        $totalObat = count($allObat);

        $categoryCounts = $this->getCategoryCounts($allObat);

        return view(

            'produk',

            compact(

                'obatList',

                'keyword',

                'categories',

                'totalObat',

                'categoryCounts'

            )

        );
    }

    /*
    |--------------------------------------------------------------------------
    | List Kategori
    |--------------------------------------------------------------------------
    */

    private function getCategories(array $obatList): array
    {
        $categories = [];

        foreach ($obatList as $obat) {

            $kategori = $obat['kategori'] ?? 'Obat Umum';

            $categories[] = $kategori;
        }

        $categories = array_unique($categories);

        sort($categories);

        return array_values($categories);
    }

    /*
    |--------------------------------------------------------------------------
    | Jumlah Obat per Kategori
    |--------------------------------------------------------------------------
    */

    private function getCategoryCounts(array $obatList): array
    {
        $counts = [];

        foreach ($obatList as $obat) {

            $kategori = $obat['kategori'] ?? 'Obat Umum';

            if (!isset($counts[$kategori])) {

                $counts[$kategori] = 0;
            }

            $counts[$kategori]++;
        }

        ksort($counts);

        return $counts;
    }

    /**
     * Merge Excel-based obat list with admin session products.
     * Session products override Excel entries with same `nama`.
     */
    private function getMergedObatList(): array
    {
        $excel = ExcelHelper::readObatData($this->excelPath);

        $sessionProduk = session('produk_data', []);

        // index by lowercased nama for quick override
        $map = [];
        foreach ($excel as $item) {
            $key = strtolower(trim($item['nama'] ?? ''));
            if ($key !== '') $map[$key] = $item;
        }

        foreach ($sessionProduk as $p) {
            $key = strtolower(trim($p['nama'] ?? ''));
            if ($key === '') continue;
            // override or add
            $map[$key] = array_merge($map[$key] ?? [], $p);
        }

        // Override with DB products created by admin
        $dbProduk = Product::all()->toArray();
        foreach ($dbProduk as $p) {
            $key = strtolower(trim($p['nama'] ?? ''));
            if ($key === '') continue;
            $map[$key] = array_merge($map[$key] ?? [], $p);
        }

        return array_values($map);
    }

    private function getDbObatList(): array
    {
        return Product::orderBy('nama')->get()->toArray();
    }

    /**
     * Server-Sent Events stream that notifies clients about new products.
     * This implementation polls the database for new Product IDs and pushes
     * events when new rows are detected. Suitable for low-volume real-time updates.
     */
    public function stream()
    {
        // Keep the existing stream route for compatibility, but if it is not used
        // by the page it won't affect the user-loading experience.
        return response('Stream endpoint', 200);
    }

    public function stats()
    {
        $obatList = $this->getDbObatList();
        return response()->json([
            'totalObat' => count($obatList),
            'categoryCounts' => $this->getCategoryCounts($obatList),
        ]);
    }
}
