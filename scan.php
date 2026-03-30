<?php
require "config/db.php";

$code=$_GET['code'];

$stmt=$pdo->prepare("
SELECT * FROM products
WHERE barcode=? OR id=?
");

$stmt->execute([$code,$code]);

echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));