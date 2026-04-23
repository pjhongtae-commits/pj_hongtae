<?php
require "config/db.php";
require "functions/stock.php";

// ================= รับ JSON =================
$data = json_decode(file_get_contents("php://input"), true);

// 🔥 กันพัง: ถ้าไม่มีข้อมูล
if(empty($data)){
    echo "ไม่มีสินค้า";
    exit;
}

// ================= คำนวณ TOTAL =================
$total = 0;

foreach($data as $product_id => $item){

    $product = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $product->execute([$product_id]);
    $product = $product->fetch();

    if(!$product) continue;

    $qty = intval($item['qty']);
    $price = floatval($product['price']);

    $total += $price * $qty;
}

// 🔥 กัน NULL
if($total <= 0){
    echo "total error";
    exit;
}

// ================= สร้าง ORDER =================
$stmt = $pdo->prepare("
INSERT INTO orders (total, created_at)
VALUES (?, NOW())
");
$stmt->execute([$total]);

$order_id = $pdo->lastInsertId();

// ================= LOOP สินค้า =================
foreach($data as $product_id => $item){

    $product = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $product->execute([$product_id]);
    $product = $product->fetch();

    if(!$product) continue;

    $qty = intval($item['qty']);
    $price = floatval($product['price']);

    // ===== INSERT order_items =====
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, qty, price)
        VALUES (?,?,?,?)
    ");
    $stmt->execute([
        $order_id,
        $product_id,
        $qty,
        $price
    ]);

    // ===== STOCK =====
    $before = intval($product['stock']);
    $after = $before - $qty;

    $stmt = $pdo->prepare("UPDATE products SET stock=? WHERE id=?");
    $stmt->execute([$after, $product_id]);

    // ===== LOG =====
    $stmt = $pdo->prepare("
        INSERT INTO stock_logs 
        (product_id, type, qty, stock_before, stock_after, ref_type, order_id)
        VALUES (?, 'out', ?, ?, ?, 'sale', ?)
    ");
    $stmt->execute([
        $product_id,
        $qty,
        $before,
        $after,
        $order_id
    ]);

    // ===== ALERT =====
    $product_new = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $product_new->execute([$product_id]);
    $product_new = $product_new->fetch();

    checkLowStockAndAlert($product_new);
}

echo $order_id;