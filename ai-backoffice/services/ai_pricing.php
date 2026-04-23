<?php

function aiProfitPercent($p){

    $cost = $p['cost'] ?? 0;
    $sales = $p['sales_count'] ?? 0;
    $stock = $p['stock'] ?? 0;

    /* 🔥 ขายดีมาก → เพิ่มกำไร */
    if($sales > 50){
        return 35;
    }

    /* 🚀 ขายดี → เพิ่มกำไร */
    if($sales > 20){
        return 30;
    }

    /* 💀 ไม่ขายเลย + ของเยอะ → ลดราคา */
    if($sales == 0 && $stock > 10){
        return 5;
    }

    /* ⚠️ ของเยอะ → ลดกำไร */
    if($stock > 50){
        return 10;
    }

    /* 🧠 ต้นทุนต่ำ → บวกกำไรได้ */
    if($cost < 50){
        return 30;
    }

    /* 🧠 สินค้าทั่วไป */
    if($cost >= 50 && $cost < 200){
        return 25;
    }

    /* 💰 ของแพง */
    if($cost >= 200){
        return 15;
    }

    return 20; // default
}