<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\ExcelHelper;

$paths = [
    __DIR__ . '/../Daftar obat Keras dan obat Umum.xlsx',
    __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx',
    __DIR__ . '/../storage/app/imports/Daftar obat Keras dan obat Umum.xlsx',
    __DIR__ . '/../obat fix.xlsx',
];

foreach ($paths as $p) {
    echo "Checking: $p\n";
    if (!file_exists($p)) {
        echo "  Not found\n";
        continue;
    }

    $data = ExcelHelper::readObatData($p);
    echo "  Found file. Parsed rows: " . count($data) . "\n";
    if (count($data) > 0) {
        echo "  First item name: " . ($data[0]['nama'] ?? '') . "\n";
    }
}
