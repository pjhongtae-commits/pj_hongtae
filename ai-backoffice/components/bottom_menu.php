<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<?php
require_once "config/db.php";

$current = basename($_SERVER['PHP_SELF']);

/* นับสินค้าใกล้หมด */
$lowStock = $pdo->query("
SELECT COUNT(*) c 
FROM products 
WHERE stock <= min_stock
")->fetch()['c'] ?? 0;
?>
<div class="bottom-menu">

    <!-- หน้าแรก -->
    <div class="<?= $current=='index.php'?'menu-item active':'menu-item' ?>" onclick="go('index.php')">
        <div>🏠</div>
        <span>หน้าแรก</span>
    </div>

    <!-- ขาย -->
    <div class="<?= $current=='pos.php'?'menu-item active':'menu-item' ?>" onclick="go('pos.php')">
        <div>🖥️</div>
        <span>ขาย</span>
    </div>

    <!-- สินค้า -->
    <div class="<?= $current=='products.php'?'menu-item active':'menu-item' ?>" onclick="go('products.php')">
    
    <div class="icon-wrap">
        📦
        <?php if($lowStock > 0): ?>
            <span class="badge"><?= $lowStock ?></span>
        <?php endif; ?>
    </div>

    <span>สินค้า</span>
</div>

    <!-- สต๊อก -->
    <div class="<?= $current=='stock_logs.php'?'menu-item active':'menu-item' ?>" onclick="go('stock_logs.php')">
        <div>📊</div>
        <span>สต๊อก</span>
    </div>

    <!-- จัดการ -->
    <div class="<?= $current=='admin_products.php'?'menu-item active':'menu-item' ?>" onclick="go('admin_products.php')">
        <div>⚙️</div>
        <span>จัดการ</span>
    </div>

    <!-- รายงาน -->
    <div class="<?= $current=='daily_report.php'?'menu-item active':'menu-item' ?>" onclick="go('daily_report.php')">
        <div>📈</div>
        <span>รายงาน</span>
    </div>

</div>

<script>
function go(url){
    window.location = url;
}
</script>