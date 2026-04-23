<?php
require "config/db.php";

// ดึงหมวดหมู่ทั้งหมด
$categories = $pdo->query("SELECT c.id, c.name, COUNT(p.id) as product_count
                            FROM categories c
                            LEFT JOIN products p ON p.category_id = c.id
                            GROUP BY c.id
                            ORDER BY c.name")->fetchAll(PDO::FETCH_ASSOC);

// เพิ่มหมวดหมู่ใหม่
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])){
    $name = trim($_POST['name']);
    if($name){
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}

// ลบหมวดหมู่
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>หมวดหมู่สินค้า</title>
<style>
body{font-family:Arial;background:#f4f4f4;margin:0;padding:20px;}
.header{font-size:22px;margin-bottom:20px;}
form input{padding:8px;width:200px;border-radius:5px;border:1px solid #ccc;}
form button{padding:8px 15px;background:#3742fa;color:white;border:none;border-radius:5px;cursor:pointer;}
form button:hover{background:#2f3542;}
table{width:100%;border-collapse:collapse;background:white;border-radius:8px;overflow:hidden;}
th, td{padding:10px;border-bottom:1px solid #eee;text-align:left;}
th{background:#3742fa;color:white;}
.delete{color:red;cursor:pointer;}
.edit{color:orange;cursor:pointer;}
@media(max-width:768px){
    table, th, td{font-size:14px;}
}
</style>
</head>
<body>

<div class="header">📂 จัดการหมวดหมู่สินค้า</div>

<!-- เพิ่มหมวดหมู่ -->
<form method="POST" style="margin-bottom:20px;">
    <input type="text" name="name" placeholder="ชื่อหมวดหมู่ใหม่" required>
    <button type="submit" name="add_category">➕ เพิ่มหมวดหมู่</button>
</form>

<!-- ตารางหมวดหมู่ -->
<table>
<tr>
<th>ชื่อหมวดหมู่</th>
<th>จำนวนสินค้า</th>
<th>จัดการ</th>
</tr>

<?php foreach($categories as $c): ?>
<tr>
<td><?= htmlspecialchars($c['name']) ?></td>
<td><?= $c['product_count'] ?></td>
<td>
    <a href="?delete=<?= $c['id'] ?>" onclick="return confirm('ลบหมวดหมู่นี้?')">ลบ</a>
</td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>