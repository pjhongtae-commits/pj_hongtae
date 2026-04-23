<?php
require "../config/db.php";
require "pricing.php";
require "ai_pricing.php";

/* รับ id */
$id = $_GET['id'] ?? 0;

if(!$id){
    echo "❌ ไม่พบสินค้า";
    exit;
}

/* ดึงข้อมูลสินค้า */
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$p){
    echo "❌ ไม่พบสินค้า";
    exit;
}

/* 🤖 AI คิด % กำไร */
$newProfitPercent = aiProfitPercent($p);

/* 🧮 คำนวณราคาใหม่ */
$newPrice = calculateSellingPrice(
    $p['cost'],
    $p['shipping_cost'],
    $p['packaging_cost'],
    $p['shopee_fee_percent'],
    $newProfitPercent
);

/* 💾 update */
$stmt = $pdo->prepare("
UPDATE products 
SET price=?, profit_percent=? 
WHERE id=?
");

$stmt->execute([
    $newPrice,
    $newProfitPercent,
    $id
]);

echo "🤖 AI ปรับราคา '{$p['name']}' เป็น ฿".number_format($newPrice,2);