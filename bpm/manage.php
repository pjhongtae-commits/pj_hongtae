<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>จัดการข้อมูล</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

<h4 class="mb-3">🛠️ จัดการข้อมูลรายวัน</h4>

<?php
// 🔥 ดึง list วันที่
$sql = "SELECT log_date, COUNT(*) as total_item, SUM(qty) as total_qty
        FROM discount_logs
        GROUP BY log_date
        ORDER BY log_date DESC";

$result = $conn->query($sql);
?>

<div class="card shadow p-3">

<table class="table table-bordered text-center align-middle">
    <thead class="table-dark">
        <tr>
            <th>วันที่</th>
            <th>จำนวนรายการ</th>
            <th>รวม Qty</th>
            <th>จัดการ</th>
        </tr>
    </thead>
    <tbody>

    <?php while($row = $result->fetch_assoc()){ ?>
        <tr>
            <td><?php echo $row['log_date']; ?></td>
            <td><?php echo $row['total_item']; ?></td>
            <td><?php echo $row['total_qty']; ?></td>
            <td>

                <!-- 🔍 ดูข้อมูล -->
                <a href="manage_view.php?date=<?php echo $row['log_date']; ?>" 
                   class="btn btn-primary btn-sm">
                   🔍 ดู
                </a>

                <!-- 🗑️ ลบ -->
                <a href="manage_delete.php?date=<?php echo $row['log_date']; ?>" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('ลบข้อมูลวันที่นี้ทั้งหมด?');">
                   🗑️ ลบ
                </a>

            </td>
        </tr>
    <?php } ?>

    </tbody>
</table>

</div>

</div>

</body>
</html>