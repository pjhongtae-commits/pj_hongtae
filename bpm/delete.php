<?php
include 'db.php';

$id = $_GET['id'] ?? 0;

$conn->query("DELETE FROM discount_logs WHERE id=$id");

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>