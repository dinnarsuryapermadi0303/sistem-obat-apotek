<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\ExcelHelper;

$path = __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx';
$data = ExcelHelper::readObatData($path);
$removed = ExcelHelper::$lastRemovedDuplicates ?? [];

echo "Parsed: " . count($data) . "\n";
echo "Removed: " . count($removed) . "\n";
foreach ($removed as $i => $r) {
    echo sprintf("%03d | name=%s | harga=%s | des=%s\n", $i + 1, $r['nama'] ?? '', $r['harga'] ?? '', mb_substr($r['deskripsi'] ?? '', 0, 80));
}

if (!empty($removed)) {
    $out = __DIR__ . '/../storage/app/dedupe_removed_export_' . date('Ymd_His') . '.csv';
    $fh = fopen($out, 'w');
    if ($fh) {
        fputcsv($fh, ['nama', 'kategori_raw', 'indikasi', 'dosis', 'jenis', 'efek_samping', 'deskripsi', 'harga']);
        foreach ($removed as $r) {
            fputcsv($fh, [
                $r['nama'] ?? '',
                $r['kategori_raw'] ?? '',
                $r['indikasi'] ?? '',
                $r['dosis'] ?? '',
                $r['jenis'] ?? '',
                $r['efek_samping'] ?? '',
                $r['deskripsi'] ?? '',
                $r['harga'] ?? '',
            ]);
        }
        fclose($fh);
        echo "Wrote CSV: $out\n";
    }
}
