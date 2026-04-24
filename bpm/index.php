<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ระบบสินค้าลดราคา</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
body { background: #f4f6f9; }
.card { border-radius: 15px; }
.table thead { background: #343a40; color: #fff; }
</style>

</head>
<body>
    <!-- Modal Update Barcode -->
<div class="modal fade" id="barcodeModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">🔧 Update Barcode</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <div class="mb-2">
            <label>Item Code</label>
            <input type="text" id="barcode_item_code" class="form-control">
        </div>

        <div class="mb-2">
            <label>Barcode</label>
            <input type="text" id="barcode" class="form-control">
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
        <button class="btn btn-success" id="save_barcode">💾 บันทึก</button>
      </div>

    </div>
  </div>
</div>

<div class="container mt-3">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
        <h4>📊 ระบบสินค้าลดราคา</h4>

        <div>
            <span class="text-muted me-2"><?php echo date("d/m/Y"); ?></span>

            <a href="export.php" class="btn btn-primary btn-sm">📤 Export</a>
            <a href="export_excel.php?date=<?php echo date('Y-m-d'); ?>" class="btn btn-success btn-sm">⚡ Export วันนี้</a>
        </div>
    </div>

    <!-- FORM -->
    <div class="card shadow p-3 mb-3">
        <h5>📦 เพิ่มรายการ</h5>

        <form id="form" class="row g-2">

            <div class="col-md-3">
                <label>รหัสสินค้า</label>
                <input type="text" id="item_code" class="form-control" required>
            </div>
            
            <div class="col-md-3">
                <label>ชื่อสินค้า</label>
                <input type="text" id="item_name" class="form-control" readonly>
            </div>

            <div class="col-md-3">
                <label>สินค้าลด</label>
                <input type="text" id="pack_name" class="form-control" readonly>
            </div>

            <div class="col-md-2">
                <label>จำนวน</label>
                <input type="number" id="qty" class="form-control" required>
            </div>

            <div class="col-md-2">
                <label>% ลด</label>
                <select id="discount_percent" class="form-control" required>
                    <option value="">-- เลือก --</option>
                    <option value="18">18%</option>
                    <option value="20">20%</option>
                    <option value="25">25%</option>
                    <option value="29">29%</option>
                    <option value="30">30%</option>
                    <option value="35">35%</option>
                    <option value="40">40%</option>
                    <option value="45">45%</option>
                    <option value="50">50%</option>
                    <option value="55">55%</option>
                    <option value="57">57%</option>
                    <option value="58">58%</option>
                    <option value="60">60%</option>
                </select>
            </div>

            <div class="col-md-1 d-grid">
                <label>&nbsp;</label>
                <button class="btn btn-success">✔</button>
            </div>

        </form>
        <div class="col-md-1 d-grid">
    <label>&nbsp;</label>
    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#barcodeModal">
        🏷️ update barcode
    </button>
</div>
    </div>

    <!-- TOTAL -->
    <?php
    $today = date("Y-m-d");
    $total = $conn->query("SELECT SUM(qty) as t FROM discount_logs WHERE log_date='$today'")->fetch_assoc();
    ?>
    <h6>📦 รวมทั้งหมด: <span class="text-primary"><?php echo $total['t'] ?? 0; ?></span></h6>

    <!-- TABLE -->
    <div class="card shadow p-3">
        <h5>📋 รายการวันนี้</h5>

        <div class="table-responsive">
            <table class="table table-bordered text-center text-nowrap">
                <thead>
                    <tr>
                        <th>Dept</th>
                        <th>Item</th>
                        <th>ชื่อสินค้า</th>
                        <th>สินค้าลด</th>
                        <th>จำนวน</th>
                        <th>% ลด</th>
                    </tr>
                </thead>
                <tbody>

                <?php

                $sql = "SELECT 
                            d.item_code,
                            COALESCE(p.item_name, d.item_name) AS product_name,
                            MAX(d.discount_name) AS discount_name,
                            SUM(d.qty) AS total_qty,
                            last.discount_percent,
                            p.dept_no,
                            MAX(d.id) AS last_id
                        FROM discount_logs d

                        LEFT JOIN product_master p 
                            ON d.item_code = p.item_code

                        LEFT JOIN (
                            SELECT item_code, discount_percent
                            FROM discount_logs
                            WHERE log_date = '$today'
                            AND id IN (
                                SELECT MAX(id)
                                FROM discount_logs
                                WHERE log_date = '$today'
                                GROUP BY item_code
                            )
                        ) last ON d.item_code = last.item_code

                        WHERE d.log_date = '$today'

                        GROUP BY d.item_code

                        ORDER BY last_id DESC";

                $result = $conn->query($sql);

                if (!$result) {
                    die("SQL Error: " . $conn->error);
                }

                while($row = $result->fetch_assoc()){
                    echo "<tr>
                        <td>{$row['dept_no']}</td>
                        <td>{$row['item_code']}</td>
                        <td>{$row['product_name']}</td>
                        <td>{$row['discount_name']}</td>
                        <td><b>{$row['total_qty']}</b></td>
                        <td>{$row['discount_percent']}%</td>
                    </tr>";
                }

                ?>

                </tbody>
            </table>
        </div>
    </div>

</div>

<script>

// ดึงสินค้า
$("#item_code").on("keyup change", function(){
    let code = $(this).val();

    if(code){
        $.get("get_item.php",{code:code},function(res){
            $("#item_name").val(res.item_name);
            $("#pack_name").val(res.pack_name);
        },"json");
    }
});

// บันทึก
$("#form").submit(function(e){
    e.preventDefault();

    $.post("save_discount.php",{
        item_code: $("#item_code").val(),
        discount_name: $("#pack_name").val(),
        qty: $("#qty").val(),
        discount_percent: $("#discount_percent").val()
    },function(){
        location.reload();
    });
});

// เปิด modal แล้วเอา item_code ไปใส่
$('#barcodeModal').on('show.bs.modal', function () {
    $("#barcode_item_code").val($("#item_code").val());
});

// save barcode
$("#save_barcode").click(function(){

    $.post("update_barcode.php",{
        item_code: $("#barcode_item_code").val(),
        barcode: $("#barcode").val()
    },function(res){
        alert("บันทึกเรียบร้อย");
        $("#barcodeModal").modal('hide');
    });

});

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>