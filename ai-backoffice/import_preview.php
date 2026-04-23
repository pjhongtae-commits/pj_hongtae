<?php
require "config/db.php";

$data = $pdo->query("
SELECT 
p.sku,
p.name,
pl.old_price,
pl.new_price,
pl.diff,
pl.created_at
FROM price_logs pl
LEFT JOIN products p ON p.id = pl.product_id
WHERE pl.source LIKE 'makro%'
ORDER BY pl.id DESC
LIMIT 100
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="assets/style.css"> <!-- menu -->
<meta charset="utf-8">
<title>Import Preview</title>

<style>
body{font-family:Arial;background:#f4f6fb;padding:20px;}
table{width:100%;border-collapse:collapse;background:#fff;}
th,td{padding:10px;border:1px solid #ddd;text-align:center;}
th{background:#111827;color:#fff;}
.up{background:#ffe5e5;}
.down{background:#e6ffed;}
</style>
</head>

<body>

<h2>📦 Import Makro (ล่าสุด)</h2>

<table>
<tr>
<th>SKU</th>
<th>สินค้า</th>
<th>ต้นทุนเดิม</th>
<th>ต้นทุนใหม่</th>
<th>เปลี่ยน</th>
<th>สถานะ</th>
<th>เวลา</th>
</tr>

<?php foreach($data as $d):

$class = '';
$status = '';

if($d['diff'] > 0){
    $class = 'up';
    $status = '🔺 ต้นทุนขึ้น';
}elseif($d['diff'] < 0){
    $class = 'down';
    $status = '🔻 ต้นทุนลง';
}else{
    $status = '-';
}
?>

<tr class="<?= $class ?>">
<td><?= $d['sku'] ?></td>
<td><?= $d['name'] ?></td>
<td><?= $d['old_price'] ?></td>
<td><?= $d['new_price'] ?></td>
<td><?= $d['diff'] ?></td>
<td><?= $status ?></td>
<td><?= $d['created_at'] ?></td>
</tr>

<?php endforeach; ?>

</table>
<?php include "components/bottom_menu.php"; ?>
</body>
</html>