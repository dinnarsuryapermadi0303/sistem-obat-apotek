<?php
$tests = [
    'Mefenamic Acid merupakan obat antiinflamasi nonsteroid (OAINS) yang bekerja mengurangi nyeri dan peradangan.',
    'Megasonum mengandung kortikosteroid yang bekerja mengurangi peradangan dan menekan respons sistem imun.',
    'Mual, gangguan pencernaan ringan, sembelit, atau reaksi alergi pada sebagian orang.'
];
$patterns = [
    '/^([\p{L}0-9\s\-]+?)\s+(?:adalah|merupakan|mengandung|dapat|digunakan|berfungsi|yaitu|ialah|termasuk|adalah obat)\b/ui',
    '/^([\p{L}0-9\s\-]+?)\s*[,;]\s*/u',
];

foreach ($tests as $t) {
    echo "Test: $t\n";
    foreach ($patterns as $p) {
        $m = null;
        $r = preg_match($p, $t, $m);
        echo " Pattern: $p\n  match=$r\n";
        if ($r) {
            var_export($m);
            echo "\n";
        }
    }
    echo "---\n";
}
