<?php
function lowStock($conn,$limit=5){
    return $conn->query("SELECT * FROM products WHERE stock <= $limit");
}