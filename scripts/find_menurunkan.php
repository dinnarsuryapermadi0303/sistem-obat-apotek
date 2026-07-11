<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\ExcelHelper;

$path = __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx';
$data = ExcelHelper::readObatData($path);
foreach ($data as $i => $it) {
    if (strtolower(trim($it['nama'] ?? '')) === 'menurunkan') {
        echo "Found at index $i:\n";
        print_r($it);
    }
}

echo "Done\n";
