<?php
require "config/db.php";

/* ยอดขายวันนี้ */
$today = date("Y-m-d");

$sales = $pdo->query("
SELECT SUM(total) t
FROM orders
WHERE DATE(created_at)='$today'
")->fetch()['t'] ?? 0;

/* จำนวนบิล */
$orders = $pdo->query("
SELECT COUNT(*) c
FROM orders
WHERE DATE(created_at)='$today'
")->fetch()['c'] ?? 0;

/* กำไร */
$profit = $pdo->query("
SELECT SUM(
(oi.price - p.cost) * oi.qty
) profit
FROM order_items oi
LEFT JOIN products p ON p.id=oi.product_id
LEFT JOIN orders o ON o.id=oi.order_id
WHERE DATE(o.created_at)='$today'
")->fetch()['profit'] ?? 0;

/* สินค้าใกล้หมด */
$low = $pdo->query("
SELECT COUNT(*) c
FROM products
WHERE stock <= 5
")->fetch()['c'] ?? 0;

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Dashboard</title>

<style>
body{
margin:0;
font-family:Arial;
background:#f1f2f6;
}

.header{
background:linear-gradient(135deg,#3742fa,#2f3542);
color:white;
padding:20px;
font-size:22px;
}

.container{
padding:20px;
}

.stats{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:15px;
margin-bottom:20px;
}

.box{
background:white;
padding:20px;
border-radius:12px;
box-shadow:0 2px 8px rgba(0,0,0,.05);
}

.box .title{
color:#888;
font-size:14px;
}

.box .num{
font-size:28px;
font-weight:bold;
margin-top:5px;
}

.grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
}

.card{
background:white;
padding:25px;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
cursor:pointer;
transition:.2s;
}

.card:hover{
transform:translateY(-5px);
}

.icon{
font-size:35px;
margin-bottom:10px;
}

.pos{border-left:5px solid #3742fa}
.product{border-left:5px solid #2ed573}
.stock{border-left:5px solid #ffa502}
.report{border-left:5px solid #ff4757}
.customer{border-left:5px solid #1e90ff}
.category{border-left:5px solid #a55eea}
</style>

</head>
<body>

<div class="header">
🤖 AI BACKOFFICE DASHBOARD
</div>

<div class="container">

<!-- stats -->
<div class="stats">

<div class="box">
<div class="title">ยอดขายวันนี้</div>
<div class="num">฿<?= number_format($sales,2) ?></div>
</div>

<div class="box">
<div class="title">จำนวนบิล</div>
<div class="num"><?= $orders ?></div>
</div>

<div class="box">
<div class="title">กำไรวันนี้</div>
<div class="num">฿<?= number_format($profit,2) ?></div>
</div>

<div class="box">
<div class="title">สินค้าใกล้หมด</div>
<div class="num"><?= $low ?></div>
</div>

</div>

<!-- menu -->
<div class="grid">

<div class="card pos" onclick="go('pos.php')">
<div class="icon">🖥️</div>
POS ขายสินค้า
</div>

<div class="card product" onclick="go('products.php')">
<div class="icon">📦</div>
สินค้า
</div>

<div class="card stock" onclick="go('stock_logs.php')">
<div class="icon">📊</div>
Stock Log
</div>

<div class="card stock" onclick="go('admin_products.php')">
<div class="icon">👤📊</div>
จัดการหลังบ้าน
</div>

<div class="card report" onclick="go('daily_report.php')">
<div class="icon">📈</div>
รายงาน
</div>

<div class="card customer" onclick="go('customers.php')">
<div class="icon">👤</div>
ลูกค้า
</div>

<div class="card category" onclick="go('categories.php')">
<div class="icon">📂</div>
หมวดหมู่

</div>

<div class="card barcode" onclick="go('barcode.php')">
<div class="icon">🏷️</div>
ดูบาร์โค้ดสินค้า
</div>

</div>

</div>

<script>
function go(url){
location=url
}
</script>

</body>
</html>