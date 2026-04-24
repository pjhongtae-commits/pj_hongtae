<?php
include 'db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=discount_report.xls");

$dept_no = $_GET['dept_no'] ?? '';
$date = $_GET['date'] ?? date('Y-m-d');

$sql = "SELECT 
            d.item_code,
            COALESCE(p.item_name, d.item_name) AS product_name,
            MAX(d.discount_name) AS discount_name,
            SUM(d.qty) AS total_qty,
            last.discount_percent,
            p.dept_no,
            MAX(d.id) AS last_id
        FROM discount_logs d

        LEFT JOIN product_master p 
            ON d.item_code = p.item_code

        -- ดึง % ล่าสุด
        LEFT JOIN (
            SELECT item_code, discount_percent
            FROM discount_logs
            WHERE log_date = '$date'
            AND id IN (
                SELECT MAX(id)
                FROM discount_logs
                WHERE log_date = '$date'
                GROUP BY item_code
            )
        ) last ON d.item_code = last.item_code

        WHERE d.log_date = '$date'";

// filter แผนก
if (!empty($dept_no)) {
    $sql .= " AND p.dept_no = '$dept_no'";
}

$sql .= " GROUP BY d.item_code
          ORDER BY last_id DESC";

$result = $conn->query($sql);

if (!$result) {
    die("SQL Error: " . $conn->error);
}

echo "<table border='1'>
<tr>
    <th>Dept</th>
    <th>Item Code</th>
    <th>ชื่อสินค้า</th>
    <th>สินค้าลด</th>
    <th>จำนวน</th>
    <th>% ลด</th>
</tr>";

while($row = $result->fetch_assoc()){
    echo "<tr>
        <td>{$row['dept_no']}</td>
        <td>{$row['item_code']}</td>
        <td>{$row['product_name']}</td>
        <td>{$row['discount_name']}</td>
        <td><b>{$row['total_qty']}</b></td>
        <td>{$row['discount_percent']}%</td>
    </tr>";
}

echo "</table>";
?>