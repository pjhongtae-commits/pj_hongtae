<?php
include 'db.php';

$item_code = $_POST['item_code'];
$barcode = $_POST['barcode'];

// update หรือ insert
$check = $conn->query("SELECT * FROM product_master WHERE item_code='$item_code'");

if($check->num_rows > 0){
    $conn->query("UPDATE product_master 
                  SET barcode='$barcode' 
                  WHERE item_code='$item_code'");
}else{
    $conn->query("INSERT INTO product_master (item_code, barcode) 
                  VALUES ('$item_code','$barcode')");
}

echo "success";
?>