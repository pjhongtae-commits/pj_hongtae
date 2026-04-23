<?php
require "config/db.php";

/* ================= FILTER ================= */
$where = "1";

if(!empty($_GET['product'])){
$where .= " AND p.name LIKE '%".$_GET['product']."%'";
}

if(!empty($_GET['type'])){
$where .= " AND s.ref_type='".$_GET['type']."'";
}

/* ================= HANDLE ACTION ================= */

// 🔁 คืนสินค้า
if(isset($_POST['return'])){
    $id = $_POST['product_id'];
    $qty = intval($_POST['qty']);

    $p = $pdo->query("SELECT * FROM products WHERE id=$id")->fetch();

    $before = $p['stock'];
    $after = $before + $qty;

    $pdo->query("UPDATE products SET stock=$after WHERE id=$id");

    $stmt = $pdo->prepare("
        INSERT INTO stock_logs
        (product_id,type,qty,stock_before,stock_after,ref_type,note,source)
        VALUES (?,?,?,?,?,'return','คืนสินค้า','MANUAL')
    ");
    $stmt->execute([$id,'in',$qty,$before,$after]);

    header("location:stock_logs.php");
    exit;
}

// ⚙️ ปรับสต๊อก
if(isset($_POST['adjust'])){
    $id = $_POST['product_id'];
    $newStock = intval($_POST['qty']);
    $note = $_POST['note'];

    $p = $pdo->query("SELECT * FROM products WHERE id=$id")->fetch();

    $before = $p['stock'];
    $after = $newStock;
    $diff = $after - $before;

    $pdo->query("UPDATE products SET stock=$after WHERE id=$id");

    $stmt = $pdo->prepare("
        INSERT INTO stock_logs
        (product_id,type,qty,stock_before,stock_after,ref_type,note,source)
        VALUES (?,?,?,?,?,'adjust',?,'MANUAL')
    ");
    $stmt->execute([$id,'adjust',$diff,$before,$after,$note]);

    header("location:stock_logs.php");
    exit;
}

/* ================= DATA ================= */
$logs = $pdo->query("
SELECT s.*,p.name
FROM stock_logs s
LEFT JOIN products p ON p.id=s.product_id
WHERE $where
ORDER BY s.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Stock Logs App</title>

<style>
body{
margin:0;
font-family:Arial;
background:#f4f6fb;
}

/* HEADER */
.header{
background:linear-gradient(135deg,#3742fa,#2f3542);
color:#fff;
padding:16px;
font-size:18px;
font-weight:bold;
}

/* TABLE */
table{
width:100%;
border-collapse:collapse;
background:white;
font-size:13px;
}

th{
background:#f1f3f6;
padding:10px;
position:sticky;
top:0;
}

td{
padding:10px;
border-bottom:1px solid #eee;
text-align:center;
}

tr:hover{
background:#f5f8ff;
}

/* BADGE */
.badge{
padding:4px 8px;
border-radius:6px;
color:white;
font-size:11px;
}

.b-sale{background:#ff4757}
.b-add{background:#2ed573}
.b-adjust{background:#ffa502}
.b-return{background:#1e90ff}

/* FLOAT BUTTON */
.fab{
position:fixed;
bottom:80px;
right:20px;
width:55px;
height:55px;
border-radius:50%;
background:#3742fa;
color:white;
font-size:28px;
display:flex;
align-items:center;
justify-content:center;
cursor:pointer;
box-shadow:0 8px 20px rgba(0,0,0,.2);
}

/* MODAL */
.modal{
display:none;
position:fixed;
top:0;left:0;
width:100%;height:100%;
background:rgba(0,0,0,.5);
z-index:999;
}

.modal-content{
background:white;
width:90%;
max-width:420px;
margin:80px auto;
padding:15px;
border-radius:12px;
}

.modal-title{
font-weight:bold;
margin-bottom:10px;
}

input,select{
width:100%;
padding:10px;
margin:6px 0;
border:1px solid #ddd;
border-radius:8px;
}

button{
width:100%;
padding:10px;
border:none;
border-radius:8px;
background:#3742fa;
color:white;
font-weight:bold;
cursor:pointer;
}

.btn2{
background:#ffa502;
}

.close{
float:right;
cursor:pointer;
color:red;
}

</style>
</head>
<link rel="stylesheet" href="assets/style.css"> <!-- menu -->
<body>

<div class="header">📦 Stock Logs (APP MODE)</div>

<!-- TABLE -->
<table>
<tr>
<th>ID</th>
<th>สินค้า</th>
<th>ประเภท</th>
<th>จำนวน</th>
<th>ก่อน</th>
<th>หลัง</th>
<th>เวลา</th>
</tr>

<?php foreach($logs as $r): ?>
<tr>

<td><?= $r['id'] ?></td>
<td><?= $r['name'] ?></td>

<td>
<?php
$type = $r['ref_type'] ?? $r['type'] ?? '';

if($type == 'sale'){
    echo '<span class="tag sale">🔴 SALE</span>';
}
elseif($type == 'restock' || $type == 'add'){
    echo '<span class="tag restock">🟢 STOCK</span>';
}
elseif($type == 'adjust'){
    echo '<span class="tag adjust">🟡 ADJUST</span>';
}
elseif($type == 'return'){
    echo '<span class="tag return">🔵 RETURN</span>';
}
elseif($type == 'auto'){
    echo '<span class="tag auto">🤖 AUTO</span>';
}
else{
    echo '<span class="tag auto">🤖 AI AUTO BUTTON</span>';
}
?>
</td>

<td><?= $r['qty'] ?></td>
<td><?= $r['stock_before'] ?></td>
<td><?= $r['stock_after'] ?></td>
<td><?= $r['created_at'] ?></td>

</tr>
<?php endforeach; ?>

</table>

<!-- FLOAT BUTTON -->
<div class="fab" onclick="openModal()">+</div>

<!-- MODAL -->
<div class="modal" id="modal">

<div class="modal-content">

<div class="modal-title">
⚙️ จัดการสต๊อก
<span class="close" onclick="closeModal()">✖</span>
</div>

<!-- RETURN -->
<form method="post">
<select name="product_id">
<?php foreach($products as $p): ?>
<option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
<?php endforeach; ?>
</select>

<input type="number" name="qty" placeholder="จำนวน">
<button name="return">🔁 คืนสินค้า</button>
</form>

<hr>

<!-- ADJUST -->
<form method="post">
<select name="product_id">
<?php foreach($products as $p): ?>
<option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
<?php endforeach; ?>
</select>

<input type="number" name="qty" placeholder="จำนวนใหม่">
<input type="text" name="note" placeholder="เหตุผล">

<button name="adjust" class="btn2">⚙️ ปรับสต๊อก</button>
</form>

</div>

</div>

<script>
function openModal(){
document.getElementById('modal').style.display='block';
}

function closeModal(){
document.getElementById('modal').style.display='none';
}
</script>
<?php include "components/bottom_menu.php"; ?>
</body>
</html>