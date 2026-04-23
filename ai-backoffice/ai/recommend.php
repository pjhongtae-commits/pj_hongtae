<?php

function getAIRecommend($pdo){

$sql = "
SELECT name, stock 
FROM products
ORDER BY stock ASC
LIMIT 5
";

$stmt = $pdo->query($sql);

$html = "";

foreach($stmt as $row){
$html .= "• {$row['name']} เหลือ {$row['stock']} <br>";
}

return $html;
}