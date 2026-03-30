<?php
require "config/db.php";

$id = $_GET['id'];

$order = $pdo->prepare("SELECT * FROM orders WHERE id=?");
$order->execute([$id]);
$order = $order->fetch(PDO::FETCH_ASSOC);

$items = $pdo->prepare("
SELECT oi.*,p.name 
FROM order_items oi
LEFT JOIN products p ON p.id=oi.product_id
WHERE order_id=?
");
$items->execute([$id]);
$items = $items->fetchAll(PDO::FETCH_ASSOC);

$discount = $order['discount'] ?? 0;
$cash = $order['cash'] ?? 0;
$total = $order['total'];
$change = $cash - $total;

/* ตั้งค่าร้าน */
$shop_name = "Hongtae.Store";
$shop_addr = "singburi";
$shop_tel = "Tel: 093-926-9524";

/* PromptPay */
$promptpay = "120-8-68802-9"; // เบอร์ หรือ เลขบัตร
$qr = "https://promptpay.io/$promptpay/$total";

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Receipt</title>

<style>
body{
font-family:monospace;
width:300px;
margin:auto;
font-size:13px;
}

.center{text-align:center}

.logo{
width:130px;
margin-bottom:5px;
}

.line{
border-top:1px dashed #000;
margin:6px 0;
}

.row{
display:flex;
justify-content:space-between;
}

.total{
font-size:18px;
font-weight:bold;
}

.qr{
width:150px;
margin-top:5px;
}
</style>
</head>

<body onload="window.print()">

<div class="center">

<img src="logo.png" class="logo"><br>

<b><?= $shop_name ?></b><br>
<?= $shop_addr ?><br>
<?= $shop_tel ?>

</div>

<div class="line"></div>

<div class="row">
<div>Receipt</div>
<div>#<?= $id ?></div>
</div>

<div class="row">
<div>Date</div>
<div><?= date("d/m/Y H:i") ?></div>
</div>

<div class="line"></div>

<?php foreach($items as $i): ?>

<div><?= $i['name'] ?></div>
<div class="row">
<div><?= $i['qty'] ?> x <?= number_format($i['price'],2) ?></div>
<div><?= number_format($i['qty']*$i['price'],2) ?></div>
</div>

<?php endforeach; ?>

<div class="line"></div>

<div class="row">
<div>Total</div>
<div><?= number_format($total,2) ?></div>
</div>

<div class="row">
<div>Cash</div>
<div><?= number_format($cash,2) ?></div>
</div>

<div class="row">
<div>Change</div>
<div><?= number_format($change,2) ?></div>
</div>

<div class="line"></div>

<div class="center">
PromptPay Scan
<br>
<img src="<?= $qr ?>" class="qr">
<br>
<?= number_format($total,2) ?> บาท
</div>

<div class="line"></div>

<div class="center">
ขอบคุณที่ใช้บริการ
</div>

</body>
</html>