<?php
$pdo = new PDO(
"mysql:host=gondola.proxy.rlwy.net;dbname=railway;port=41366",
"root",
"JIQXqeOFGMIsnOLYAgQeLFoElAFGAcgt"
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);