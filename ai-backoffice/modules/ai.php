<?php
// ===============================
// 🤖 AI PROMOTION + REORDER (เดิมของคุณ)
// ===============================
function aiPromotionAndReorder($products, $pdo) {
    $results = [];

    foreach($products as $p) {

        $id = $p['id'];
        $promo = [];
        $reorder = 0;

        // ยอดขาย 7 วันล่าสุด
        $stmt = $pdo->prepare("
            SELECT SUM(qty) as sold
            FROM order_items oi
            LEFT JOIN orders o ON o.id=oi.order_id
            WHERE oi.product_id=? 
            AND o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $stmt->execute([$id]);
        $sold_last_week = $stmt->fetch()['sold'] ?? 0;

        // สต็อกใกล้หมด
        if($p['stock'] <= 5) $promo[] = '⚠️ สต็อกใกล้หมด ลด10%';

        // ขายดี
        if($sold_last_week >= 20) $promo[] = '🔥 ขายดี ลด5% แพ็กคู่';

        // กำไรสูง
        $profit = ($p['price'] - $p['cost']) * ($sold_last_week ?: 0);
        if($profit >= 1000) $promo[] = '💡 กำไรสูง แนะนำสั่งเพิ่ม';

        if($p['stock'] <= 10) {
            $reorder = max(0, 30 - $p['stock']);
        }

        $results[$id] = [
            'recommendation' => implode(' | ', $promo),
            'reorder_qty' => $reorder
        ];
    }

    return $results;
}


// ===============================
// 📊 PROFIT PER PRODUCT (เดิม)
// ===============================
function calcProfitPerProduct($products, $pdo) {
    $profits = [];

    foreach ($products as $p) {
        $stmt = $pdo->prepare("
            SELECT SUM((oi.price - p.cost)*oi.qty) as profit
            FROM order_items oi
            LEFT JOIN products p ON p.id=oi.product_id
            WHERE oi.product_id=?
        ");
        $stmt->execute([$p['id']]);
        $profits[$p['id']] = $stmt->fetch()['profit'] ?? 0;
    }

    return $profits;
}


// ===============================
// 🤖 NEW: AI PRICE ENGINE (เพิ่มใหม่)
// ===============================
function aiCalculatePrice($cost, $stock = 0, $sold7d = 0) {

    // base margin
    $margin = 0.35; // 35%

    // 🔥 ขายดี → เพิ่มราคาได้
    if ($sold7d >= 20) {
        $margin += 0.10;
    }

    // ⚠️ สต็อกเยอะ → ลดราคา
    if ($stock >= 50) {
        $margin -= 0.05;
    }

    // ⚠️ สต็อกต่ำ → ดันราคา
    if ($stock <= 5) {
        $margin += 0.15;
    }

    // กันติดลบ
    if ($margin < 0.1) $margin = 0.1;

    $price = $cost + ($cost * $margin);

    // ปัดราคาขายง่าย
    return ceil($price);
}
?>