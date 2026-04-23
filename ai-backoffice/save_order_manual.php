<?php
require "config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$items = $data['items'] ?? [];
$date = $data['date'] ?? date("Y-m-d H:i:s");
$cutstock = $data['cutstock'] ?? false;

if(empty($items)){
    echo "ไม่มีสินค้า";
    exit;
}

/* TOTAL */
$total = 0;

foreach($items as $id=>$i){
    $product = $pdo->query("SELECT * FROM products WHERE id=$id")->fetch();
    if(!$product) continue;

    $total += $product['price'] * $i['qty'];
}

/* ORDER */
$stmt = $pdo->prepare("INSERT INTO orders (total, created_at) VALUES (?,?)");
$stmt->execute([$total, $date]);

$order_id = $pdo->lastInsertId();

/* ITEMS */
foreach($items as $id=>$i){

    $product = $pdo->query("SELECT * FROM products WHERE id=$id")->fetch();
    if(!$product) continue;

    $qty = $i['qty'];
    $price = $product['price'];

    $pdo->prepare("
    INSERT INTO order_items (order_id, product_id, qty, price)
    VALUES (?,?,?,?)
    ")->execute([$order_id,$id,$qty,$price]);

    // 🔥 ถ้าเลือกตัด stock
    if($cutstock){

        $before = $product['stock'];
        $after = $before - $qty;

        $pdo->query("UPDATE products SET stock=$after WHERE id=$id");

    }
}

echo "บันทึกสำเร็จ (#$order_id)";