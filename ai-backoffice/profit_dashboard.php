<?php
require "config/db.php";

/* กำไรรวมทั้งหมด */
$totalProfit = $pdo->query("
SELECT SUM((price - cost) * qty) 
FROM order_items
")->fetchColumn();

/* กำไรวันนี้ */
$todayProfit = $pdo->query("
SELECT SUM((oi.price - oi.cost) * oi.qty)
FROM order_items oi
JOIN orders o ON o.id = oi.order_id
WHERE DATE(o.date) = CURDATE()
")->fetchColumn();

/* ยอดขายวันนี้ */
$todaySales = $pdo->query("
SELECT SUM(total)
FROM orders
WHERE DATE(date) = CURDATE()
")->fetchColumn();

/* สินค้ากำไรสูงสุด */
$topProfit = $pdo->query("
SELECT p.name,
SUM((oi.price-oi.cost)*oi.qty) profit
FROM order_items oi
JOIN products p ON p.id = oi.product_id
GROUP BY oi.product_id
ORDER BY profit DESC
LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
<title>Profit Dashboard</title>

<style>
body{font-family:arial;background:#f5f6fa}
.grid{
display:grid;
grid-template-columns:repeat(3,1fr);
gap:15px;
}

.card{
background:white;
padding:20px;
border-radius:10px;
box-shadow:0 0 5px rgba(0,0,0,.1);
}

.green{color:green}
</style>

</head>
<body>

<h2>📊 Profit Dashboard</h2>

<div class="grid">

<div class="card">
กำไรทั้งหมด
<h2 class="green">
<?= number_format($totalProfit,2) ?>
</h2>
</div>

<div class="card">
กำไรวันนี้
<h2 class="green">
<?= number_format($todayProfit,2) ?>
</h2>
</div>

<div class="card">
ยอดขายวันนี้
<h2>
<?= number_format($todaySales,2) ?>
</h2>
</div>

</div>

<br>

<div class="card">
<h3>สินค้ากำไรสูงสุด</h3>

<?php foreach($topProfit as $p): ?>

<div>
<?= $p['name'] ?> 
- กำไร <?= number_format($p['profit'],2) ?>
</div>

<?php endforeach; ?>

</div>

</body>
</html>