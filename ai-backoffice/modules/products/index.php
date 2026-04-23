<?php
require __DIR__ . "/../../config/db.php";
require __DIR__ . "/../products.php";

$msg = '';

if (isset($_POST['add'])) {

    $name  = trim($_POST['name']);
    $model = trim($_POST['model']);
    $cost  = (float)$_POST['cost'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];

    if ($name == '' || $price <= 0) {
        $msg = '<div class="alert alert-danger">❌ กรุณากรอกข้อมูลให้ครบ</div>';
    } else {
        // ✅ ใช้ PDO
        addProduct($pdo, $name, $model, $price, $stock, $cost);
        header("Location: index.php?success=1");

        exit;
    }
}

if (isset($_GET['success'])) {
    $msg = '<div class="alert alert-success">✅ เพิ่มสินค้าเรียบร้อย</div>';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>เพิ่มสินค้า</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

<section class="content pt-3">
<div class="container-fluid">

<div class="row justify-content-center">
<div class="col-md-6">

<div class="card card-primary">
 <div class="card-header">
  <h3 class="card-title">➕ เพิ่มสินค้าใหม่</h3>
 </div>

 <form method="post">
 <div class="card-body">

  <?= $msg ?>

  <div class="form-group">
   <label>ชื่อสินค้า</label>
   <input name="name" class="form-control" required>
  </div>

  <div class="form-group">
   <label>รุ่น / รายละเอียด</label>
   <input name="model" class="form-control">
  </div>

  <div class="form-group">
   <label>ต้นทุน</label>
   <input name="cost" type="number" step="0.01" class="form-control">
  </div>

  <div class="form-group">
   <label>ราคาขาย</label>
   <input name="price" type="number" step="0.01" class="form-control" required>
  </div>

  <div class="form-group">
   <label>สต๊อกเริ่มต้น</label>
   <input name="stock" type="number" value="0" class="form-control">
  </div>

 </div>

 <div class="card-footer">
  <button name="add" class="btn btn-primary">💾 บันทึกสินค้า</button>
  <a href="\dashboard\dashboard.php" class="btn btn-secondary float-right">
   ⬅️ กลับ Dashboard
  </a>
 </div>

 </form>
</div>

</div>
</div>

</div>
</section>

</div>
</body>
</html>
