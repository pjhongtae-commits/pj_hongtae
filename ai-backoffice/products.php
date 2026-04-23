<?php
require "config/db.php";
require "services/pricing.php";

/* ================= SAVE ================= */
if(isset($_POST['save'])){

$id = $_POST['id'] ?? '';
$name = $_POST['name'];
$price = $_POST['price'];
$cost = $_POST['cost'];
$stock = $_POST['stock'];
$category = $_POST['category'];
$barcode = $_POST['barcode'];

$shipping = $_POST['shipping_cost'];
$packaging = $_POST['packaging_cost'];
$profit_percent = $_POST['profit_percent'];
$use_ai = $_POST['use_ai'];

$image = $_POST['old_image'] ?? '';

if(!empty($_FILES['image']['name'])){
$image = time().$_FILES['image']['name'];
move_uploaded_file($_FILES['image']['tmp_name'],"uploads/".$image);
}

/* AI */
if($use_ai){
    $price = calculateSellingPrice(
        $cost,$shipping,$packaging,10,$profit_percent
    );
}

/* UPDATE */
if($id){

$stmt=$pdo->prepare("
UPDATE products SET
name=?,price=?,cost=?,stock=?,category_id=?,barcode=?,image=?,
shipping_cost=?,packaging_cost=?,profit_percent=?,use_ai=?
WHERE id=?
");

$stmt->execute([
$name,$price,$cost,$stock,$category,$barcode,$image,
$shipping,$packaging,$profit_percent,$use_ai,$id
]);

}else{

$stmt=$pdo->prepare("
INSERT INTO products
(name,price,cost,stock,category_id,barcode,image,
shipping_cost,packaging_cost,profit_percent,use_ai)
VALUES (?,?,?,?,?,?,?,?,?,?,?)
");

$stmt->execute([
$name,$price,$cost,$stock,$category,$barcode,$image,
$shipping,$packaging,$profit_percent,$use_ai
]);
}

header("location:products.php");
exit;
}

/* ================= DELETE ================= */
if(isset($_GET['del'])){
$pdo->query("DELETE FROM products WHERE id=".$_GET['del']);
header("location:products.php");
exit;
}

/* ================= DATA ================= */
$products = $pdo->query("
SELECT p.*,c.name cat
FROM products p
LEFT JOIN categories c ON c.id=p.category_id
ORDER BY p.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$cats = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

/* ================= STOCK ALERT ================= */
$low_stock = $pdo->query("SELECT COUNT(*) c FROM products WHERE stock <= 5")->fetch()['c'] ?? 0;

$low_items = $pdo->query("
SELECT name, stock FROM products 
WHERE stock <= 5 
ORDER BY stock ASC
LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Products App</title>

<style>
body{font-family:Arial;background:#f4f6fb;margin:0}

/* HEADER */
.header{
background:#111827;
color:white;
padding:15px;
font-size:18px;
}

/* ALERT */
.alert{
background:#fff3cd;
border:1px solid #ffeeba;
padding:10px;
margin:10px;
border-radius:10px;
}

/* TABLE */
table{
width:100%;
border-collapse:collapse;
background:white;
margin-top:10px;
}

th,td{
padding:10px;
border:1px solid #e5e7eb;
text-align:center;
font-size:14px;
}

th{
background:#f9fafb;
}

tr:hover{
background:#f3f6ff;
}

/* STOCK COLORS */
.low-stock{background:#ffe5e5 !important;}
.medium-stock{background:#fff6d6 !important;}

/* IMAGE */
.preview{
width:45px;height:45px;
object-fit:cover;border-radius:8px;
}

/* BUTTON */
.actions{
display:flex;
justify-content:center;
gap:6px;
}

.icon-btn{
border:none;
padding:6px 8px;
border-radius:6px;
cursor:pointer;
}

.edit{background:#dbeafe;color:#1d4ed8}
.del{background:#fee2e2;color:#b91c1c}

/* FAB */
.fab{
position:fixed;
right:20px;
bottom:80px;
background:#3b82f6;
color:white;
width:55px;height:55px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
font-size:28px;
cursor:pointer;
}

/* MODAL */
.modal{
display:none;
position:fixed;
inset:0;
background:rgba(0,0,0,.5);
}

.modal-content{
background:white;
width:95%;
max-width:600px;
margin:50px auto;
padding:15px;
border-radius:12px;
}

.grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:10px;
}

input,select{
padding:8px;
border:1px solid #ddd;
border-radius:8px;
width:100%;
}

.save{
margin-top:10px;
width:100%;
background:#111827;
color:white;
padding:10px;
border:none;
border-radius:8px;
}
</style>
</head>
<link rel="stylesheet" href="assets/style.css"> <!-- menu -->
<body>

<div class="header">📦 Products (APP MODE)</div>

<!-- ALERT -->
<?php if($low_stock > 0): ?>
<div class="alert">
⚠️ สินค้าใกล้หมด <b><?= $low_stock ?></b> รายการ<br><br>

<?php foreach($low_items as $l): ?>
• <?= $l['name'] ?> (เหลือ <?= $l['stock'] ?>)<br>
<?php endforeach; ?>

</div>
<?php endif; ?>

<!-- TABLE -->
<table>
<tr>
<th>รูป</th>
<th>ชื่อ</th>
<th>ราคา</th>
<th>ทุน</th>
<th>Stock</th>
<th>AI</th>
<th>Action</th>
</tr>

<?php foreach($products as $p):

$class = "";
if($p['stock'] <= 5) $class = "low-stock";
elseif($p['stock'] <= 9) $class = "medium-stock";
?>

<tr class="<?= $class ?>">

<td>
<?php if($p['image']): ?>
<img src="uploads/<?= $p['image'] ?>" class="preview">
<?php endif; ?>
</td>

<td><?= $p['name'] ?></td>
<td><?= $p['price'] ?></td>
<td><?= $p['cost'] ?></td>

<td>
<?= $p['stock'] ?>
<?php if($p['stock'] <= 5): ?>
⚠️
<?php endif; ?>
</td>

<td><?= $p['use_ai']?'🤖':'✍️' ?></td>

<td>
<div class="actions">

<button class="icon-btn edit"
onclick="openEdit(<?= htmlspecialchars(json_encode($p)) ?>)">
✏️
</button>

<button class="icon-btn del"
onclick="if(confirm('ลบ?')) location='?del=<?= $p['id'] ?>'">
🗑️
</button>

</div>
</td>

</tr>

<?php endforeach; ?>

</table>

<!-- FAB -->
<div class="fab" onclick="openAdd()">+</div>

<!-- MODAL -->
<div class="modal" id="modal">
<div class="modal-content">

<h3 id="title">📦 จัดการสินค้า</h3>

<form method="post" enctype="multipart/form-data">

<input type="hidden" name="id" id="id">
<input type="hidden" name="old_image" id="old_image">

<div class="grid">

<!-- ===== ข้อมูลพื้นฐาน ===== -->
<input name="name" id="name" placeholder="ชื่อสินค้า">
<input name="sku" id="sku" placeholder="SKU">
<input name="barcode" id="barcode" placeholder="Barcode">

<!-- ===== ราคา / ต้นทุน ===== -->
<input name="price" id="price" placeholder="ราคาขาย">
<input name="cost" id="cost" placeholder="ต้นทุนสินค้า">

<!-- ===== ค่าแฝง ===== -->
<input name="packaging_cost" id="packaging_cost" placeholder="ค่ากล่อง">
<input name="shipping_cost" id="shipping_cost" placeholder="ค่าส่ง">
<input name="shopee_fee_percent" id="shopee_fee_percent" placeholder="% Shopee">

<!-- ===== กำไร ===== -->
<input name="profit_percent" id="profit_percent" placeholder="% กำไร">

<!-- ===== สต๊อก ===== -->
<input name="stock" id="stock" placeholder="สต๊อก">
<input name="min_stock" id="min_stock" placeholder="สต๊อกขั้นต่ำ">

<!-- ===== AI ===== -->
<select name="use_ai" id="use_ai">
<option value="1">🤖 AI</option>
<option value="0">Manual</option>
</select>

<!-- ===== หมวดหมู่ ===== -->
<select name="category_id" id="category">
<?php foreach($cats as $c): ?>
<option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
<?php endforeach; ?>
</select>

</div>

<!-- ===== รูป ===== -->
<input type="file" name="image">
<img id="preview_img" style="width:80px;margin:10px 0;">

<button class="save" name="save">💾 บันทึกสินค้า</button>

</form>

</div>
</div>

<script>
function openAdd(){
document.getElementById('modal').style.display='block';
document.getElementById('title').innerText='เพิ่มสินค้า';
}

function openEdit(data){

document.getElementById('modal').style.display='block';
document.getElementById('title').innerText='แก้สินค้า';

for(let k in data){
    let el=document.getElementById(k);
    if(el) el.value=data[k];
}

/* ✅ ใส่รูปเก่าเข้า hidden */
document.getElementById('old_image').value = data.image;

/* (OPTION) แสดงรูปเก่า */
if(data.image){
    document.getElementById('preview_img').src = "uploads/" + data.image;
}
}
</script>
<?php include "components/bottom_menu.php"; ?>
</body>
</html>