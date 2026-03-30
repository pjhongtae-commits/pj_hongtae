<?php

function getAIDecision($pdo){

$low = $pdo->query("
SELECT COUNT(*) 
FROM products 
WHERE stock <= min_stock
")->fetchColumn();

if($low > 0){
return "⚠️ มีสินค้าใกล้หมด ควรสั่งซื้อเพิ่ม";
}

$sales = $pdo->query("
SELECT SUM(total) FROM orders 
WHERE date >= CURDATE() - INTERVAL 7 DAY
")->fetchColumn();

if($sales < 1000){
return "📉 ยอดขายต่ำ ควรทำโปรโมชั่น";
}

return "✅ ระบบปกติ";
}