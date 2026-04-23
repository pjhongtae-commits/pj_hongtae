<?php
require "config/db.php";

function getExternalCost($sku){
    // TODO: ต่อ API จริง (1688 / Makro / Google Sheet)
    return rand(40, 120);
}

$products = $pdo->query("SELECT * FROM products")->fetchAll();

foreach($products as $p){

    $newCost = getExternalCost($p['sku']);
    $oldCost = $p['last_price'];

    // ถ้ายังไม่มีค่าเริ่มต้น
    if($oldCost == null){
        $oldCost = $p['cost'];
    }

    if($newCost != $oldCost){

        // update cost
        $stmt = $pdo->prepare("
            UPDATE products 
            SET last_price=?, updated_at=NOW()
            WHERE id=?
        ");
        $stmt->execute([$newCost, $p['id']]);

        // log
        $pdo->prepare("
            INSERT INTO price_logs 
            (product_id, old_price, new_price, diff, source)
            VALUES (?,?,?,?,?)
        ")->execute([
            $p['id'],
            $oldCost,
            $newCost,
            $newCost - $oldCost,
            'AUTO_SYNC'
        ]);

        // 🤖 AI PRICE ADJUST
        if($p['use_ai'] == 1){

            $real_cost = $newCost 
                + $p['shipping_cost'] 
                + $p['packaging_cost'];

            $fee = $p['price'] * ($p['shopee_fee_percent'] / 100);

            $target_profit = $p['profit_percent'] / 100;

            $newSellPrice = ($real_cost + $fee) * (1 + $target_profit);

            $updatePrice = $pdo->prepare("
                UPDATE products SET price=?
                WHERE id=?
            ");
            $updatePrice->execute([$newSellPrice, $p['id']]);
        }

        // 🚨 ALERT SYSTEM
        if(!$p['alert_sent'] && abs($newCost - $oldCost) > 10){

            // ส่งแจ้งเตือน (LINE / dashboard)
            $pdo->prepare("
                UPDATE products 
                SET alert_sent=1,
                    last_alert_time=NOW()
                WHERE id=?
            ")->execute([$p['id']]);
        }
    }
}

echo "PRICE SYNC DONE";