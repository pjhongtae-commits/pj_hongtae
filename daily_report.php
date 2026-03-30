<?php
require "config/db.php";

$today = date("Y-m-d");

// ยอดขายรวมวันนี้
$sales = $pdo->query("
SELECT SUM(total) total
FROM orders
WHERE DATE(created_at)='$today'
")->fetch()['total'] ?? 0;

// จำนวนบิลวันนี้
$orders = $pdo->query("
SELECT COUNT(*) c
FROM orders
WHERE DATE(created_at)='$today'
")->fetch()['c'] ?? 0;

// สินค้าที่ขายวันนี้ เรียงตามจำนวน
$items = $pdo->query("
SELECT p.name, SUM(oi.qty) qty
FROM order_items oi
JOIN products p ON p.id=oi.product_id
JOIN orders o ON o.id=oi.order_id
WHERE DATE(o.created_at)='$today'
GROUP BY p.name
ORDER BY qty DESC
LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// สำหรับ Chart.js
$chart_labels = array_column($items, 'name');
$chart_data = array_column($items, 'qty');
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Daily Report Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body{font-family:Arial;background:#f4f4f4;padding:20px;}
.header{background:linear-gradient(135deg,#3742fa,#2f3542);color:white;padding:20px;font-size:22px;border-radius:12px;}
.stats{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin:20px 0;}
.box{background:white;padding:20px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.05);}
.box .title{color:#888;font-size:14px;}
.box .num{font-size:28px;font-weight:bold;margin-top:5px;}
.table-items{background:white;padding:15px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.05);margin-bottom:20px;}
.table-items td, .table-items th{padding:8px;border-bottom:1px solid #ddd;text-align:left;}
.table-items th{background:#3742fa;color:white;}
ul{background:white;padding:15px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.05);}
li{margin-bottom:8px;}
canvas{background:white;padding:15px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.05);margin-bottom:20px;}
h2,h3{color:#3742fa;}
hr{margin:20px 0;}
</style>
</head>
<body>

<div class="header">📊 DAILY REPORT - <?= $today ?></div>

<!-- Stats -->
<div class="stats">
<div class="box">
    <div class="title">ยอดขายวันนี้</div>
    <div class="num">฿<?= number_format($sales,2) ?></div>
</div>
<div class="box">
    <div class="title">จำนวนบิลวันนี้</div>
    <div class="num"><?= $orders ?></div>
</div>
</div>

<hr>

<!-- ตารางสินค้าขายดี -->
<h3>สินค้าที่ขายดีที่สุด Top 5</h3>
<table class="table-items">
<tr><th>สินค้า</th><th>จำนวนขาย</th></tr>
<?php foreach($items as $i): ?>
<tr>
<td><?= htmlspecialchars($i['name']) ?></td>
<td><?= $i['qty'] ?></td>
</tr>
<?php endforeach; ?>
</table>

<!-- กราฟสินค้าขายดี Top 5 -->
<h3>กราฟสินค้าขายดี Top 5</h3>
<canvas id="top5Chart"></canvas>

<hr>

<!-- UL แนวทางพัฒนาต่อยอด -->
<h3>แนวทางพัฒนาต่อยอดระบบ</h3>
<ul>
<li>เพิ่มระบบแจ้งเตือนสต็อกใกล้หมดแบบอัตโนมัติ</li>
<li>วิเคราะห์ยอดขายสินค้าขายดี-ขายไม่ดี ด้วย AI</li>
<li>เพิ่มฟิลเตอร์หรือค้นหาสินค้าในรายงาน</li>
<li>รวมข้อมูล Promotion และ Reorder จาก AI เป็น Dashboard</li>
<li>รายงานกำไร/ขาดทุนรายสัปดาห์หรือรายเดือน</li>
<li>ส่งอีเมลรายงานอัตโนมัติให้ผู้บริหาร</li>
<li>Export ข้อมูลเป็น Excel หรือ PDF</li>
<li>ปรับ UI ให้รองรับมือถือและแท็บเล็ต</li>
<li>เชื่อมข้อมูลลูกค้า/คำสั่งซื้อเพื่อวิเคราะห์แนวโน้มการซื้อ</li>
</ul>

<script>
// Chart.js สำหรับ Top 5 สินค้าขายดี
const ctx = document.getElementById('top5Chart').getContext('2d');
const top5Chart = new Chart(ctx,{
    type:'bar',
    data:{
        labels: <?= json_encode($chart_labels) ?>,
        datasets:[{
            label:'จำนวนขาย',
            data: <?= json_encode($chart_data) ?>,
            backgroundColor:'rgba(55,66,250,0.6)',
            borderColor:'#3742fa',
            borderWidth:1
        }]
    },
    options:{
        responsive:true,
        scales:{y:{beginAtZero:true}},
        plugins:{legend:{display:false}}
    }
});
</script>

</body>
</html>