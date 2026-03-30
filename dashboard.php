<?php
require "config/db.php";
require "ai/decision.php";
require "ai/recommend.php";

/* ===== Summary ===== */

$totalSales = $pdo->query("
SELECT SUM(total) FROM orders
")->fetchColumn();

$totalOrders = $pdo->query("
SELECT COUNT(*) FROM orders
")->fetchColumn();

$lowStock = $pdo->query("
SELECT COUNT(*) 
FROM products 
WHERE stock <= min_stock
")->fetchColumn();

/* ===== AI ===== */

$aiDecision = getAIDecision($pdo);
$aiRecommend = getAIRecommend($pdo);

?>

<!DOCTYPE html>
<html>
<head>
<title>AI Backoffice</title>
<style>
body{font-family:arial;background:#f5f6fa}
.card{
background:white;
padding:20px;
margin:10px;
border-radius:10px;
box-shadow:0 0 10px rgba(0,0,0,0.1);
}
.grid{
display:grid;
grid-template-columns:repeat(3,1fr);
}
.red{color:red}
.green{color:green}
</style>
</head>

<body>

<h2>AI BACKOFFICE DASHBOARD</h2>

<div class="grid">

<div class="card">
ยอดขายทั้งหมด<br>
<h2><?= number_format($totalSales) ?></h2>
</div>

<div class="card">
จำนวนออเดอร์<br>
<h2><?= $totalOrders ?></h2>
</div>

<div class="card">
สินค้าใกล้หมด<br>
<h2 class="red"><?= $lowStock ?></h2>
</div>

</div>

<div class="card">
<h3>🤖 AI ตัดสินใจ</h3>
<?= $aiDecision ?>
</div>

<div class="card">
<h3>📊 AI แนะนำ</h3>
<?= $aiRecommend ?>
</div>

</body>
</html>