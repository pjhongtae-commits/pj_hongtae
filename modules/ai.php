<?php
// แนะนำโปรโมชั่น + reorder
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
            WHERE oi.product_id=? AND o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $stmt->execute([$id]);
        $sold_last_week = $stmt->fetch()['sold'] ?? 0;

        // สต็อกใกล้หมด → ลดราคา
        if($p['stock'] <= 5) $promo[] = '⚠️ สต็อกใกล้หมด ลด10%';

        // ขายดี → แพ็กคู่
        if($sold_last_week >= 20) $promo[] = '🔥 ขายดี ลด5% แพ็กคู่';

        // กำไรสูง → reorder
        $profit = ($p['price'] - $p['cost']) * ($sold_last_week ?: 0);
        if($profit >= 1000) $promo[] = '💡 กำไรสูง แนะนำสั่งเพิ่ม';
        if($p['stock'] <= 10) $reorder = max(0, 30 - $p['stock']); // แนะนำสต็อกเต็ม 30 ชิ้น

        $results[$id] = [
            'recommendation' => implode(' | ', $promo),
            'reorder_qty' => $reorder
        ];
    }
    return $results;
}

// คำนวณกำไรรวมแต่ละสินค้า
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
?>