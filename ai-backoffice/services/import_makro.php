<?php
require "../config/db.php";
require_once "../services/pricing.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

$fullPath = __DIR__ . "/../uploads/makro.csv";

echo "📂 FILE: $fullPath <br>";

if (!file_exists($fullPath)) {
    die("❌ ไม่เจอไฟล์");
}

$handle = fopen($fullPath, "r");

if (!$handle) {
    die("❌ เปิดไฟล์ไม่ได้");
}

/* =========================
   🔥 FIX: ใช้ fgetcsv ล้วน
========================= */
$header = fgetcsv($handle, 0, ",");

echo "HEADER: ";
print_r($header);
echo "<br><br>";

$updated = 0;
$new = 0;
$nochange = 0;
$skip = 0;
$rowCount = 0;

/* =========================
   LOOP FIXED
========================= */
while (($row = fgetcsv($handle, 0, ",")) !== false) {

    $rowCount++;

    echo "➡ ROW $rowCount: ";
    print_r($row);
    echo "<br>";

    if (count($row) < 3) {
        echo "❌ SKIP (bad columns)<br><br>";
        $skip++;
        continue;
    }

    /* =========================
       CLEAN DATA
    ========================= */
    $sku  = trim($row[0]);
    $name = trim($row[1]);
    $cost = (float) preg_replace('/[^0-9.]/', '', $row[2]);

    echo "SKU=$sku | NAME=$name | COST=$cost <br>";

    if ($sku === '' || $cost <= 0) {
        echo "⛔ INVALID<br><br>";
        $skip++;
        continue;
    }

    /* =========================
       FIND PRODUCT
    ========================= */
    $stmt = $pdo->prepare("SELECT * FROM products WHERE sku = ?");
    $stmt->execute([$sku]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    $new_price = calculateSellingPrice($cost, 0, 0, 20, 30);

    /* =========================
       UPDATE
    ========================= */
    if ($product) {

        $old_cost = (float)$product['cost'];

        if ($old_cost != $cost) {

            $pdo->prepare("
                UPDATE products 
                SET cost=?, name=?, price=? 
                WHERE sku=?
            ")->execute([$cost, $name, $new_price, $sku]);

            echo "🔄 UPDATED<br><br>";
            $updated++;

        } else {
            echo "➖ NO CHANGE<br><br>";
            $nochange++;
        }

    } 
    /* =========================
       INSERT
    ========================= */
    else {

        $pdo->prepare("
            INSERT INTO products (sku, name, cost, price)
            VALUES (?, ?, ?, ?)
        ")->execute([$sku, $name, $cost, $new_price]);

        echo "🆕 INSERTED<br><br>";
        $new++;
    }
}

fclose($handle);

/* =========================
   RESULT
========================= */
echo "<hr>";
echo "📊 TOTAL ROWS: $rowCount <br>";
echo "🔄 UPDATED: $updated <br>";
echo "🆕 NEW: $new <br>";
echo "➖ NO CHANGE: $nochange <br>";
echo "⏭ SKIP: $skip <br><br>";

echo "🚀 DONE";