<?php
include 'db.php';

$date = $_GET['date'];

$conn->query("DELETE FROM discount_logs WHERE log_date='$date'");

echo "<script>alert('ลบเรียบร้อย');location='manage.php';</script>";
?>