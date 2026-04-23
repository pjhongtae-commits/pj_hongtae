<?php

// ====================== SEND LINE ======================
function sendLine($message){
    $token = "+NehYXX/vy+zfdMBy6+EkXvvOP/qX+qynxiGPgnkB0Nw2t0Es/WMLHaVlg0L+X42oYjx9x3NQMr/eh8+l+RSBmVuipodBoOS0QCU+Z958DUvQeirSZRfAPjziQHkDkBnl0HhTyHlHcOITTwo9XvxzgdB04t89/1O/w1cDnyilFU=";
    $groupId = "C1b623b9aefb2b483ea388b95bde28039";

    $data = [
        "to" => $groupId,
        "messages" => [
            [
                "type" => "text",
                "text" => $message
            ]
        ]
    ];

    $ch = curl_init("https://api.line.me/v2/bot/message/push");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer ".$token
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_exec($ch);
    curl_close($ch);
}


// ====================== CHECK & ALERT (กัน spam) ======================
function checkLowStockAndAlert($product){
    global $pdo;

    $now = time();
    $last = $product['last_alert_time'] ? strtotime($product['last_alert_time']) : 0;

    $cooldown = 3600; // 1 ชั่วโมง

    if(
        $product['stock'] <= $product['min_stock'] &&
        (
            $product['alert_sent'] == 0 ||
            ($now - $last) > $cooldown
        )
    ){
        $msg = "🚨 แจ้งเตือนสินค้าใกล้หมด\n\n";
        $msg .= "📦 {$product['name']}\n";
        $msg .= "เหลือ: {$product['stock']} | ควรมี: {$product['min_stock']}\n";
        $msg .= "เวลา: ".date("Y-m-d H:i:s");

        sendLine($msg);

        // อัปเดตสถานะกัน spam
        $stmt = $pdo->prepare("
            UPDATE products 
            SET alert_sent = 1, last_alert_time = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$product['id']]);
    }
}


// ====================== SELL ======================
function sellProduct($product_id, $qty){
    global $pdo;

    // ดึง stock ก่อน
    $product = $pdo->query("SELECT * FROM products WHERE id = $product_id")->fetch();

    $before = $product['stock'];
    $after = $before - $qty;

    // อัปเดต stock
    $pdo->query("UPDATE products SET stock = $after WHERE id = $product_id");

    // log
    $pdo->query("
        INSERT INTO stock_logs 
        (product_id, type, qty, stock_before, stock_after, ref_type) 
        VALUES ($product_id, 'out', $qty, $before, $after, 'sale')
    ");

    // ดึงใหม่
    $product = $pdo->query("SELECT * FROM products WHERE id = $product_id")->fetch();

    // เช็คแจ้งเตือน
    checkLowStockAndAlert($product);
}


// ====================== RESTOCK ======================
function restockProduct($product_id, $qty){
    global $pdo;

    $product = $pdo->query("SELECT * FROM products WHERE id = $product_id")->fetch();

    $before = $product['stock'];
    $after = $before + $qty;

    $pdo->query("
        UPDATE products 
        SET stock = $after,
            alert_sent = 0,
            last_alert_time = NULL
        WHERE id = $product_id
    ");

    $pdo->query("
        INSERT INTO stock_logs 
        (product_id, type, qty, stock_before, stock_after, ref_type) 
        VALUES ($product_id, 'in', $qty, $before, $after, 'restock')
    ");
}