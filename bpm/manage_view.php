<?php include 'db.php'; 
$date = $_GET['date'] ?? date('Y-m-d');

// 🔢 summary
$sum = $conn->query("SELECT COUNT(*) as total_item, SUM(qty) as total_qty 
                     FROM discount_logs WHERE log_date='$date'")
            ->fetch_assoc();

// 📦 data
$result = $conn->query("SELECT * FROM discount_logs WHERE log_date='$date' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ดูข้อมูล</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }
.card { border-radius:15px; }
</style>

</head>
<body>

<div class="container mt-4">

<!-- 🔥 Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>📅 รายการวันที่ <?php echo $date; ?></h4>
    <a href="manage.php" class="btn btn-secondary">⬅ กลับ</a>
</div>

<!-- 📊 Summary -->
<div class="row mb-3">

    <div class="col-md-6 mb-2">
        <div class="card shadow p-3 text-center">
            <h6>จำนวนรายการ</h6>
            <h3 class="text-primary"><?php echo $sum['total_item']; ?></h3>
        </div>
    </div>

    <div class="col-md-6 mb-2">
        <div class="card shadow p-3 text-center">
            <h6>รวม Qty</h6>
            <h3 class="text-success"><?php echo $sum['total_qty'] ?? 0; ?></h3>
        </div>
    </div>

</div>

<!-- 💻 TABLE (คอม) -->
<div class="card shadow p-3 d-none d-md-block">

<table class="table table-bordered table-hover text-center align-middle">
    <thead class="table-dark">
        <tr>
            <th>Item</th>
            <th>ชื่อสินค้า</th>
            <th>สินค้าลด</th>
            <th>จำนวน</th>
            <th>% ลด</th>
            <th>จัดการ</th>
            
        </tr>
    </thead>
    <tbody>

    <?php while($row = $result->fetch_assoc()){ 
        $color = ($row['discount_percent'] >= 50) ? "text-danger fw-bold" : "";
    ?>
        <tr>
            <td><?php echo $row['item_code']; ?></td>
            <td><?php echo $row['item_name']; ?></td>
            <td><?php echo $row['discount_name']; ?></td>
            <td><?php echo $row['qty']; ?></td>
            <td class="<?php echo $color; ?>">
                <?php echo $row['discount_percent']; ?>%
            </td>
            <td>
    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
        ✏ แก้ไข
    </a>

    <a href="delete.php?id=<?php echo $row['id']; ?>" 
       class="btn btn-danger btn-sm"
       onclick="return confirm('ยืนยันการลบ?')">
        🗑 ลบ
    </a>
</td>
        </tr>
    <?php } ?>

    </tbody>
</table>

</div>

<!-- 📱 MOBILE (UL LIST) -->
<div class="d-block d-md-none">

<?php
$result = $conn->query("SELECT * FROM discount_logs WHERE log_date='$date' ORDER BY id DESC");

while($row = $result->fetch_assoc()){
    $color = ($row['discount_percent'] >= 50) ? "bg-danger" : "bg-primary";
?>

<div class="card shadow mb-3 p-3">

    <div class="mb-2">
        <b>📦 Item:</b> <?php echo $row['item_code']; ?>
    </div>

    <div class="mb-2">
        <b>📄 ชื่อ:</b> <?php echo $row['item_name']; ?>
    </div>

    <div class="mb-2">
        <b>🏷️ ลด:</b> <?php echo $row['discount_name']; ?>
    </div>

    <div class="d-flex justify-content-between">
        <span>จำนวน</span>
        <span class="badge bg-success"><?php echo $row['qty']; ?></span>
    </div>

    <div class="d-flex justify-content-between mt-2">
        <span>% ลด</span>
        <span class="badge <?php echo $color; ?>">
            <?php echo $row['discount_percent']; ?>%
        </span>
    </div>
    <div class="mt-3 d-flex gap-2">
    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm w-50">
        ✏ แก้ไข
    </a>

    <a href="delete.php?id=<?php echo $row['id']; ?>" 
       class="btn btn-danger btn-sm w-50"
       onclick="return confirm('ยืนยันการลบ?')">
        🗑 ลบ
    </a>
</div>

</div>

<?php } ?>

</div>

</div>

</body>
</html>