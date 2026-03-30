<?php
require "config/db.php";

$data=json_decode(file_get_contents("php://input"),true);

$total=0;

foreach($data as $id=>$i){
$total += $i['price']*$i['qty'];
}

$pdo->prepare("
INSERT INTO orders(total)
VALUES(?)
")->execute([$total]);

$order_id=$pdo->lastInsertId();

foreach($data as $id=>$i){

$pdo->prepare("
INSERT INTO order_items
(order_id,product_id,qty,price)
VALUES(?,?,?,?)
")->execute([
$order_id,
$id,
$i['qty'],
$i['price']
]);

$pdo->prepare("
UPDATE products
SET stock = stock - ?
WHERE id=?
")->execute([
$i['qty'],
$id
]);

}

echo $order_id;