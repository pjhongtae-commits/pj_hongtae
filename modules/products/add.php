<?php
require_once "../../config/db.php";

if ($_POST) {
    $stmt = $pdo->prepare("
        INSERT INTO products (name, model, cost, price, stock)
        VALUES (?,?,?,?,?)
    ");
    $stmt->execute([
        $_POST['name'],
        $_POST['model'],
        $_POST['cost'],
        $_POST['price'],
        $_POST['qty']
    ]);

    header("Location: list.php");
}
?>
<form method="post">
<h2>➕ เพิ่มสินค้า</h2>
ชื่อ <input name="name"><br>
รุ่น <input name="model"><br>
ต้นทุน <input name="cost"><br>
ราคาขาย <input name="price"><br>
สต๊อกเริ่มต้น <input name="qty"><br>
<button>บันทึก</button>
</form>
