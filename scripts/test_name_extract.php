<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\ExcelHelper;

$descs = [
    'Renadinac mengandung obat antiinflamasi nonsteroid (OAINS) yang membantu mengurangi nyeri dan peradangan.',
    'Roxidene mengandung antibiotik golongan makrolida yang bekerja menghambat pertumbuhan bakteri penyebab infeksi.',
    'Tranexamic Acid merupakan obat antifibrinolitik yang bekerja membantu menghentikan perdarahan.',
    'Amoxicillin adalah antibiotik golongan penisilin yang digunakan untuk mengatasi berbagai infeksi bakteri.'
];

$ref = new ReflectionMethod(ExcelHelper::class, 'extractNamaFromDeskripsi');
$ref->setAccessible(true);
foreach ($descs as $d) {
    $res = $ref->invoke(null, $d);
    echo "Desc: {$d}\n -> Extracted: [{$res}]\n\n";
}
