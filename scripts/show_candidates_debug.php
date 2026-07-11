<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\ExcelHelper;

$path = __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx';
$data = ExcelHelper::readObatData($path);
$empty = array_filter($data, fn($it) => trim(($it['nama'] ?? '')) === '');

$ref = new ReflectionMethod(ExcelHelper::class, 'extractNamaFromDeskripsi');
$ref->setAccessible(true);

foreach ($empty as $idx => $it) {
    $des = $it['deskripsi'] ?? '';
    $raw = $des;
    $chars = [];
    $len = mb_strlen($raw);
    for ($i = 0; $i < min(10, $len); $i++) {
        $ch = mb_substr($raw, $i, 1);
        $chars[] = bin2hex(mb_convert_encoding($ch, 'UTF-8', 'UTF-8')) . "(" . $ch . ")";
    }
    $candidate = $ref->invoke(null, $des);
    echo sprintf("IDX %03d | price=%s | first=%s\n des=%s\n -> cand=[%s]\n\n", $idx + 1, $it['harga'] ?? '', implode(' ', $chars), $des, $candidate);
}
