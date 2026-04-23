<?php require "config/db.php"; ?>

<form method="POST">
ชื่อสินค้า: <input name="name"><br>
ต้นทุน: <input name="cost"><br>
ค่าส่ง: <input name="shipping"><br>
ค่ากล่อง: <input name="packaging"><br>
% Shopee: <input name="shopee"><br>
% กำไร: <input name="profit"><br>
<button>บันทึก</button>
</form>

<?php
if($_POST){
    $cost = $_POST['cost'];
    $shipping = $_POST['shipping'];
    $packaging = $_POST['packaging'];
    $shopee = $_POST['shopee'];
    $profit = $_POST['profit'];

    // สูตรคำนวณ
    $sell = ($cost + $shipping + $packaging) / (1 - (($shopee + $profit)/100));

    $stmt = $pdo->prepare("INSERT INTO products 
    (name,cost,shipping_cost,packaging_cost,shopee_fee_percent,profit_percent,selling_price)
    VALUES (?,?,?,?,?,?,?)");

    $stmt->execute([
        $_POST['name'],
        $cost,
        $shipping,
        $packaging,
        $shopee,
        $profit,
        $sell
    ]);

    echo "✅ บันทึกแล้ว ราคาแนะนำ: ".round($sell,2);
}
?>