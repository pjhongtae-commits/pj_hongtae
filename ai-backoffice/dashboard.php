<?php
require "config/db.php";

$products = $pdo->query("
SELECT * FROM products ORDER BY updated_at DESC
")->fetchAll();
?>

<h2>🤖 AI BACKOFFICE DASHBOARD</h2>

<table border="1" cellpadding="8">
<tr>
    <th>สินค้า</th>
    <th>ทุนล่าสุด</th>
    <th>ราคาขาย</th>
    <th>กำไร</th>
    <th>AI</th>
    <th>สถานะ</th>
</tr>

<?php foreach($products as $p): ?>

<?php
$real_cost = $p['last_price'] + $p['shipping_cost'] + $p['packaging_cost'];
$profit = $p['price'] - $real_cost;

$status = "OK";
$color = "white";

if($profit < 10){
    $status = "⚠️ LOW PROFIT";
    $color = "#ffdddd";
}
if($p['last_price'] > $p['cost']){
    $status = "🔴 COST UP";
}
if($p['last_price'] < $p['cost']){
    $status = "🟢 COST DOWN";
}
?>

<tr style="background:<?=$color?>">
    <td><?=$p['name']?></td>
    <td><?=$p['last_price']?></td>
    <td><?=$p['price']?></td>
    <td><?=$profit?></td>
    <td><?=$p['use_ai'] ? "🤖 ON" : "OFF"?></td>
    <td><?=$status?></td>
</tr>

<?php endforeach; ?>
</table>