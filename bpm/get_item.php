<?php
include 'db.php';

$code = $_GET['code'];

$sql = "SELECT item_code, item_name, pack_name
        FROM product_master
        WHERE item_code = '$code'
           OR plu = '$code'
           OR barcode = '$code'
        LIMIT 1";

$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode([
        "item_name" => "",
        "pack_name" => ""
    ]);
}
?>