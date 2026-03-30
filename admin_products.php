<?php
require "config/db.php";
require_once "modules/products.php";
require_once "modules/ai.php";

// ------------------ Stats + สินค้า + AI ------------------
$today = date("Y-m-d");
$sales = $pdo->query("SELECT SUM(total) t FROM orders WHERE DATE(created_at)='$today'")->fetch()['t'] ?? 0;
$orders = $pdo->query("SELECT COUNT(*) c FROM orders WHERE DATE(created_at)='$today'")->fetch()['c'] ?? 0;
$profit_today = $pdo->query("
SELECT SUM((oi.price - p.cost) * oi.qty) profit
FROM order_items oi
LEFT JOIN products p ON p.id=oi.product_id
LEFT JOIN orders o ON o.id=oi.order_id
WHERE DATE(o.created_at)='$today'
")->fetch()['profit'] ?? 0;
$low = $pdo->query("SELECT COUNT(*) c FROM products WHERE stock <= 5")->fetch()['c'] ?? 0;

$products = getAllProducts($pdo);
$ai_results = aiPromotionAndReorder($products, $pdo);
$profit_per_product = calcProfitPerProduct($products, $pdo);

// กราฟยอดขาย 7 วัน
$sales7days = $pdo->query("
SELECT DATE(created_at) date, SUM(total) total
FROM orders
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DATE(created_at)
")->fetchAll(PDO::FETCH_ASSOC);
$dates = array_column($sales7days,'date');
$totals = array_column($sales7days,'total');
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Admin Products - AI Backoffice</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body{margin:0;font-family:Arial;background:#f4f4f4;padding:20px;}
.header{background:linear-gradient(135deg,#3742fa,#2f3542);color:white;padding:20px;font-size:22px;}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:15px;margin-bottom:20px;}
.box{background:white;padding:20px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.05);}
.box .title{color:#888;font-size:14px;}
.box .num{font-size:28px;font-weight:bold;margin-top:5px;}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:30px;}
.card{background:white;padding:25px;border-radius:15px;box-shadow:0 5px 15px rgba(0,0,0,.08);cursor:pointer;transition:.2s;text-align:center;text-decoration:none;color:#333;}
.card:hover{transform:translateY(-5px);}
.icon{font-size:35px;margin-bottom:10px;}
.modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);}
.modal-content { background:white; margin:10% auto; padding:20px; border-radius:10px; width:400px; position:relative;}
.modal-header{font-weight:bold;margin-bottom:15px;}
.close{position:absolute;top:10px;right:15px;font-size:18px;cursor:pointer;}
input, select{width:100%;padding:8px;margin-bottom:10px;border-radius:5px;border:1px solid #ccc;}
button{padding:10px 15px;background:#3742fa;color:white;border:none;border-radius:5px;cursor:pointer;}
button:hover{background:#2f3542;}
table{background:white;width:100%;border-collapse:collapse;margin-top:10px;}
table th{background:#3742fa;color:white;}
table th, table td{padding:8px;border:1px solid #ddd;text-align:center;}
.low-stock{background:#f8d7da;}
.medium-stock{background:#fff3cd;}
.good-stock{background:#d4edda;}
</style>
</head>
<body>

<div class="header">🤖 AI BACKOFFICE DASHBOARD</div>

<!-- Stats -->
<div class="stats">
<div class="box"><div class="title">ยอดขายวันนี้</div><div class="num">฿<?= number_format($sales,2) ?></div></div>
<div class="box"><div class="title">จำนวนบิลวันนี้</div><div class="num"><?= $orders ?></div></div>
<div class="box"><div class="title">กำไรวันนี้</div><div class="num">฿<?= number_format($profit_today,2) ?></div></div>
<div class="box"><div class="title">สินค้าใกล้หมด</div><div class="num"><?= $low ?></div></div>
</div>

<!-- Menu Card -->
<div class="grid">
<div class="card" onclick="go('pos.php')"><div class="icon">🖥️</div>POS ขายสินค้า</div>
<div class="card" onclick="go('products.php')"><div class="icon">📦</div>สินค้า</div>
<div class="card" onclick="go('stock_logs.php')"><div class="icon">📊</div>Stock Log</div>
<div class="card" onclick="go('daily_report.php')"><div class="icon">📈</div>รายงาน</div>
<div class="card" onclick="go('customers.php')"><div class="icon">👤</div>ลูกค้า</div>
<div class="card" onclick="go('categories.php')"><div class="icon">📂</div>หมวดหมู่</div>
<div class="card" onclick="go('barcode.php')"><div class="icon">🏷️</div>ดูบาร์โค้ดสินค้า</div>
</div>

<!-- กราฟยอดขาย 7 วัน -->
<h2>กราฟยอดขาย 7 วัน</h2>
<canvas id="salesChart" style="background:white;padding:15px;border-radius:10px;"></canvas>

<!-- ตารางสินค้า + AI Recommendation + Reorder (ดูอย่างเดียว) -->
<h2>สินค้าทั้งหมด + AI ช่วยคำนวน จากยอดขาย 7 วัน</h2>
<table>
<tr>
<th>สินค้า</th><th>ราคา</th><th>ต้นทุน</th><th>สต็อก</th><th>AI คำนวนเสนอ Promotion</th><th>กำไรรวม</th><th>AI เสนอจำนวน</th>
</tr>

<?php foreach($products as $p):
    $stockClass = $p['stock'] <=5 ? 'low-stock' : ($p['stock']<=20 ? 'medium-stock' : 'good-stock');
    $ai = $ai_results[$p['id']] ?? ['recommendation'=>'','reorder_qty'=>0];
    $profit = $profit_per_product[$p['id']] ?? 0;
?>
<tr class="<?= $stockClass ?>">
<td><?= htmlspecialchars($p['name']) ?></td>
<td>฿<?= number_format($p['price'],2) ?></td>
<td>฿<?= number_format($p['cost'],2) ?></td>
<td><?= $p['stock'] ?></td>
<td><?= htmlspecialchars($ai['recommendation']) ?></td>
<td>฿<?= number_format($profit,2) ?></td>
<td><?= $ai['reorder_qty'] ?></td>
</tr>
<?php endforeach; ?>
</table>

<script>
function openModal(){ document.getElementById('addModal').style.display='block'; }
function closeModal(){ document.getElementById('addModal').style.display='none'; }
function go(url){ location=url; }

// Chart.js
const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx,{
type:'line',
data:{
labels: <?= json_encode($dates) ?>,
datasets:[{label:'ยอดขาย (฿)',data: <?= json_encode($totals) ?>,backgroundColor:'rgba(55,66,250,0.2)',borderColor:'#3742fa',borderWidth:2,fill:true,tension:0.3}]
},
options:{responsive:true,scales:{y:{beginAtZero:true}}}
});
</script>

</body>
</html>