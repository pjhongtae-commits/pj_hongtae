<?php
function getAIAlerts($pdo){

    $alerts = [];

    $products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

    foreach($products as $p){

        $totalCost = $p['cost'] + ($p['shipping_cost'] ?? 0) + ($p['packaging_cost'] ?? 0);
        $profit = $p['price'] - $totalCost;

        // ❌ ขาดทุน
        if($profit < 0){
            $alerts[] = [
                'type' => 'danger',
                'text' => "❌ {$p['name']} ขาดทุน ควรขึ้นราคา"
            ];
        }

        // ⚠️ กำไรต่ำ
        elseif($profit < 10){
            $alerts[] = [
                'type' => 'warning',
                'text' => "⚠️ {$p['name']} กำไรต่ำ"
            ];
        }

        // 🔥 ขายดี
        if(($p['sales_count'] ?? 0) > 50){
            $alerts[] = [
                'type' => 'success',
                'text' => "🔥 {$p['name']} ขายดี เพิ่มกำไรได้"
            ];
        }

        // 💀 ไม่ขายเลย
        if(($p['sales_count'] ?? 0) == 0 && $p['stock'] > 10){
            $alerts[] = [
                'type' => 'dark',
                'text' => "💀 {$p['name']} ไม่ขายเลย ลดราคาหรือทำโปร"
            ];
        }

        // 📦 ใกล้หมด
        if($p['stock'] <= 5){
            $alerts[] = [
                'type' => 'info',
                'text' => "📦 {$p['name']} ใกล้หมด สั่งเพิ่ม"
            ];
        }

    }

    return array_slice($alerts,0,10); // เอาแค่ 10 อัน
}