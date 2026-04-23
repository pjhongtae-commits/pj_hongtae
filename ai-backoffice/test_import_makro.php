<?php

$fullPath = __DIR__ . "/../uploads/makro.csv";

if (!file_exists($fullPath)) {
    die("❌ ไม่เจอไฟล์ CSV");
}

$handle = fopen($fullPath, "r");

echo "📂 FILE OK<br><br>";

$i = 0;

while (($row = fgetcsv($handle)) !== false) {

    echo "ROW $i: ";
    print_r($row);
    echo "<br>";

    $i++;

    // เอาแค่ 5 แถวพอ
    if ($i >= 5) break;
}

fclose($handle);
