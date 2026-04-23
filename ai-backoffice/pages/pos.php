<?php
require "../config/db.php";
require "../functions/stock.php";

// ================= รับค่าจากฟอร์ม =================
if(isset($_POST['product_id']) && isset($_POST['qty'])){
    
    $product_id = intval($_POST['product_id']);
    $qty = intval($_POST['qty']);

    if($qty > 0){
        sellProduct($product_id, $qty);
        $success = "✅ ขายสินค้าเรียบร้อย";
    } else {
        $error = "❌ จำนวนไม่ถูกต้อง";
    }
}

// ================= ดึงสินค้า =================
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>POS</title>
</head>
<body>

<h2>🛒 ระบบขายสินค้า (POS)</h2>

<?php if(!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
<?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

<!-- ================= ฟอร์มขาย ================= -->
<form method="post">
    <label>สินค้า:</label>
    <select name="product_id" required>
        <option value="">-- เลือกสินค้า --</option>
        <?php foreach($products as $p): ?>
            <option value="<?= $p['id'] ?>">
                <?= $p['name'] ?> (เหลือ <?= $p['stock'] ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <br><br>

    <label>จำนวน:</label>
    <input type="number" name="qty" min="1" required>

    <br><br>

    <button type="submit">💰 ขายสินค้า</button>
</form>

<hr>

<!-- ================= ตารางสินค้า ================= -->
<h3>📦 รายการสินค้า</h3>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>ชื่อ</th>
        <th>คงเหลือ</th>
        <th>ขั้นต่ำ</th>
        <th>สถานะ</th>
    </tr>

    <?php foreach($products as $p): ?>
    <tr>
        <td><?= $p['id'] ?></td>
        <td><?= $p['name'] ?></td>
        <td><?= $p['stock'] ?></td>
        <td><?= $p['min_stock'] ?></td>
        <td>
            <?php if($p['stock'] <= $p['min_stock']): ?>
                🔴 ใกล้หมด
            <?php else: ?>
                🟢 ปกติ
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>