<?php
$dir = __DIR__ . '/../storage/app';
if (!is_dir($dir)) {
    echo "no dir $dir\n";
    exit;
}
$files = scandir($dir);
foreach ($files as $f) {
    echo $f . "\n";
}
