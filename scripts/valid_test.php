<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\ExcelHelper;

$tests = [
    'Mefenamic Acid',
    'Megasonum',
    'Mual',
    'Metformin',
    'Metronidazole',
    'Mecobalamin'
];
$ref = new ReflectionMethod(ExcelHelper::class, 'isValidNama');
$ref->setAccessible(true);
foreach ($tests as $t) {
    $ok = $ref->invoke(null, $t) ? 'YES' : 'NO';
    echo "$t => $ok\n";
}
