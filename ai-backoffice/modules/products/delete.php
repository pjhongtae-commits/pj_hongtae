<?php
require_once "../../config/db.php";
$id = $_GET['id'];

$pdo->prepare("DELETE FROM stock WHERE product_id=?")->execute([$id]);
$pdo->prepare("DELETE FROM products WHERE id=?")->execute([$id]);

header("Location: list.php");
