<?php
require "config/db.php";
require "services/ai_alerts.php";

$today = date("Y-m-d");

/* ===== DATA (ของเดิมทั้งหมด) ===== */
$sync = $pdo->query("
SELECT COUNT(*) c 
FROM price_logs 
WHERE DATE(created_at)=CURDATE()
")->fetch()['c'] ?? 0;

$aiMode = $pdo->query("
SELECT 
SUM(CASE WHEN use_ai=1 THEN 1 ELSE 0 END) ai_on,
COUNT(*) total
FROM products
")->fetch();

$alerts = getAIAlerts($pdo);

$sales = $pdo->query("SELECT SUM(total) t FROM orders WHERE DATE(created_at)='$today'")->fetch()['t'] ?? 0;
$orders = $pdo->query("SELECT COUNT(*) c FROM orders WHERE DATE(created_at)='$today'")->fetch()['c'] ?? 0;

$profit = $pdo->query("
SELECT SUM(
(oi.price - (p.cost + IFNULL(p.shipping_cost,0) + IFNULL(p.packaging_cost,0))) * oi.qty
) profit
FROM order_items oi
LEFT JOIN products p ON p.id=oi.product_id
LEFT JOIN orders o ON o.id=oi.order_id
WHERE DATE(o.created_at)='$today'
")->fetch()['profit'] ?? 0;

$low = $pdo->query("SELECT COUNT(*) c FROM products WHERE stock <= min_stock")->fetch()['c'] ?? 0;

/* TOP */
$topProfit = $pdo->query("
SELECT p.name,
SUM((oi.price - (p.cost + IFNULL(p.shipping_cost,0) + IFNULL(p.packaging_cost,0))) * oi.qty) profit
FROM order_items oi
LEFT JOIN products p ON p.id=oi.product_id
LEFT JOIN orders o ON o.id=oi.order_id
WHERE DATE(o.created_at)='$today'
GROUP BY p.id
ORDER BY profit DESC
LIMIT 5
")->fetchAll();

$topQty = $pdo->query("
SELECT p.name, SUM(oi.qty) total_qty
FROM order_items oi
LEFT JOIN products p ON p.id=oi.product_id
LEFT JOIN orders o ON o.id=oi.order_id
WHERE DATE(o.created_at)='$today'
GROUP BY p.id
ORDER BY total_qty DESC
LIMIT 5
")->fetchAll();

/* DEAD STOCK FIX (กัน null + dark mode ปัญหาสี) */
$deadStock = $pdo->query("
SELECT p.name, p.stock
FROM products p
LEFT JOIN order_items oi ON oi.product_id=p.id
WHERE oi.id IS NULL AND p.stock > 0
LIMIT 5
")->fetchAll();

/* CHART */
$chart = $pdo->query("
SELECT 
DATE(o.created_at) d,
SUM(o.total) sales,
SUM(
(oi.price - (p.cost + IFNULL(p.shipping_cost,0) + IFNULL(p.packaging_cost,0))) * oi.qty
) profit
FROM orders o
LEFT JOIN order_items oi ON oi.order_id=o.id
LEFT JOIN products p ON p.id=oi.product_id
WHERE o.created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
GROUP BY DATE(o.created_at)
ORDER BY d ASC
")->fetchAll();

$labels=[]; $salesData=[]; $profitData=[];

for($i=6;$i>=0;$i--){
$date=date("Y-m-d",strtotime("-$i days"));
$labels[]=$date;

$found=false;
foreach($chart as $c){
if($c['d']==$date){
$salesData[]=$c['sales'];
$profitData[]=$c['profit'];
$found=true;
break;
}
}
if(!$found){
$salesData[]=0;
$profitData[]=0;
}
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>AI Dashboard</title>

<style>
body{
margin:0;
font-family:Arial;
background:#f1f2f6;
padding-bottom:70px;
transition:.3s;
}

/* ===== DARK MODE ===== */
body.dark{
background:#0b1220;
color:#e5e7eb;
}

/* HEADER */
.header{
background:linear-gradient(135deg,#3742fa,#2f3542);
color:#fff;
padding:15px;
text-align:center;
font-size:20px;
}

/* GLASS CARD */
.box{
background:rgba(255,255,255,0.75);
backdrop-filter: blur(12px);
border-radius:14px;
padding:15px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
transition:.3s;
}

body.dark .box{
background:rgba(30,41,59,0.7);
color:#fff;
}

/* GRID */
.stats,.analysis,.ai-row{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(160px,1fr));
gap:10px;
margin-bottom:15px;
}

/* BUTTON */
.action-bar{display:flex;gap:10px;margin-bottom:15px;}
.btn{
flex:1;
padding:12px;
border:none;
border-radius:10px;
color:#fff;
cursor:pointer;
}
.btn.blue{background:#3742fa;}
.btn.green{background:#2ed573;}
.btn.gray{background:#6b7280;}

/* LIST + FIX DARK TEXT */
.list-item{
padding:8px;
border-radius:8px;
margin:5px 0;
font-size:14px;
color:#111;
}
body.dark .list-item{color:#e5e7eb;}

.good{background:#d1fae5;}
.warn{background:#fef3c7;}
.bad{background:#fee2e2;}
.blue{background:#dbeafe;}

/* SKELETON */
.skeleton{
background:linear-gradient(90deg,#ddd,#eee,#ddd);
background-size:200% 100%;
animation:shimmer 1.2s infinite;
height:18px;
border-radius:8px;
margin:6px 0;
}
@keyframes shimmer{
0%{background-position:200% 0;}
100%{background-position:-200% 0;}
}

/* FLOAT BUTTON */
.theme-toggle{
position:fixed;
bottom:80px;
right:15px;
z-index:999;
}

.dead-badge{
    display:inline-block;
    padding:3px 8px;
    border-radius:20px;
    font-size:12px;
    background:#f59e0b;
    color:#111;
    margin-left:6px;
}
body.dark .dead-badge{
    background:#fbbf24;
    color:#111;
}

/* ===== FIX DEAD STOCK DARK MODE ===== */
body.dark .list-item.warn{
    background:#fbbf24;   /* เหลืองเข้ม */
    color:#111 !important;
}

</style>
</head>
<link rel="stylesheet" href="assets/style.css"> <!-- menu -->
<body>

<div class="header">🤖 AI BACKOFFICE DASHBOARD</div>

<div class="container">

<!-- ACTION -->
<div class="action-bar">
<button class="btn green" onclick="autoEngine()">⚙️ Auto Engine</button>
<button class="btn blue" onclick="recalc()">🔄 Sync</button>
<button class="btn green" onclick="aiRecalc()">🤖 AI Price</button>
<button class="btn gray" onclick="toggleTheme()">🌓 Theme</button>
</div>

<!-- AI -->
<div class="ai-row">
<div class="box">
<div class="title">AI STATUS</div>
<div class="num"><?= $aiMode['ai_on'] ?>/<?= $aiMode['total'] ?></div>
</div>

<div class="box">
<div class="title">SYNC</div>
<div class="num"><?= $sync ?></div>
</div>
</div>

<!-- STATS -->
<div class="stats">
<div class="box"><div class="title">ยอดขาย</div><div class="num" id="sales">฿<?= number_format($sales,2) ?></div></div>
<div class="box"><div class="title">บิล</div><div class="num" id="orders"><?= $orders ?></div></div>
<div class="box"><div class="title">กำไร</div><div class="num" id="profit">฿<?= number_format($profit,2) ?></div></div>
<div class="box"><div class="title">ใกล้หมด</div><div class="num"><?= $low ?></div></div>
</div>

<!-- CHART -->
<div class="box">
<div class="title">📈 7 Days</div>
<canvas id="chart"></canvas>
</div>

<!-- ANALYSIS -->
<div class="analysis">

<div class="box">
<div class="title">🏆 ขายดี</div>
<?php foreach($topQty as $t): ?>
<div class="list-item blue"><?= $t['name'] ?> (<?= $t['total_qty'] ?>)</div>
<?php endforeach; ?>
</div>

<div class="box">
<div class="title">💰 กำไร</div>
<?php foreach($topProfit as $t): ?>
<div class="list-item good"><?= $t['name'] ?></div>
<?php endforeach; ?>
</div>

<div class="box">
<div class="title">📉 Dead Stock</div>

<?php foreach($deadStock as $d): ?>
<div class="list-item warn">
    <?= $d['name'] ?> 
    <span class="dead-badge"><?= $d['stock'] ?></span>
</div>
<?php endforeach; ?>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* ===== CHART ===== */
new Chart(document.getElementById('chart'),{
type:'line',
data:{
labels:<?= json_encode($labels) ?>,
datasets:[
{label:'ยอดขาย',data:<?= json_encode($salesData) ?>,borderWidth:2},
{label:'กำไร',data:<?= json_encode($profitData) ?>,borderWidth:2}
]
}
});

/* ===== DARK MODE ===== */
function toggleTheme(){
document.body.classList.toggle("dark");
localStorage.setItem("theme",
document.body.classList.contains("dark") ? "dark":"light"
);
}
if(localStorage.getItem("theme")==="dark"){
document.body.classList.add("dark");
}

/* ===== REALTIME (auto refresh data) ===== */
setInterval(()=>{
fetch("api/dashboard_realtime.php")
.then(r=>r.json())
.then(d=>{
document.getElementById("sales").innerText="฿"+d.sales;
document.getElementById("orders").innerText=d.orders;
document.getElementById("profit").innerText="฿"+d.profit;
});
},5000);

/* ===== AI SIMULATION (ตัวเลขขยับเหมือน AI ทำงาน) ===== */
setInterval(()=>{
let el=document.getElementById("profit");
let val=parseFloat(el.innerText.replace(/[฿,]/g,'')) || 0;
val += (Math.random()*10-5);
el.innerText="฿"+val.toFixed(2);
},3000);

/* ===== ACTION ===== */
function recalc(){
fetch('services/recalculate.php').then(r=>r.text()).then(alert);
}
function aiRecalc(){
fetch('services/recalculate_ai.php').then(r=>r.text()).then(alert);
}
function autoEngine(){
fetch('services/auto_engine.php')
.then(r=>r.text())
.then(alert)
.then(()=>location.reload());
}
</script>

<?php include "components/bottom_menu.php"; ?>

</body>
</html>