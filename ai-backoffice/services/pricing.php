<?php

function calculateSellingPrice($cost, $shipping = 0, $packaging = 0, $shopee = 20, $profit = 30){

    // กัน null
    $cost = (float)($cost ?? 0);
    $shipping = (float)($shipping ?? 0);
    $packaging = (float)($packaging ?? 0);
    $shopee = (float)($shopee ?? 0);
    $profit = (float)($profit ?? 0);

    // รวมต้นทุนจริง
    $total = $cost + $shipping + $packaging;

    // เปลี่ยน % → decimal
    $percent = ($shopee + $profit) / 100;

    // กัน error (เช่น 100%+)
    if($percent >= 1){
        return ceil($total);
    }

    // 🔥 สูตรจริง (Shopee หักจากราคาขาย)
    $price = $total / (1 - $percent);

    // กันราคาต่ำเกิน (กันขาดทุน)
    if($price < $total){
        $price = $total + 5;
    }

    return ceil($price); // ปัดขึ้น
}