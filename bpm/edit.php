<?php
include 'db.php';

$id = $_GET['id'];
$data = $conn->query("SELECT * FROM discount_logs WHERE id=$id")->fetch_assoc();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $item_name = $_POST['item_name'];
    $qty = $_POST['qty'];
    $discount_name = $_POST['discount_name'];
    $discount_percent = $_POST['discount_percent'];

    $conn->query("UPDATE discount_logs 
                  SET item_name='$item_name',
                      qty='$qty',
                      discount_name='$discount_name',
                      discount_percent='$discount_percent'
                  WHERE id=$id");

    header("Location: manage_view.php?date=".$data['log_date']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>แก้ไขข้อมูล</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f4f6f9;
}
.card {
    border-radius: 15px;
}
</style>

</head>
<body>

<div class="container mt-5">

    <div class="card shadow p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>✏️ แก้ไขรายการ</h4>
            <a href="manage_view.php?date=<?php echo $data['log_date']; ?>" class="btn btn-secondary">
                ⬅ กลับ
            </a>
        </div>

        <form method="POST">

            <!-- Item Code -->
            <div class="mb-3">
                <label class="form-label">📦 Item Code</label>
                <input type="text" class="form-control" 
                       value="<?php echo $data['item_code']; ?>" readonly>
            </div>

            <!-- Item Name -->
            <div class="mb-3">
                <label class="form-label">📄 ชื่อสินค้า</label>
                <input type="text" name="item_name" class="form-control"
                       value="<?php echo $data['item_name']; ?>" required>
            </div>

            <!-- Discount Name -->
            <div class="mb-3">
                <label class="form-label">🏷️ ประเภทลด</label>
                <input type="text" name="discount_name" class="form-control"
                       value="<?php echo $data['discount_name']; ?>">
            </div>

            <!-- Qty -->
            <div class="mb-3">
                <label class="form-label">🔢 จำนวน</label>
                <input type="number" name="qty" class="form-control"
                       value="<?php echo $data['qty']; ?>" required>
            </div>

            <!-- Discount Percent -->
            <div class="mb-3">
                <label class="form-label">💸 % ลด</label>
                <input type="number" name="discount_percent" class="form-control"
                       value="<?php echo $data['discount_percent']; ?>">
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success w-50">
                    💾 บันทึก
                </button>

                <a href="manage_view.php?date=<?php echo $data['log_date']; ?>" 
                   class="btn btn-danger w-50">
                    ❌ ยกเลิก
                </a>
            </div>

        </form>

    </div>

</div>

</body>
</html>