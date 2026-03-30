<?php
require_once "../../config/db.php";

$sql = "
SELECT p.*, s.quantity,
CASE
    WHEN s.quantity <= 0 THEN 'out'
    WHEN s.quantity <= p.min_stock THEN 'low'
    ELSE 'ok'
END AS status
FROM products p
LEFT JOIN stock s ON p.id = s.product_id
ORDER BY p.id DESC
";
$products = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>สินค้า</title>
</head>
<body>
<h2>📦 สินค้า</h2>
<a href="add.php">➕ เพิ่มสินค้า</a>
<table border="1" cellpadding="8">
<tr>
<th>ชื่อ</th><th>SKU</th><th>คงเหลือ</th><th>สถานะ</th><th>จัดการ</th>
</tr>
<?php foreach ($products as $p): ?>
<tr style="color:
<?= $p['status']=='out'?'red':($p['status']=='low'?'orange':'green') ?>">
<td><?= $p['name'] ?></td>
<td><?= $p['sku'] ?></td>
<td><?= $p['quantity'] ?? 0 ?></td>
<td><?= strtoupper($p['status']) ?></td>
<td>
<a href="edit.php?id=<?= $p['id'] ?>">✏️</a>
<a href="delete.php?id=<?= $p['id'] ?>" onclick="return confirm('ลบ?')">🗑️</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
