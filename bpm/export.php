<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Export Excel</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow p-4">
        <h4>📤 Export รายงานสินค้าลดราคา</h4>

        <form action="export_excel.php" method="GET" class="row g-3 mt-3">

            <div class="col-md-4">
                <label>เลือกแผนก (Dept)</label>
                <select name="dept_no" class="form-control">
                    <option value="">-- ทั้งหมด --</option>
                    
                    <?php
                    $dept = $conn->query("SELECT DISTINCT dept_no FROM product_master ORDER BY dept_no");
                    while($d = $dept->fetch_assoc()){
                        echo "<option value='{$d['dept_no']}'>{$d['dept_no']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-4">
                <label>วันที่</label>
                <input type="date" name="date" class="form-control" required>
            </div>

            <div class="col-md-4 d-grid">
                <label>&nbsp;</label>
                <button class="btn btn-success">⬇ Export Excel</button>
            </div>

        </form>
    </div>

</div>

</body>
</html>