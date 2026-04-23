<?php
// ดึงสินค้าทั้งหมด
function getAllProducts($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM products ORDER BY id DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ดึงสินค้าตาม ID
function getProductById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// เพิ่มสินค้า
function addProduct($pdo, $data) {
    $stmt = $pdo->prepare("INSERT INTO products (name, price, cost, stock, category_id) VALUES (?,?,?,?,?)");
    return $stmt->execute([$data['name'],$data['price'],$data['cost'],$data['stock'],$data['category_id']]);
}

// แก้ไขสินค้า
function updateProduct($pdo, $id, $data) {
    $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, cost=?, stock=?, category_id=? WHERE id=?");
    return $stmt->execute([$data['name'],$data['price'],$data['cost'],$data['stock'],$data['category_id'],$id]);
}

// ลบสินค้า
function deleteProduct($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
    return $stmt->execute([$id]);
}
?>