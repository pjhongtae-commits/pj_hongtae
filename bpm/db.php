<?php
$conn = new mysqli("localhost","root","","bpm");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>