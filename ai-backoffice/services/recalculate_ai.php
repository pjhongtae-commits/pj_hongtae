<?php
require "../config/db.php";
require "pricing.php";
require "ai_pricing.php";

/* เปิด error */
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ✅ รับค่าจากหน้าเว็บ */
$profitMode = $_GET['profit'] ?? 'auto'; 
// auto = ใช้ AI, ถ้าเป็นตัวเลข = ใช้ค่านั้น

$data = $pdo->query("SELECT * FROM products");

$count = 0;
$skip = 0;

foreach($data as $p){

    /* ❌ ข้ามสินค้าที่ไม่ใช้ AI */
    if(isset($p['use_ai']) && !$p['use_ai']){
        $skip++;
        continue;
    }

    /* กันค่า null */
    $cost = $p['cost'] ?? 0;
    $shipping = $p['shipping_cost'] ?? 0;
    $packaging = $p['packaging_cost'] ?? 0;
    $fee = $p['shopee_fee_percent'] ?? 20;

    /* =========================
       🧠 เลือกโหมดกำไร
    ========================= */

    if($profitMode === 'auto'){
        // 🤖 AI คิด %
        $newProfitPercent = aiProfitPercent($p);
    }else{
        // 🔥 ใช้ค่าที่ user เลือก
        $newProfitPercent = (float)$profitMode;
    }

    /* 🧮 คำนวณราคา */
    $newPrice = calculateSellingPrice(
        $cost,
        $shipping,
        $packaging,
        $fee,
        $newProfitPercent
    );

    /* กันค่าพัง */
    if($newPrice <= 0){
        continue;
    }

    /* 💾 update */
    $stmt = $pdo->prepare("
        UPDATE products 
        SET price=?, profit_percent=? 
        WHERE id=?
    ");

    $stmt->execute([
        $newPrice,
        $newProfitPercent,
        $p['id']
    ]);

    $count++;
}

/* 📊 result */
echo "🚀 อัปเดต $count รายการ | ข้าม $skip รายการ | โหมด: $profitMode";