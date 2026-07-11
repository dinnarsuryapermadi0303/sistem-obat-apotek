<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelHelper
{
    /**
     * Last removed duplicates (for debugging/audit). Populated by dedupeObatData.
     */
    public static $lastRemovedDuplicates = [];
    /**
     * Resolve the current default Excel path.
     */
    public static function resolveExcelPath(): string
    {
        $candidates = [
            storage_path('app/Daftar obat Keras dan obat Umum.xlsx'),
            storage_path('app/imports/Daftar obat Keras dan obat Umum.xlsx'),
            storage_path('app/obat fix.xlsx'),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \RuntimeException('Excel file for obat data not found.');
    }

    /**
     * Membaca data obat dari file Excel
     */
    public static function readObatData($filePath)
    {
        try {
            if (!file_exists($filePath)) {
                return [];
            }

            $spreadsheet = IOFactory::load($filePath);
            $data = [];

            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $sheetTitle = $sheet->getTitle();
                $rows = $sheet->toArray();
                $data = array_merge($data, self::readSheetData($rows, $sheetTitle));
            }

            // Remove duplicates before returning. Keep the item with the
            // most complete details (more non-empty fields). If tied, keep
            // the later (newer) row so newer imports replace older ones.
            return self::dedupeObatData($data);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Remove duplicate obat entries based on normalized `nama`.
     * Preference is given to the entry with higher completeness score
     * (more non-empty important fields). If scores tie, newer entry wins.
     */
    private static function dedupeObatData(array $items): array
    {
        // Use fuzzy matching on `nama` to detect duplicates that differ by
        // small typos or formatting. We keep a map keyed by a chosen
        // representative name; when a new item is similar to an existing
        // key we merge accordingly.
        $map = [];
        $removed = [];
        foreach ($items as $idx => $it) {
            $rawName = (string)($it['nama'] ?? '');
            $name = trim(strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', '', $rawName)));

            if ($name === '') {
                $key = "__noname__{$idx}";
            } else {
                // search for an existing similar key
                $foundKey = null;
                foreach ($map as $k => $v) {
                    // compare normalized strings
                    similar_text($k, $name, $percent);
                    $dist = levenshtein($k, $name);
                    if ($percent >= 85 || $dist <= 2) {
                        $foundKey = $k;
                        break;
                    }
                }

                $key = $foundKey ?? $name;
            }

            // Compute completeness: count non-empty important fields
            $fields = ['nama', 'deskripsi', 'indikasi', 'dosis', 'efek_samping', 'harga'];
            $score = 0;
            foreach ($fields as $f) {
                if (!empty(trim((string)($it[$f] ?? '')))) {
                    $score++;
                }
            }

            if (!isset($map[$key])) {
                $map[$key] = ['item' => $it, 'score' => $score, 'idx' => $idx];
                continue;
            }

            // If current item is more complete, replace. If tied, prefer later (current)
            $existing = $map[$key];
            if ($score > $existing['score'] || ($score === $existing['score'] && $idx > $existing['idx'])) {
                // record the replaced (older/less complete) entry for logging
                $removed[] = array_merge($existing['item'], ['_removed_score' => $existing['score'], '_removed_idx' => $existing['idx'], '_merged_to' => $key]);
                $map[$key] = ['item' => $it, 'score' => $score, 'idx' => $idx];
            } else {
                // current item is removed in favor of existing
                $removed[] = array_merge($it, ['_removed_score' => $score, '_removed_idx' => $idx, '_merged_to' => $key]);
            }
        }

        // return values only
        // write removed items to CSV for audit if any
        // store removed list for runtime inspection
        self::$lastRemovedDuplicates = $removed;

        if (!empty($removed)) {
            try {
                $filename1 = storage_path('app/dedupe_removed_' . date('Ymd_His') . '.csv');
                $filename2 = __DIR__ . '/../../storage/app/dedupe_removed_' . date('Ymd_His') . '.csv';
                $header = ['nama', 'kategori_raw', 'indikasi', 'dosis', 'jenis', 'efek_samping', 'deskripsi', 'harga', '_removed_score', '_removed_idx'];

                foreach ([$filename1, $filename2] as $filename) {
                    try {
                        $fh = @fopen($filename, 'w');
                        if ($fh) {
                            fputcsv($fh, $header);
                            foreach ($removed as $r) {
                                $row = [
                                    $r['nama'] ?? '',
                                    $r['kategori_raw'] ?? '',
                                    $r['indikasi'] ?? '',
                                    $r['dosis'] ?? '',
                                    $r['jenis'] ?? '',
                                    $r['efek_samping'] ?? '',
                                    $r['deskripsi'] ?? '',
                                    $r['harga'] ?? '',
                                    $r['_removed_score'] ?? '',
                                    $r['_removed_idx'] ?? '',
                                ];
                                fputcsv($fh, $row);
                            }
                            fclose($fh);
                        }
                    } catch (\Throwable $inner) {
                        // ignore per-file errors
                    }
                }
            } catch (\Throwable $e) {
                // ignore logging errors
            }
        }

        return array_values(array_map(fn($v) => $v['item'], $map));
    }

    public static function readSheetData(array $rows, string $sheetTitle): array
    {
        $headerRow = null;
        $mapping = [];
        $titleCategory = null;
        $data = [];

        for ($i = 0; $i < count($rows); $i++) {
            $row = $rows[$i];
            $clean = array_map(fn($cell) => strtolower(trim((string)$cell)), $row);

            $isHeaderRow = self::isHeaderRow($clean);
            if ($isHeaderRow) {
                // If we haven't set a header yet, use this row as header. If a header
                // was already found earlier in the sheet, ignore subsequent header-like
                // rows to avoid remapping columns mid-sheet (common in this workbook).
                if ($headerRow === null) {
                    $headerRow = $i;
                    $mapping = self::getHeaderMapping($clean);
                    $titleCategory = self::resolveSheetCategory($sheetTitle, $rows, $i);
                }
                continue;
            }

            if ($headerRow === null || $i === $headerRow) {
                continue;
            }

            $obat = [
                'nama' => self::getValue($row, $mapping, 'nama', 1),
                'kategori_raw' => self::getValue($row, $mapping, 'kategori', 0),
                'indikasi' => self::getValue($row, $mapping, 'indikasi', 2),
                'dosis' => self::getValue($row, $mapping, 'dosis', 3),
                'jenis' => self::getValue($row, $mapping, 'jenis', 4),
                'efek_samping' => self::getValue($row, $mapping, 'efek_samping', 5),
                'deskripsi' => self::getValue($row, $mapping, 'deskripsi', 6),
                'harga' => self::parsePrice(self::getValue($row, $mapping, 'harga', 7)),
                'gambar' => self::getValue($row, $mapping, 'gambar', 0) ?: 'default.jpg',
            ];

            if (empty($obat['nama'])) {
                $obat['nama'] = self::extractNamaFromRow($row, $mapping, $obat['deskripsi'], $sheetTitle);
            }

            // Skip label rows (Bebas, Obat keras, Obat umum, etc)
            $namaLower = strtolower(trim($obat['nama']));
            $labelKeywords = ['bebas', 'obat keras', 'obat umum', 'tidak ada', 'keterangan', 'no', 'nama'];
            if (in_array($namaLower, $labelKeywords, true)) {
                continue;
            }

            if (!empty($titleCategory)) {
                $obat['kategori_raw'] = $titleCategory;
                $obat['kategori'] = $titleCategory;
            } else {
                if (empty($obat['kategori_raw']) && $titleCategory) {
                    $obat['kategori_raw'] = $titleCategory;
                }

                $obat['kategori'] = self::classifyKategori($obat['kategori_raw'], $obat['deskripsi']);
            }

            $obat = self::fillDetailFields($obat);

            // Include rows that contain any meaningful data even if `nama` couldn't be extracted.
            // Consider the row meaningful if any cell in the raw row is non-empty.
            $hasMeaningful = false;
            foreach ($row as $cell) {
                if (trim((string)$cell) !== '') {
                    $hasMeaningful = true;
                    break;
                }
            }

            if ($hasMeaningful) {
                $data[] = $obat;
            }
        }

        return $data;
    }

    private static function resolveSheetCategory(string $sheetTitle, array $rows, int $headerRow): ?string
    {
        $sheetTitleLower = strtolower($sheetTitle);
        if (str_contains($sheetTitleLower, 'obat keras')) {
            return 'Obat Keras';
        }
        if (str_contains($sheetTitleLower, 'obat umum')) {
            return 'Obat Umum';
        }

        $sectionCategoryRow = $headerRow - 1;
        while ($sectionCategoryRow >= 0 && empty(array_filter($rows[$sectionCategoryRow], fn($cell) => trim((string)$cell) !== ''))) {
            $sectionCategoryRow--;
        }

        if ($sectionCategoryRow >= 0) {
            foreach ($rows[$sectionCategoryRow] as $cell) {
                $value = strtolower(trim((string)$cell));
                if (str_contains($value, 'obat keras')) {
                    return 'Obat Keras';
                }
                if (str_contains($value, 'obat umum') || str_contains($value, 'bebas')) {
                    return 'Obat Umum';
                }
            }
        }

        return null;
    }

    private static function extractNamaFromRow(array $row, array $mapping, string $deskripsi, string $sheetTitle): string
    {
        if (isset($mapping['nama']) && isset($row[$mapping['nama']])) {
            $value = trim((string)$row[$mapping['nama']]);
            if ($value !== '') {
                return $value;
            }
        }

        if (self::isObatKerasSheet($sheetTitle) || str_contains(strtolower($sheetTitle), 'keras')) {
            $name = self::extractNamaFromDeskripsi($deskripsi);
            if ($name !== '') {
                return $name;
            }

            // If the provided `deskripsi` looks like a symptom list (and thus
            // didn't yield a name), scan other cells in the row for a longer
            // descriptive sentence that may contain the product name, e.g.
            // "X merupakan ..." or "X mengandung ...".
            foreach ($row as $cell) {
                $text = trim((string)$cell);
                if ($text === '') continue;
                if (preg_match('/\b(merupakan|mengandung|adalah|mengandung|mengurangi|mengobat|membantu|berfungsi)\b/i', $text)) {
                    $name2 = self::extractNamaFromDeskripsi($text);
                    if ($name2 !== '') return $name2;
                }
            }
        }

        // For sheets without a `nama` column, avoid using the fallback column
        // (often `indikasi`) as the name because it contains symptoms/indications.
        if (!self::isObatKerasSheet($sheetTitle)) {
            $possible = trim((string)($row[1] ?? ''));
            if ($possible !== '' && !self::looksLikeIndication($possible) && self::isValidNama($possible)) {
                return $possible;
            }
        }

        return '';
    }

    private static function isObatKerasSheet(string $sheetTitle): bool
    {
        return str_contains(strtolower($sheetTitle), 'obat keras');
    }

    private static function extractNamaFromDeskripsi(string $deskripsi): string
    {
        $deskripsi = trim($deskripsi);
        if ($deskripsi === '') {
            return '';
        }

        $patterns = [
            '/^([\p{L}0-9\s\-]+?)\s+(?:adalah|merupakan|mengandung|dapat|digunakan|berfungsi|yaitu|ialah|termasuk|adalah obat)\b/ui',
            '/^([\p{L}0-9\s\-]+?)\s*[,;]\s*/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $deskripsi, $matches)) {
                $candidate = trim($matches[1]);
                if (self::isValidNama($candidate)) {
                    return $candidate;
                }
            }
        }

        $parts = preg_split('/\s+/', $deskripsi, 4);
        $candidate = trim($parts[0] ?? '');
        if (self::isValidNama($candidate)) {
            return $candidate;
        }

        return '';
    }

    private static function isValidNama(string $name): bool
    {
        $name = trim($name);
        if ($name === '') return false;

        // normalize for checks: remove punctuation
        $normalized = strtolower(trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', $name)));
        if ($normalized === '') return false;

        // reject price-like values
        if (preg_match('/^\s*(rp|idr)?\s*[\d\.\,]+\s*$/i', $name)) {
            return false;
        }

        // reject if it looks like an indication sentence (verb prefixes)
        if (self::looksLikeIndication($name)) {
            return false;
        }

        // reject common symptom/indication short tokens that may appear as names
        $symptoms = [
            'mual',
            'muntah',
            'kembung',
            'nyeri',
            'nyeri otot',
            'sakit perut',
            'demam',
            'batuk',
            'pilek',
            'pusing',
            'sakit kepala',
            'meredakan',
            'mengatasi',
            'mengencerkan',
            'mengobati',
            'membantu',
            'digunakan'
        ];
        $lower = $normalized;
        foreach ($symptoms as $s) {
            if ($lower === $s) return false;
            if (str_starts_with($lower, $s . ' ')) return false;
        }

        // must contain at least one letter and be reasonably short
        if (preg_match('/[\p{L}]/u', $name) !== 1) {
            return false;
        }

        if (mb_strlen($name) > 60) {
            return false;
        }

        return true;
    }

    private static function looksLikeIndication(string $text): bool
    {
        // Match common Indonesian indication verbs/phrases explicitly.
        // Avoid overly broad short prefixes like `me`/`men` which match many drug names.
        return preg_match('/^(untuk|mencegah|meredakan|membantu|mengatasi|mengobati|mengurangi|dapat|adalah|merupakan|digunakan|berfungsi)\\b/i', trim($text)) === 1;
    }

    private static function fillDetailFields(array $obat): array
    {
        $deskripsi = strtolower($obat['deskripsi'] ?? '');
        $kategori = strtolower($obat['kategori'] ?? '');
        $nama = strtolower($obat['nama'] ?? '');

        if (empty($obat['cara_pakai'])) {
            if (str_contains($deskripsi, 'antibakteri') || str_contains($deskripsi, 'antibiotik')) {
                $obat['cara_pakai'] = 'Gunakan sesuai anjuran dokter atau apoteker. Biasanya diminum 2 kali sehari setelah makan, sesuai dosis pada kemasan.';
            } elseif (str_contains($deskripsi, 'antivirus')) {
                $obat['cara_pakai'] = 'Gunakan sesuai petunjuk penggunaan. Minum setelah makan dan hindari melewatkan dosis.';
            } elseif (str_contains($deskripsi, 'menurunkan gula darah') || str_contains($deskripsi, 'diabetes')) {
                $obat['cara_pakai'] = 'Minum sesuai anjuran dokter, biasanya setelah atau sebelum makan. Pantau gula darah secara rutin.';
            } elseif (str_contains($deskripsi, 'meredakan batuk') || str_contains($deskripsi, 'tenggorokan')) {
                $obat['cara_pakai'] = 'Minum 3 kali sehari setelah makan. Ikuti dosis pada kemasan dan jangan gunakan lebih dari jumlah yang dianjurkan.';
            } elseif (str_contains($deskripsi, 'meredakan nyeri') || str_contains($deskripsi, 'sakit kepala') || str_contains($deskripsi, 'pusing')) {
                $obat['cara_pakai'] = 'Gunakan 1-2 tablet setiap 4-6 jam jika perlu. Jangan melebihi dosis maksimum yang tertera pada kemasan.';
            } elseif (str_contains($deskripsi, 'asam urat') || str_contains($deskripsi, 'sendi')) {
                $obat['cara_pakai'] = 'Gunakan sesuai anjuran dokter. Minum setelah makan dan penuhi asupan cairan yang cukup.';
            } elseif (str_contains($deskripsi, 'maag') || str_contains($deskripsi, 'lambung')) {
                $obat['cara_pakai'] = 'Minum setelah makan. Jika perlu, dapat diulang setiap 4-6 jam sesuai keluhan dan petunjuk pada kemasan.';
            } else {
                $obat['cara_pakai'] = 'Gunakan sesuai petunjuk kemasan atau resep dokter. Jika ragu, konsultasikan pada apoteker.';
            }
        }

        if (empty($obat['efek_samping'])) {
            if (str_contains($deskripsi, 'antibakteri') || str_contains($deskripsi, 'antibiotik')) {
                $obat['efek_samping'] = 'Dapat menyebabkan mual, diare, sakit perut, atau reaksi alergi. Hentikan penggunaan jika muncul ruam atau bengkak.';
            } elseif (str_contains($deskripsi, 'antivirus')) {
                $obat['efek_samping'] = 'Mual ringan dan nyeri kepala dapat terjadi. Jika efek samping berlanjut, segera konsultasi dokter.';
            } elseif (str_contains($deskripsi, 'menurunkan gula darah') || str_contains($deskripsi, 'diabetes')) {
                $obat['efek_samping'] = 'Dapat menyebabkan penurunan gula darah berlebihan jika tidak sesuai dosis. Pantau gula darah secara rutin.';
            } elseif (str_contains($deskripsi, 'meredakan batuk') || str_contains($deskripsi, 'tenggorokan')) {
                $obat['efek_samping'] = 'Beberapa orang dapat mengalami mulut kering atau kantuk ringan. Jangan digunakan bersama alkohol.';
            } elseif (str_contains($deskripsi, 'meredakan nyeri') || str_contains($deskripsi, 'sakit kepala') || str_contains($deskripsi, 'pusing')) {
                $obat['efek_samping'] = 'Dapat menyebabkan sakit perut, mual, atau pusing. Konsumsi bersama makanan bila perlu.';
            } elseif (str_contains($deskripsi, 'asam urat') || str_contains($deskripsi, 'sendi')) {
                $obat['efek_samping'] = 'Dapat menyebabkan gangguan pencernaan atau reaksi kulit. Hentikan jika muncul gejala alergi.';
            } elseif (str_contains($deskripsi, 'maag') || str_contains($deskripsi, 'lambung')) {
                $obat['efek_samping'] = 'Dapat menyebabkan mulas atau konstipasi pada beberapa orang. Minum air yang cukup saat penggunaan.';
            } else {
                $obat['efek_samping'] = 'Efek samping yang mungkin terjadi dapat meliputi gangguan pencernaan atau reaksi alergi. Jika terjadi keluhan, hentikan dan konsultasi dokter.';
            }
        }

        return $obat;
    }

    /**
     * Cari obat berdasarkan nama
     */
    public static function findObatByName($filePath, $nama)
    {
        $obatList = self::readObatData($filePath);

        $nama = strtolower(trim($nama));

        foreach ($obatList as $obat) {

            $namaObat = strtolower(trim($obat['nama'] ?? ''));

            // Sama persis
            if ($namaObat === $nama) {
                return $obat;
            }

            // Nama hasil rekomendasi mengandung nama di Excel
            if (str_contains($nama, $namaObat)) {
                return $obat;
            }

            // Nama Excel mengandung nama hasil rekomendasi
            if (str_contains($namaObat, $nama)) {
                return $obat;
            }
        }

        return null;
    }
    private static function isHeaderRow(array $clean): bool
    {
        $keywords = [
            'nama',
            'obat',
            'harga',
            'indikasi',
            'dosis',
            'efek samping',
            'deskripsi',
            'penyajian',
            'keterangan',
            'kategori',
        ];

        $matchCount = 0;
        foreach ($clean as $cell) {
            // Ignore very long cells (likely description text) to avoid false positives
            // where a long description contains header keywords.
            if (mb_strlen($cell) > 40) {
                continue;
            }
            foreach ($keywords as $keyword) {
                if ($keyword !== '' && str_contains($cell, $keyword)) {
                    $matchCount++;
                    break;
                }
            }
        }

        return $matchCount >= 3;
    }

    private static function getHeaderMapping(array $headers): array
    {
        $map = [];
        $candidates = [
            'nama' => ['nama obat', 'nama'],
            'kategori' => ['kategori', 'category', 'type', 'golongan'],
            'deskripsi' => ['deskripsi obat', 'deskripsi', 'description', 'ket', 'keterangan', 'fungsi', 'kegunaan'],
            'indikasi' => ['indikasi', 'indication'],
            'dosis' => ['dosis'],
            'jenis' => ['jenis', 'penyajian', 'penyajian obat', 'sajian', 'cara pakai', 'aturan pakai'],
            'komposisi' => ['komposisi', 'composition', 'bahan aktif', 'kandungan'],
            'kontraindikasi' => ['kontraindikasi', 'contraindikasi', 'contraindication'],
            'efek_samping' => ['efek samping', 'efek_samping', 'side effect', 'side_effect'],
            'harga' => ['harga', 'price', 'harga jual', 'harga 1 strip', 'harga 1strip', 'harga strip', 'price per strip'],
            'gambar' => ['gambar', 'image', 'foto', 'file gambar'],
        ];

        foreach ($headers as $index => $header) {
            $header = strtolower(trim((string) $header));
            if ($header === '') {
                continue;
            }

            foreach ($candidates as $field => $names) {
                foreach ($names as $name) {
                    $needle = strtolower($name);
                    if ($needle === $header || str_contains($header, $needle)) {
                        if (!isset($map[$field])) {
                            $map[$field] = $index;
                            break 2;
                        }

                        break;
                    }
                }
            }
        }

        return $map;
    }

    private static function getValue(array $row, array $mapping, string $field, int $fallbackIndex)
    {
        if (isset($mapping[$field]) && isset($row[$mapping[$field]])) {
            return trim((string)$row[$mapping[$field]]);
        }

        if ($field === 'kategori') {
            return '';
        }

        if ($field === 'harga') {
            return trim((string)($row[count($row) - 1] ?? ''));
        }

        if ($field === 'nama') {
            return '';
        }

        if (in_array($field, ['indikasi', 'efek_samping', 'cara_pakai'], true)) {
            return '';
        }

        if ($field === 'jenis') {
            return trim((string)($row[$fallbackIndex] ?? ''));
        }

        return trim((string)($row[$fallbackIndex] ?? ''));
    }

    public static function parsePrice($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return 0;
        }

        // Hanya ambil digit agar format seperti 7.000 atau 2,000 menjadi 7000 / 2000
        $normalized = preg_replace('/\D/', '', $value);

        if ($normalized === '') {
            return 0;
        }

        return (int) $normalized;
    }

    /**
     * Klasifikasi kategori ke Obat Umum / Obat Keras
     */
    private static function classifyKategori(string $kategori, string $deskripsi = ''): string
    {
        $kategoriLower = strtolower($kategori);
        $deskripsiLower = strtolower($deskripsi);

        $obatKerasKeywords = [
            'keras',
            'resep',
            'antibiotik',
            'steroid',
            'hormon',
            'trombosit',
            'napza',
            'koplum',
            'narkotik',
            'obat bebas terbatas',
            'obat keras',
            'obat resep',
        ];

        foreach ($obatKerasKeywords as $keyword) {
            if (str_contains($kategoriLower, $keyword) || str_contains($deskripsiLower, $keyword)) {
                return 'Obat Keras';
            }
        }

        return 'Obat Umum';
    }

    /**
     * Cari obat berdasarkan kategori
     */
    public static function findObatByCategory($filePath, $kategori)
    {
        $obatList = self::readObatData($filePath);
        $result = [];

        foreach ($obatList as $obat) {
            if (strtolower($obat['kategori']) == strtolower($kategori)) {
                $result[] = $obat;
            }
        }

        return $result;
    }

    /**
     * Cari obat berdasarkan keyword di beberapa field
     */
    public static function searchObat($filePath, $keyword)
    {
        $obatList = self::readObatData($filePath);
        $result = [];
        $keyword = trim(strtolower((string) $keyword));

        if ($keyword === '') {
            return $obatList;
        }

        $keywords = array_filter(preg_split('/[^\p{L}0-9]+/u', $keyword), fn($word) => $word !== '');

        foreach ($obatList as $obat) {
            $haystack = strtolower(implode(' ', [
                $obat['nama'] ?? '',
                $obat['kategori'] ?? '',
                $obat['kategori_raw'] ?? '',
                $obat['deskripsi'] ?? '',
                $obat['indikasi'] ?? '',
                $obat['cara_pakai'] ?? '',
                $obat['efek_samping'] ?? '',
            ]));

            $matches = false;

            if (str_contains($haystack, $keyword)) {
                $matches = true;
            } else {
                foreach ($keywords as $word) {
                    if (strlen($word) >= 1 && str_contains($haystack, $word)) {
                        $matches = true;
                        break;
                    }
                }
            }

            if ($matches) {
                $result[] = $obat;
            }
        }

        return $result;
    }
}
