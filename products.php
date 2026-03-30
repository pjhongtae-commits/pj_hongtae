<?php
require "config/db.php";

/* SAVE */
if(isset($_POST['save'])){

$id = $_POST['id'] ?? '';
$name = $_POST['name'];
$price = $_POST['price'];
$cost = $_POST['cost'];
$stock = $_POST['stock'];
$category = $_POST['category'];
$barcode = $_POST['barcode'];

$image = $_POST['old_image'] ?? '';

if(!empty($_FILES['image']['name'])){
$image = time().$_FILES['image']['name'];
move_uploaded_file($_FILES['image']['tmp_name'],"uploads/".$image);
}

/* UPDATE */
if($id){

$old = $pdo->query("SELECT stock FROM products WHERE id=$id")->fetch()['stock'];
$diff = $stock - $old;

$stmt=$pdo->prepare("
UPDATE products SET
name=?,
price=?,
cost=?,
stock=?,
category_id=?,
barcode=?,
image=?
WHERE id=?
");

$stmt->execute([
$name,
$price,
$cost,
$stock,
$category,
$barcode,
$image,
$id
]);

if($diff != 0){
$pdo->prepare("
INSERT INTO stock_logs(product_id,type,qty,note)
VALUES (?,?,?,?)
")->execute([
$id,
'adjust',
$diff,
'ปรับสต๊อก'
]);
}

}else{

$stmt=$pdo->prepare("
INSERT INTO products
(name,price,cost,stock,category_id,barcode,image)
VALUES (?,?,?,?,?,?,?)
");

$stmt->execute([
$name,
$price,
$cost,
$stock,
$category,
$barcode,
$image
]);

$pid = $pdo->lastInsertId();

$pdo->prepare("
INSERT INTO stock_logs(product_id,type,qty,note)
VALUES (?,?,?,?)
")->execute([
$pid,
'add',
$stock,
'เพิ่มสินค้า'
]);

}

header("location:products.php");
exit;
}

/* DELETE */
if(isset($_GET['del'])){
$pdo->query("DELETE FROM products WHERE id=".$_GET['del']);
header("location:products.php");
}

/* EDIT */
$edit=null;
if(isset($_GET['edit'])){
$s=$pdo->prepare("SELECT * FROM products WHERE id=?");
$s->execute([$_GET['edit']]);
$edit=$s->fetch();
}

/* DATA */
$products = $pdo->query("
SELECT p.*,c.name cat
FROM products p
LEFT JOIN categories c ON c.id=p.category_id
ORDER BY p.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$cats = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Products</title>

<style>
body{font-family:Arial;background:#f1f2f6;margin:0}
.header{background:#2f3542;color:white;padding:15px;font-size:20px}
.container{padding:20px}
.card{
background:white;
padding:15px;
border-radius:10px;
margin-bottom:15px;
box-shadow:0 2px 8px rgba(0,0,0,.05);
}

.grid{
display:grid;
grid-template-columns:repeat(7,1fr);
gap:10px;
}

input,select{
padding:10px;
border:1px solid #ddd;
border-radius:6px;
width:100%;
}

button{
padding:10px;
border:0;
background:#3742fa;
color:white;
border-radius:6px;
cursor:pointer;
}

table{
width:100%;
border-collapse:collapse;
background:white;
}

th,td{
padding:10px;
border-bottom:1px solid #eee;
}

.preview{
width:60px;
height:60px;
object-fit:cover;
border-radius:6px;
}

.low{background:#fff3f3}

.profit{
color:#2ed573;
font-weight:bold;
}

.loss{
color:#ff4757;
font-weight:bold;
}

.drop{
border:2px dashed #ccc;
padding:10px;
text-align:center;
border-radius:6px;
cursor:pointer;
}
</style>

</head>
<body>

<div class="header">
📦 จัดการสินค้า
</div>

<div class="container">

<div class="card">

<form method="post" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
<input type="hidden" name="old_image" value="<?= $edit['image'] ?? '' ?>">

<div class="grid">

<input name="name" placeholder="ชื่อสินค้า"
value="<?= $edit['name'] ?? '' ?>">

<input name="price" placeholder="ราคาขาย"
value="<?= $edit['price'] ?? '' ?>">

<input name="cost" placeholder="ต้นทุน"
value="<?= $edit['cost'] ?? '' ?>">

<input name="stock" placeholder="Stock"
value="<?= $edit['stock'] ?? '' ?>">

<select name="category">
<option value="">หมวดหมู่</option>
<?php foreach($cats as $c): ?>
<option value="<?= $c['id'] ?>"
<?= (($edit['category_id']??'')==$c['id'])?'selected':'' ?>>
<?= $c['name'] ?>
</option>
<?php endforeach; ?>
</select>

<input name="barcode" placeholder="Barcode"
value="<?= $edit['barcode'] ?? '' ?>">

<div class="drop" ondrop="drop(event)" ondragover="allowDrop(event)">
ลากรูป
<input type="file" name="image" onchange="preview(event)">
</div>

</div>

<br>

<img id="imgPreview"
src="uploads/<?= $edit['image'] ?? '' ?>"
class="preview">

<br><br>

<button name="save">
<?= $edit?'อัปเดต':'เพิ่มสินค้า' ?>
</button>

</form>

</div>

<div class="card">

<table>

<tr>
<th>รูป</th>
<th>ชื่อ</th>
<th>ขาย</th>
<th>ทุน</th>
<th>กำไร</th>
<th>Stock</th>
<th></th>
</tr>

<?php foreach($products as $p):

$profit = $p['price'] - $p['cost'];
?>

<tr class="<?= $p['stock']<=5?'low':'' ?>">

<td>
<?php if($p['image']): ?>
<img src="uploads/<?= $p['image'] ?>" class="preview">
<?php endif; ?>
</td>

<td><?= $p['name'] ?></td>

<td><?= $p['price'] ?></td>

<td><?= $p['cost'] ?></td>

<td class="<?= $profit<0?'loss':'profit' ?>">
<?= $profit ?>
</td>

<td><?= $p['stock'] ?></td>

<td>
<a href="?edit=<?= $p['id'] ?>">แก้ไข</a>
<a href="?del=<?= $p['id'] ?>" onclick="return confirm('ลบ?')">ลบ</a>
</td>

</tr>

<?php endforeach; ?>

</table>

</div>

</div>

<script>
function preview(e){
imgPreview.src = URL.createObjectURL(e.target.files[0])
}

function allowDrop(e){
e.preventDefault()
}

function drop(e){
e.preventDefault()
let file = e.dataTransfer.files[0]
document.querySelector('input[type=file]').files = e.dataTransfer.files
imgPreview.src = URL.createObjectURL(file)
}
</script>

</body>
</html>