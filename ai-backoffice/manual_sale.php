<?php
require "config/db.php";

$products = $pdo->query("SELECT * FROM products ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>บันทึกยอดย้อนหลัง</title>

<style>
body{
    margin:0;
    font-family:Arial;
    background:#f1f2f6;
    padding:20px;
}

.container{
    max-width:900px;
    margin:auto;
}

.header{
    font-size:22px;
    font-weight:bold;
    margin-bottom:15px;
}

.box{
    background:#fff;
    padding:15px;
    border-radius:12px;
    margin-bottom:15px;
    box-shadow:0 3px 10px rgba(0,0,0,0.05);
}

.grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:10px;
}

.card{
    border:1px solid #eee;
    padding:10px;
    border-radius:8px;
    cursor:pointer;
    text-align:center;
    transition:.2s;
}

.card:hover{
    background:#f9f9f9;
}

.cart table{
    width:100%;
}

.cart td{
    padding:6px;
    border-bottom:1px solid #eee;
}

.total{
    font-size:20px;
    font-weight:bold;
    margin-top:10px;
}

.btn{
    background:#2ed573;
    color:white;
    padding:12px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    width:100%;
    font-size:16px;
}

.btn:hover{
    background:#20bf6b;
}

input,select{
    width:100%;
    padding:10px;
    border-radius:6px;
    border:1px solid #ccc;
    margin-bottom:10px;
}
</style>
</head>
<link rel="stylesheet" href="assets/style.css"> <!-- menu -->
<body>

<div class="container">

<div class="header">🧾 บันทึกยอดขายย้อนหลัง</div>

<div class="box">
<label>📅 วันที่</label>
<input type="datetime-local" id="date">
</div>

<div class="box">
<h3>📦 เลือกสินค้า</h3>
<div class="grid">

<?php foreach($products as $p): ?>
<div class="card" onclick="addItem(<?= $p['id'] ?>,'<?= addslashes($p['name']) ?>',<?= $p['price'] ?>)">
    <div><?= $p['name'] ?></div>
    <div>฿<?= number_format($p['price'],2) ?></div>
</div>
<?php endforeach; ?>

</div>
</div>

<div class="box cart">
<h3>🛒 รายการ</h3>

<table id="cart"></table>

<div class="total">
รวม: ฿<span id="total">0.00</span>
</div>

<label>
<input type="checkbox" id="cutstock"> ตัดสต๊อก
</label>

<button class="btn" onclick="save()">💾 บันทึกย้อนหลัง</button>

</div>

</div>

<script>

let items = {};

/* เพิ่มสินค้า */
function addItem(id,name,price){

    if(!items[id])
        items[id] = {name,price,qty:1};
    else
        items[id].qty++;

    render();
}

/* แสดงผล */
function render(){

    let html="";
    let total=0;

    for(let id in items){

        let i=items[id];
        let sum=i.qty*i.price;
        total+=sum;

        html+=`
        <tr>
            <td>${i.name}</td>
            <td>
                <button onclick="dec(${id})">-</button>
                ${i.qty}
                <button onclick="inc(${id})">+</button>
            </td>
            <td>฿${sum.toFixed(2)}</td>
        </tr>`;
    }

    document.getElementById("cart").innerHTML = html;
    document.getElementById("total").innerText = total.toFixed(2);
}

/* เพิ่มลด */
function inc(id){ items[id].qty++; render(); }
function dec(id){
    items[id].qty--;
    if(items[id].qty<=0) delete items[id];
    render();
}

/* SAVE */
function save(){

    let date = document.getElementById("date").value;
    let cutstock = document.getElementById("cutstock").checked;

    if(!date){
        alert("เลือกวันที่ก่อน");
        return;
    }

    if(Object.keys(items).length === 0){
        alert("ไม่มีสินค้า");
        return;
    }

    let payload = {
        items: items,
        date: date,
        cutstock: cutstock
    };

    fetch("save_order_manual.php",{
        method:"POST",
        headers:{
            "Content-Type":"application/json"
        },
        body: JSON.stringify(payload)
    })
    .then(r=>r.text())
    .then(msg=>{
        alert(msg);
        location.reload();
    });

}

</script>
<?php include "components/bottom_menu.php"; ?>
</body>
</html>