<?php
require "../config/db.php";

$today = date("Y-m-d");

$sales = $pdo->query("SELECT SUM(total) t FROM orders WHERE DATE(created_at)='$today'")->fetch()['t'] ?? 0;
$orders = $pdo->query("SELECT COUNT(*) c FROM orders WHERE DATE(created_at)='$today'")->fetch()['c'] ?? 0;

$profit = $pdo->query("
SELECT SUM(
(oi.price - (p.cost + IFNULL(p.shipping_cost,0) + IFNULL(p.packaging_cost,0))) * oi.qty
) profit
FROM order_items oi
LEFT JOIN products p ON p.id=oi.product_id
LEFT JOIN orders o ON o.id=oi.order_id
WHERE DATE(o.created_at)='$today'
")->fetch()['profit'] ?? 0;

echo json_encode([
"sales"=>$sales,
"orders"=>$orders,
"profit"=>$profit
]);