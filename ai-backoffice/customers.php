<?php
require "config/db.php";

// Add / Edit / Delete
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(isset($_POST['add'])){
        $stmt = $pdo->prepare("INSERT INTO customers (name, phone, email, note) VALUES (?,?,?,?)");
        $stmt->execute([$_POST['name'], $_POST['phone'], $_POST['email'], $_POST['note']]);
    }
    if(isset($_POST['delete'])){
        $stmt = $pdo->prepare("DELETE FROM customers WHERE id=?");
        $stmt->execute([$_POST['id']]);
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// ดึงข้อมูลลูกค้าทั้งหมด
$customers = $pdo->query("SELECT * FROM customers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Customers</title>
<style>
body{font-family:Arial;background:#f1f2f6;margin:0;padding:20px;}
.header{background:#2f3542;color:white;padding:15px;font-size:20px;margin-bottom:15px;}
table{width:100%;border-collapse:collapse;background:white;}
th,td{border:1px solid #ddd;padding:8px;text-align:center;}
button{padding:6px 12px;border:none;border-radius:4px;cursor:pointer;}
.add-btn{background:#2ed573;color:white;}
.delete-btn{background:#ff4757;color:white;}
input{padding:5px;width:100%;margin-bottom:8px;border:1px solid #ccc;border-radius:4px;}
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);}
.modal-content{background:white;padding:20px;border-radius:8px;width:400px;margin:10% auto;position:relative;}
.close{position:absolute;top:10px;right:15px;font-size:18px;cursor:pointer;}
</style>
</head>

<body>

<div class="header">👤 ลูกค้า</div>

<button class="add-btn" onclick="openModal()">เพิ่มลูกค้าใหม่</button>

<input type="text" id="search" placeholder="ค้นหาลูกค้า..." style="margin:10px 0;padding:5px;width:300px;">

<table id="customerTable">
<tr>
<th>ชื่อ</th><th>โทร</th><th>Email</th><th>หมายเหตุ</th><th>จัดการ</th>
</tr>
<?php foreach($customers as $c): ?>
<tr>
<td><?= htmlspecialchars($c['name']) ?></td>
<td><?= htmlspecialchars($c['phone']) ?></td>
<td><?= htmlspecialchars($c['email']) ?></td>
<td><?= htmlspecialchars($c['note']) ?></td>
<td>
<form method="POST" style="display:inline;">
<input type="hidden" name="id" value="<?= $c['id'] ?>">
<button class="delete-btn" name="delete" onclick="return confirm('ลบลูกค้านี้?')">ลบ</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>

<!-- Modal Add Customer -->
<div class="modal" id="addModal">
<div class="modal-content">
<div class="close" onclick="closeModal()">&times;</div>
<h3>เพิ่มลูกค้าใหม่</h3>
<form method="POST">
<input type="text" name="name" placeholder="ชื่อ" required>
<input type="text" name="phone" placeholder="โทรศัพท์">
<input type="email" name="email" placeholder="Email">
<textarea name="note" placeholder="หมายเหตุ"></textarea>
<button class="add-btn" name="add">บันทึก</button>
</form>
</div>
</div>

<script>
function openModal(){ document.getElementById('addModal').style.display='block'; }
function closeModal(){ document.getElementById('addModal').style.display='none'; }

// search filter
document.getElementById('search').addEventListener('input', function(){
    let filter = this.value.toLowerCase();
    document.querySelectorAll('#customerTable tr').forEach((tr,i)=>{
        if(i===0) return; // skip header
        let name = tr.cells[0].innerText.toLowerCase();
        let phone = tr.cells[1].innerText.toLowerCase();
        tr.style.display = (name.includes(filter) || phone.includes(filter)) ? '' : 'none';
    });
});
</script>

</body>
</html>