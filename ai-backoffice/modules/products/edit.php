<?php
require_once "../../config/db.php";
$id = $_GET['id'];

if ($_POST) {
    $pdo->prepare("
        UPDATE products SET name=?, sku=?, cost_price=?, sell_price=?, min_stock=?
        WHERE id=?
    ")->execute([
        $_POST['name'], $_POST['sku'], $_POST['cost'],
        $_POST['price'], $_POST['min'], $id
    ]);

    $pdo->prepare("UPDATE stock SET quantity=? WHERE product_id=?")
        ->execute([$_POST['qty'], $id]);

    header("Location: list.php");
}

$p = $pdo->query("
SELECT p.*, s.quantity
FROM products p
LEFT JOIN stock s ON p.id=s.product_id
WHERE p.id=$id
")->fetch(PDO::FETCH_ASSOC);
?>
<form method="post">
<h2>✏️ แก้ไขสินค้า</h2>
ชื่อ <input name="name" value="<?= $p['name'] ?>"><br>
SKU <input name="sku" value="<?= $p['sku'] ?>"><br>
ต้นทุน <input name="cost" value="<?= $p['cost_price'] ?>"><br>
ราคาขาย <input name="price" value="<?= $p['sell_price'] ?>"><br>
คงเหลือ <input name="qty" value="<?= $p['quantity'] ?>"><br>
ขั้นต่ำ <input name="min" value="<?= $p['min_stock'] ?>"><br>
<button>อัปเดต</button>
</form>
