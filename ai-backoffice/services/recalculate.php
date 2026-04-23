<?php
require "../config/db.php";
require "pricing.php";

$data = $pdo->query("SELECT * FROM products");

$count = 0;

foreach($data as $p){

    $newPrice = calculateSellingPrice(
        $p['cost'],
        $p['shipping_cost'],
        $p['packaging_cost'],
        $p['shopee_fee_percent'],
        $p['profit_percent']
    );

    $stmt = $pdo->prepare("UPDATE products SET price=? WHERE id=?");
    $stmt->execute([$newPrice, $p['id']]);

    $count++;
}

echo "✅ อัปเดตราคาแล้ว $count รายการ";