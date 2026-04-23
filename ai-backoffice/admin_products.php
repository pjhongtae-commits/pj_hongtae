<?php
require "config/db.php";
require_once "modules/products.php";
require_once "modules/ai.php";

/* ================= MONTH ================= */
$month = $_GET['month'] ?? date("Y-m");
$year = substr($month,0,4);
$mon = substr($month,5,2);

$start = "$year-$mon-01";
$end = date("Y-m-t", strtotime($start));

/* ================= STATS ================= */
$sales = $pdo->query("
SELECT SUM(total) t 
FROM orders 
WHERE DATE(created_at) BETWEEN '$start' AND '$end'
")->fetch()['t'] ?? 0;

$orders = $pdo->query("
SELECT COUNT(*) c 
FROM orders 
WHERE DATE(created_at) BETWEEN '$start' AND '$end'
")->fetch()['c'] ?? 0;

$profit_today = $pdo->query("
SELECT SUM((oi.price - p.cost) * oi.qty) profit
FROM order_items oi
LEFT JOIN products p ON p.id=oi.product_id
LEFT JOIN orders o ON o.id=oi.order_id
WHERE DATE(o.created_at) BETWEEN '$start' AND '$end'
")->fetch()['profit'] ?? 0;

/* ================= PRODUCTS ================= */
$low = $pdo->query("SELECT COUNT(*) c FROM products WHERE stock <= 5")->fetch()['c'] ?? 0;

$products = getAllProducts($pdo);
$ai_results = aiPromotionAndReorder($products, $pdo);
$profit_per_product = calcProfitPerProduct($products, $pdo);

/* ================= GRAPH ================= */
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
<title>Admin Products AI</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

/* ===== BASE ===== */
body{
margin:0;
font-family:Arial;
background:#f4f4f4;
padding:20px;
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
padding:18px;
font-size:22px;
border-radius:10px;
}

/* BOX */
.box{
background:#fff;
padding:15px;
border-radius:12px;
margin-bottom:15px;
box-shadow:0 2px 10px rgba(0,0,0,.05);
}

body.dark .box{
background:#1e293b;
color:#fff;
}

/* STATS */
.stats{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:10px;
margin:15px 0;
}

.num{font-size:22px;font-weight:bold}
.title{font-size:13px;color:gray}

/* ACTION BAR (AI TOOLS) */
.action-bar{
display:flex;
gap:10px;
margin:15px 0;
flex-wrap:wrap;
}

.btn{
padding:10px 12px;
border:none;
border-radius:8px;
color:#fff;
cursor:pointer;
font-weight:bold;
}

.blue{background:#3742fa}
.green{background:#2ed573}
.purple{background:#a55eea}
.orange{background:#ff9f43}
.red{background:#ff4757}

/* TABLE */
table{
width:100%;
border-collapse:collapse;
background:#fff;
border-radius:10px;
overflow:hidden;
}

th{
background:#3742fa;
color:#fff;
padding:10px;
}

td{
padding:8px;
border-bottom:1px solid #eee;
text-align:center;
}

.low-stock{background:#ffe6e6}
.medium-stock{background:#fff3cd}
.good-stock{background:#e6fffa}

/* LOADING */
.skeleton{
height:14px;
background:linear-gradient(90deg,#eee,#f5f5f5,#eee);
background-size:200% 100%;
animation:shimmer 1.2s infinite;
border-radius:6px;
}

@keyframes shimmer{
0%{background-position:200% 0}
100%{background-position:-200% 0}
}
.modal{
display:none;
position:fixed;
inset:0;
background:rgba(0,0,0,.5);
z-index:999;
}

.modal-content{
background:#fff;
width:90%;
max-width:400px;
margin:100px auto;
padding:20px;
border-radius:12px;
}

</style>
</head>
<link rel="stylesheet" href="assets/style.css"> <!-- menu -->
<body>

<div class="header">🤖 AI BACKOFFICE - PRODUCTS CONTROL</div>

<!-- ================= AI CONTROL PANEL (ย้ายมาหน้านี้แล้ว) ================= -->
<style>
.action-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:12px;
    margin:15px 0;
}

.action-card{
    background:#fff;
    padding:14px;
    border-radius:14px;
    cursor:pointer;
    box-shadow:0 4px 12px rgba(0,0,0,.06);
    transition:.25s;
    display:flex;
    flex-direction:column;
    gap:6px;
}

.action-card:hover{
    transform:translateY(-4px);
    box-shadow:0 8px 20px rgba(0,0,0,.12);
}

.icon{
    font-size:22px;
}

.title{
    font-weight:bold;
    font-size:14px;
}

.desc{
    font-size:12px;
    color:#777;
}

/* สี */
.blue{border-left:4px solid #3742fa;}
.green{border-left:4px solid #2ed573;}
.orange{border-left:4px solid #ff9f43;}
.purple{border-left:4px solid #a55eea;}
.red{border-left:4px solid #ff4757;}
.dark{border-left:4px solid #57606f;}

.filter-box{
    background:#fff;
    padding:15px;
    border-radius:14px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:10px;
    margin:15px 0;
}

.filter-left{
    display:flex;
    flex-direction:column;
}

.filter-title{
    font-size:14px;
    color:#777;
}

.filter-value{
    font-size:18px;
    font-weight:bold;
}

.filter-controls{
    display:flex;
    gap:8px;
    align-items:center;
}

.filter-controls input{
    padding:8px 10px;
    border-radius:8px;
    border:1px solid #ddd;
    font-size:14px;
}

.filter-controls button{
    background:#3742fa;
    color:#fff;
    border:none;
    padding:8px 14px;
    border-radius:8px;
    cursor:pointer;
    font-weight:bold;
}

.filter-controls button:hover{
    background:#2f3542;
}
.container{
max-width:1200px;
margin:auto;
}

.section{
margin:20px 0;
}

.section h2{
margin-bottom:10px;
font-size:18px;
}

/* MAIN BUTTON */
.main-action{
background:linear-gradient(135deg,#ff4757,#ff6b81);
color:#fff;
padding:20px;
border-radius:16px;
text-align:center;
font-size:20px;
font-weight:bold;
cursor:pointer;
box-shadow:0 8px 20px rgba(0,0,0,.15);
transition:.3s;
}

.main-action:hover{
transform:translateY(-3px);
}

/* GRID */
.grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
gap:12px;
}

.card{
background:#fff;
padding:14px;
border-radius:12px;
cursor:pointer;
box-shadow:0 4px 10px rgba(0,0,0,.05);
transition:.25s;
}

.card:hover{
transform:translateY(-3px);
}

.card .title{
font-weight:bold;
margin-top:5px;
}

.card .desc{
font-size:12px;
color:#777;
}
</style>

<div class="section">
    <div class="main-action" onclick="fixAll()">
        🚀 อัพราคาทั้งร้าน (กำไรอัตโนมัติ)
    </div>
</div>
<div class="action-grid">

<div class="action-card blue" onclick="fixAll()">
    <div class="badge">แนะนำ</div>
    <div class="icon">🤖</div>
    <div class="title">อัพราคาทั้งร้าน</div>
    <div class="desc">คำนวณราคาขายใหม่จากต้นทุน + ค่าธรรมเนียม + กำไร อัตโนมัติ</div>
</div>

<div class="action-card orange" onclick="document.getElementById('makroFile').click()">
    <input type="file" id="makroFile" style="display:none">
    <div class="icon">📦</div>
    <div class="title">อัพเดตราคาทุน</div>
    <div class="desc">นำเข้าราคาสินค้าจาก Makro เพื่อใช้คำนวณกำไรล่าสุด</div>
</div>
<a href="import_preview.php" class="action-card orange" style="text-decoration:none;">
    <div class="icon">📊</div>
    <div class="title">ดูต้นทุนล่าสุด</div>
    <div class="desc">ดูข้อมูลที่ import จาก Makro</div>
</a>
<div class="action-card purple" onclick="autoEngine()">
    <div class="icon">⚙️</div>
    <div class="title">รันระบบอัตโนมัติ</div>
    <div class="desc">ปรับราคา + เช็คสต๊อก + สั่งซื้อสินค้า ให้ครบในคลิกเดียว</div>
</div>

<!--<div class="action-card green" onclick="recalcCost()">
    <div class="icon">🔄</div>
    <div class="title">คำนวณต้นทุน</div>
    <div class="desc">อัปเดตต้นทุนสินค้าใหม่</div>
</div>-->

<div class="action-card red" onclick="optimizeMargin()">
    <div class="icon">🧠</div>
    <div class="title">เพิ่มกำไรสินค้า</div>
    <div class="desc">วิเคราะห์สินค้าและปรับราคาเพื่อเพิ่มกำไรสูงสุด</div>
</div>

<a href="manual_sale.php" class="action-card green" style="text-decoration:none;">
    <div class="icon">🧾</div>
    <div class="title">เพิ่มยอดย้อนหลัง</div>
    <div class="desc">บันทึกยอดขายย้อนหลังเข้าระบบ</div>
</a>
<a href="categories.php" class="action-card orange" style="text-decoration:none;">
    <div class="icon">🗂️</div>
    <div class="title">จัดการหมวดหมู่</div>
    <div class="desc">แยกประเภทสินค้าเพื่อจัดการและวิเคราะห์ง่ายขึ้น</div>
</a>

<div class="action-card dark" onclick="toggleDark()">
    <div class="icon">🌙</div>
    <div class="title">โหมดกลางคืน</div>
    <div class="desc">เปลี่ยนธีมหน้าจอให้สบายตา</div>
</div>
</div>
<!-- ================= MONTH ================= -->
<form method="GET">
<div class="filter-box">

    <div class="filter-left">
        <div class="filter-title">📅 ช่วงข้อมูล</div>
        <div class="filter-value">
            <?= date("F Y", strtotime($month)) ?>
        </div>
    </div>

    <div class="filter-controls">
        <input type="month" name="month" value="<?= $month ?>">
        <button type="submit">🔍 ดูข้อมูล</button>
    </div>

</div>
</form>

<!-- ================= STATS ================= -->
<div class="stats">
<div class="box"><div class="title">ยอดขาย</div><div class="num">฿<?= number_format($sales,2) ?></div></div>
<div class="box"><div class="title">บิล</div><div class="num"><?= $orders ?></div></div>
<div class="box"><div class="title">กำไร</div><div class="num">฿<?= number_format($profit_today,2) ?></div></div>
<div class="box"><div class="title">ใกล้หมด</div><div class="num"><?= $low ?></div></div>
</div>

<!-- ================= CHART ================= -->
<div class="box">
<h3>📈 Sales Chart</h3>
<canvas id="chart"></canvas>
</div>

<!-- ================= PRODUCTS ================= -->
<h3>📦 Products + AI</h3>

<input id="search" placeholder="ค้นหา">

<table>
<tr>
<th>สินค้า</th><th>ราคา</th><th>ต้นทุน</th><th>สต็อก</th><th>AI</th><th>กำไร</th><th>จัดการ</th>
</tr>

<?php foreach($products as $p):
$stockClass = $p['stock'] <=5 ? 'low-stock' : ($p['stock']<=20 ? 'medium-stock' : 'good-stock');
$ai = $ai_results[$p['id']] ?? ['recommendation'=>'','reorder_qty'=>0];
$profit = $profit_per_product[$p['id']] ?? 0;
?>

<tr class="<?= $stockClass ?>">
<td><?= $p['name'] ?></td>
<td><?= $p['price'] ?></td>
<td><?= $p['cost'] ?></td>
<td><?= $p['stock'] ?></td>
<td><?= $ai['recommendation'] ?></td>
<td><?= number_format($profit,2) ?></td>
<td>
<button title="ปรับราคาสินค้านี้ตาม AI" onclick="fixPrice(<?= $p['id'] ?>)">⚡</button>
<button title="สั่งสินค้าเพิ่มตาม AI"onclick="reorder(<?= $p['id'] ?>,<?= $ai['reorder_qty'] ?>)">📦</button>
</td>
</tr>

<?php endforeach; ?>
</table>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

/* ================= GRAPH ================= */
new Chart(document.getElementById('chart'),{
type:'line',
data:{
labels:<?= json_encode($dates) ?>,
datasets:[{
label:'Sales',
data:<?= json_encode($totals) ?>,
borderWidth:2,
tension:.3
}]
}
});

/* ================= SEARCH ================= */
document.getElementById('search').onkeyup=function(){
let v=this.value.toLowerCase();
document.querySelectorAll("table tr").forEach((r,i)=>{
if(i===0)return;
r.style.display=r.innerText.toLowerCase().includes(v)?'':'none';
});
};

/* ================= AI ACTIONS ================= */
function fixAll(){
document.getElementById('priceModeModal').style.display = 'block';
}

function importMakro(){
fetch('services/import_makro.php')
.then(r=>r.text())
.then(alert);
}

function autoEngine(){
fetch('services/auto_engine.php')
.then(r=>r.text())
.then(alert);
}

function recalcCost(){
fetch('services/recalculate.php')
.then(r=>r.text())
.then(alert);
}

function optimizeMargin(){
fetch('services/optimize_margin.php')
.then(r=>r.text())
.then(alert);
}

/* ================= DARK MODE ================= */
function toggleDark(){
document.body.classList.toggle("dark");
}

/* ================= RELOAD (light realtime) ================= */
setInterval(()=>{
fetch('services/live_products.php')
.then(r=>r.text())
.then(html=>{
// optional future realtime update
});
},10000);
function importMakro(){

let fileInput = document.getElementById('makroFile');

if(!fileInput.files.length){
alert("เลือกไฟล์ก่อน");
fileInput.click();
return;
}

let formData = new FormData();
formData.append("file", fileInput.files[0]);
formData.append("source", "excel");

fetch("services/import_makro.php", {
method: "POST",
body: formData
})
.then(res => res.text())
.then(msg => {
alert(msg);
location.reload();
});
}
function fixPrice(id){

console.log("fixPrice clicked",id);

fetch("services/recalculate_ai_single.php?id="+id)
.then(res => res.text())
.then(data => {
alert(data);
location.reload();
})
.catch(err => {
console.error(err);
alert("AI Price error");
});

}

function reorder(id,qty){

console.log("reorder clicked",id,qty);

if(!qty || qty <= 0){
alert("ไม่มีจำนวน reorder");
return;
}

fetch("services/reorder.php?id="+id+"&qty="+qty)
.then(res => res.text())
.then(data => {
alert(data);
location.reload();
})
.catch(err => {
console.error(err);
alert("Reorder error");
});

}
document.getElementById('makroFile').addEventListener('change', function () {
    let file = this.files[0];

    if (!file) return;

    let formData = new FormData();
    formData.append("file", file);

    fetch("services/import_makro.php", {
        method: "POST",
        body: formData
    })
    .then(r => r.text())
    .then(msg => {
        alert(msg);
        location.reload();
    })
    .catch(err => alert("Import Error"));
});
function startUpdate(){

let mode = document.querySelector('input[name="mode"]:checked').value;

fetch('services/recalculate_ai.php?profit='+mode)
.then(r=>r.text())
.then(msg=>{
alert(msg);
location.reload();
});

closeModal();
}

function closeModal(){
document.getElementById('priceModeModal').style.display='none';
}
document.getElementById('makroFile').addEventListener('change', function () {

    let file = this.files[0];
    if (!file) return;

    let formData = new FormData();
    formData.append("file", file);

    fetch("services/import_makro.php", {
        method: "POST",
        body: formData
    })
    .then(r => r.text())
    .then(msg => {
        alert(msg);
        location.reload();
    })
    .catch(err => {
        console.error(err);
        alert("Import Error");
    });

});
</script>

<?php include "components/bottom_menu.php"; ?>
<div class="modal" id="priceModeModal">
  <div class="modal-content">
    <h3>💰 เลือกโหมดกำไร</h3>

    <label><input type="radio" name="mode" value="15"> 🔴 แข่งราคา (15%)</label><br>
    <label><input type="radio" name="mode" value="30" checked> 🟡 ปกติ (30%)</label><br>
    <label><input type="radio" name="mode" value="50"> 🟢 กำไรสูง (50%)</label><br><br>

    <button onclick="startUpdate()">🚀 เริ่มอัพราคา</button>
    <button onclick="closeModal()">❌ ยกเลิก</button>
  </div>
</div>

</body>

</html>