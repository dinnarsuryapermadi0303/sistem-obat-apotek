<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\ExcelHelper;

$path = __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx';
$data = ExcelHelper::readObatData($path);
$empty = array_filter($data, fn($it) => trim(($it['nama'] ?? '')) === '');

$ref = new ReflectionMethod(ExcelHelper::class, 'extractNamaFromDeskripsi');
$ref->setAccessible(true);
$refIsValid = new ReflectionMethod(ExcelHelper::class, 'isValidNama');
$refIsValid->setAccessible(true);

foreach ($empty as $idx => $it) {
    $des = $it['deskripsi'] ?? '';
    $candidate = $ref->invoke(null, $des);
    $isValid = $refIsValid->invoke(null, $candidate);
    echo sprintf("%03d | harga=%s | des=%s\n -> candidate=[%s] valid=%s\n", $idx + 1, $it['harga'] ?? '', mb_substr($des, 0, 80), $candidate, $isValid ? 'YES' : 'NO');
}
