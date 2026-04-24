<?php 
include 'db.php';

// รับค่า
$input_code = $_POST['item_code'] ?? '';
$item_name_input = $_POST['item_name'] ?? '';
$discount_name = $_POST['discount_name'] ?? '';
$qty = $_POST['qty'] ?? 0;
$discount_percent = $_POST['discount_percent'] ?? 0;
$log_date = date("Y-m-d");

// 🔎 หาใน product_master (รองรับ item_code / plu / barcode)
$sql_find = "SELECT item_code, item_name 
             FROM product_master
             WHERE item_code = '$input_code'
                OR plu = '$input_code'
                OR barcode = '$input_code'
             LIMIT 1";

$result = $conn->query($sql_find);

if ($result && $row = $result->fetch_assoc()) {
    // ✔️ เจอสินค้า
    $item_code = $row['item_code']; // ใช้ตัวจริง
    $item_name = $row['item_name'];
} else {
    // ❗ ไม่เจอ → ใช้ค่าที่กรอกมา
    $item_code = $input_code;
    $item_name = $item_name_input;
}

// กันค่าว่าง
if(empty($item_code) || empty($qty)){
    die("Error: missing data");
}

// 💾 บันทึก
$sql = "INSERT INTO discount_logs 
(item_code,item_name,discount_name,qty,discount_percent,log_date)
VALUES 
('$item_code','$item_name','$discount_name','$qty','$discount_percent','$log_date')";

if(!$conn->query($sql)){
    die("SQL Error: " . $conn->error);
}

echo "success";
?>