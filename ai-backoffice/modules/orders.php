<?php
function addOrder($conn,$product_id,$qty){
    $p=$conn->query("SELECT price,stock FROM products WHERE id=$product_id")->fetch_assoc();

    if($p['stock'] < $qty){
        return false;
    }

    $total=$p['price']*$qty;
    $conn->query("INSERT INTO orders(product_id,qty,total) VALUES($product_id,$qty,$total)");
    $conn->query("UPDATE products SET stock=stock-$qty WHERE id=$product_id");
    return true;
}

function getOrders($conn){
    return $conn->query("SELECT o.*,p.name FROM orders o LEFT JOIN products p ON o.product_id=p.id ORDER BY o.id DESC");
}