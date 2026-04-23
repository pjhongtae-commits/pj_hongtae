<?php
require "../config/db.php";

function autoPriceEngine($pdo){

    $products = $pdo->query("SELECT * FROM products")->fetchAll();

    foreach($products as $p){

        $cost =
            $p['cost'] +
            $p['shipping_cost'] +
            $p['packaging_cost'];

        // 🔴 RULE 1: กันขาดทุน
        $minPrice = $cost + 10;

        // 🔵 RULE 2: กำไรเป้าหมาย 20%
        $targetPrice = $cost * 1.2;

        // 🟢 RULE 3: stock เยอะ ลดราคา
        if($p['stock'] > 50){
            $targetPrice *= 0.95;
        }

        // 🔴 เลือกราคาสุดท้าย
        $finalPrice = max($minPrice, $targetPrice);

        // update DB
        $stmt = $pdo->prepare("UPDATE products SET price=? WHERE id=?");
        $stmt->execute([$finalPrice, $p['id']]);
    }

    return "AUTO PRICE UPDATED";
}

echo autoPriceEngine($pdo);