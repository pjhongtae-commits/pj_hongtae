<?php
require "config/db.php";

$where = "1";

if(!empty($_GET['product'])){
$where .= " AND p.name LIKE '%".$_GET['product']."%'";
}

if(!empty($_GET['type'])){
$where .= " AND s.type='".$_GET['type']."'";
}

$logs = $pdo->query("
SELECT s.*,p.name
FROM stock_logs s
LEFT JOIN products p ON p.id=s.product_id
WHERE $where
ORDER BY s.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Stock Logs</title>

<style>
body{
font-family:Arial;
background:#f1f2f6;
margin:0;
}

.header{
background:#2f3542;
color:white;
padding:15px;
font-size:20px;
}

.container{
padding:20px;
}

.card{
background:white;
padding:15px;
border-radius:8px;
box-shadow:0 2px 5px rgba(0,0,0,.1);
margin-bottom:15px;
}

input,select{
padding:8px;
border:1px solid #ddd;
border-radius:5px;
}

button{
padding:8px 15px;
border:0;
background:#3742fa;
color:white;
border-radius:5px;
cursor:pointer;
}

table{
width:100%;
border-collapse:collapse;
background:white;
}

th{
background:#f1f2f6;
text-align:left;
padding:10px;
}

td{
padding:10px;
border-bottom:1px solid #eee;
}

.sale{
color:#ff4757;
font-weight:bold;
}

.add{
color:#2ed573;
font-weight:bold;
}

.adjust{
color:#ffa502;
font-weight:bold;
}

.qty-minus{
color:#ff4757;
}

.qty-plus{
color:#2ed573;
}

.top{
display:flex;
gap:10px;
margin-bottom:10px;
}

.badge{
padding:4px 8px;
border-radius:5px;
color:white;
font-size:12px;
}

.b-sale{background:#ff4757}
.b-add{background:#2ed573}
.b-adjust{background:#ffa502}
</style>

</head>
<body>

<div class="header">
📦 Stock Movement Logs
</div>

<div class="container">

<div class="card">

<form>

<div class="top">

<input name="product"
placeholder="ค้นหาสินค้า"
value="<?= $_GET['product'] ?? '' ?>">

<select name="type">
<option value="">ทุกประเภท</option>
<option value="sale">ขาย</option>
<option value="add">เพิ่ม</option>
<option value="adjust">ปรับ</option>
</select>

<button>ค้นหา</button>

<a href="stock_logs.php">
<button type="button">รีเซ็ต</button>
</a>

</div>

</form>

</div>

<table>

<tr>
<th>ID</th>
<th>สินค้า</th>
<th>ประเภท</th>
<th>จำนวน</th>
<th>หมายเหตุ</th>
<th>เวลา</th>
</tr>

<?php foreach($logs as $r): ?>

<tr>

<td><?= $r['id'] ?></td>

<td><?= $r['name'] ?></td>

<td>

<?php if($r['type']=='sale'): ?>
<span class="badge b-sale">ขาย</span>
<?php endif; ?>

<?php if($r['type']=='add'): ?>
<span class="badge b-add">เพิ่ม</span>
<?php endif; ?>

<?php if($r['type']=='adjust'): ?>
<span class="badge b-adjust">ปรับ</span>
<?php endif; ?>

</td>

<td class="<?= $r['qty']<0?'qty-minus':'qty-plus' ?>">
<?= $r['qty'] ?>
</td>

<td><?= $r['note'] ?></td>

<td><?= $r['created_at'] ?></td>

</tr>

<?php endforeach; ?>

</table>

</div>

</body>
</html>