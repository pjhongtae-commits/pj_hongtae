<?php
require "../config/db.php";

$id = $_GET['id'] ?? 0;
$qty = $_GET['qty'] ?? 0;

if(!$id || !$qty){
    echo "❌ ข้อมูลไม่ถูกต้อง";
    exit;
}

/* อัปเดต stock */
$stmt = $pdo->prepare("
UPDATE products 
SET stock = stock + ? 
WHERE id=?
");

$stmt->execute([$qty, $id]);

/* บันทึก log */
$pdo->prepare("
INSERT INTO stock_logs(product_id,type,qty,note)
VALUES (?,?,?,?)
")->execute([
$id,
'in',
$qty,
'AI สั่งเพิ่ม'
]);

echo "📦 เพิ่ม stock สำเร็จ +$qty";