<?php
require "config/db.php";

$products = $pdo->query("
SELECT * FROM products
ORDER BY name
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>POS</title>

<style>

body{
margin:0;
font-family:Arial;
background:#f1f2f6;
}

/* HEADER */

.header{
background:#111;
color:#fff;
padding:15px;
font-size:20px;
font-weight:bold;
display:flex;
justify-content:space-between;
align-items:center;
}

.back-btn{
background:#00c853;
color:white;
padding:8px 14px;
text-decoration:none;
border-radius:6px;
font-size:14px;
}

.back-btn:hover{
background:#00a844;
}

/* POS LAYOUT */

.pos{
display:grid;
grid-template-columns:60% 40%;
height:calc(100vh - 60px);
gap:10px;
}

/* LEFT PRODUCT AREA */

.products{
padding:10px;
overflow:auto;
}

.grid{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:10px;
}

.card{
background:white;
padding:10px;
border-radius:8px;
cursor:pointer;
text-align:center;
transition:0.2s;
}

.card:hover{
transform:translateY(-3px);
box-shadow:0 3px 10px rgba(0,0,0,0.1);
}

.card img{
width:100%;
height:80px;
object-fit:cover;
border-radius:6px;
}

.name{
font-size:13px;
height:32px;
overflow:hidden;
}

/* RIGHT CART */

.cart{
background:white;
padding:10px;
display:flex;
flex-direction:column;
}

.cart h2{
margin:0 0 10px;
}

/* BARCODE */

.barcode{
padding:15px;
font-size:18px;
width:100%;
margin-bottom:10px;
}

/* TABLE */

table{
width:100%;
border-collapse:collapse;
}

td, th{
padding:6px;
border-bottom:1px solid #eee;
text-align:left;
}

/* QTY BUTTON */

.qty{
display:flex;
align-items:center;
gap:5px;
}

.qty button{
width:25px;
height:25px;
border:none;
background:#57606f;
color:white;
cursor:pointer;
border-radius:4px;
}

.qty button:hover{
background:#2f3542;
}

/* REMOVE */

.remove{
color:#ff4757;
font-weight:bold;
cursor:pointer;
}

/* TOTAL */

.total{
margin-top:10px;
font-size:26px;
font-weight:bold;
background:#f1f2f6;
padding:10px;
border-radius:8px;
}

/* CASH SECTION */

.cash-section{
display:flex;
flex-direction:column;
gap:10px;
margin-top:10px;
}

.cash-section input{
width:100%;
padding:10px;
font-size:16px;
border-radius:5px;
border:1px solid #ccc;
}

.change{
font-size:16px;
}

/* PAY BUTTON */

.pay{
background:#2ed573;
color:white;
padding:18px;
text-align:center;
cursor:pointer;
border-radius:8px;
font-size:18px;
font-weight:bold;
}

.pay:hover{
background:#20bf6b;
}

/* CLEAR BUTTON */

.clear-btn{
background:#ff4757;
color:white;
padding:10px;
text-align:center;
border-radius:6px;
cursor:pointer;
margin-top:5px;
}

/* MOBILE */

@media(max-width:768px){

.pos{
grid-template-columns:1fr;
}

.grid{
grid-template-columns:repeat(2,1fr);
}

}

</style>
</head>

<body>

<div class="header">
🤖 AI BACKOFFICE POS
<a href="index.php" class="back-btn">← กลับหน้าเมนู</a>
</div>

<div class="pos">

<!-- LEFT PRODUCT -->

<div class="products">

<div class="grid">

<?php foreach($products as $p): ?>

<div class="card"
onclick="addItem(<?= $p['id'] ?>,'<?= addslashes($p['name']) ?>',<?= $p['price'] ?>)">

<?php if($p['image']): ?>
<img src="uploads/<?= $p['image'] ?>">
<?php endif; ?>

<div class="name"><?= $p['name'] ?></div>

<div>฿<?= number_format($p['price'],2) ?></div>

</div>

<?php endforeach; ?>

</div>

</div>

<!-- RIGHT CART -->

<div class="cart">

<h2>🧾 รายการขาย</h2>

<input class="barcode" placeholder="ยิง barcode" onkeypress="scan(event)" autofocus>

<table id="cart">
<tr>
<th>สินค้า</th>
<th>จำนวน</th>
<th>รวม</th>
<th></th>
</tr>
</table>

<div class="total">
รวม: ฿<span id="total">0.00</span>
</div>

<div class="clear-btn" onclick="clearCart()">
ล้างบิล
</div>

<div class="cash-section">

<input type="number" id="cash" placeholder="จำนวนเงินที่รับ">

<div class="change">
เงินทอน: ฿<span id="change">0.00</span>
</div>

<div class="pay" onclick="pay()">
💰 ชำระเงิน
</div>

</div>

</div>

</div>

<script>

let items={}

/* ADD PRODUCT */

function addItem(id,name,price){

if(!items[id])
items[id]={name,price,qty:1}

else
items[id].qty++

render()

}

/* RENDER CART */

function render(){

let html='<tr><th>สินค้า</th><th>จำนวน</th><th>รวม</th><th></th></tr>'

let total=0

for(let id in items){

let i=items[id]
let sum=i.qty*i.price
total+=sum

html+=`<tr>

<td>${i.name}</td>

<td>
<div class="qty">
<button onclick="dec(${id})">-</button>
${i.qty}
<button onclick="inc(${id})">+</button>
</div>
</td>

<td>฿${sum.toFixed(2)}</td>

<td class="remove" onclick="del(${id})">✕</td>

</tr>`

}

document.getElementById('cart').innerHTML=html

document.getElementById('total').innerText=total.toFixed(2)

const cash=parseFloat(document.getElementById('cash').value)||0
const change=cash-total

document.getElementById('change').innerText=
change>=0?change.toFixed(2):'0.00'

}

/* QTY + */

function inc(id){

items[id].qty++
render()

}

/* QTY - */

function dec(id){

items[id].qty--

if(items[id].qty<=0)
delete items[id]

render()

}

/* DELETE */

function del(id){

delete items[id]
render()

}

/* CLEAR CART */

function clearCart(){

items={}
render()

}

/* CASH INPUT */

document.getElementById('cash')
.addEventListener('input',render)

/* SCAN BARCODE */

function scan(e){

if(e.key==="Enter"){

let code=e.target.value

fetch("scan.php?code="+code)
.then(r=>r.json())
.then(p=>{

if(p)
addItem(p.id,p.name,p.price)

})

e.target.value=''

}

}

/* PAY */

function pay(){

let total=parseFloat(document.getElementById('total').innerText)
let cash=parseFloat(document.getElementById('cash').value)

if(Object.keys(items).length===0){

alert('ไม่มีสินค้า')
return

}

if(!cash||cash<total){

alert('เงินสดไม่เพียงพอ')
return

}

fetch("save_order.php",{

method:"POST",

headers:{
'Content-Type':'application/json'
},

body:JSON.stringify(items)

})

.then(r=>r.text())

.then(id=>{

alert(`เงินทอน: ฿${(cash-total).toFixed(2)}`)

window.location="receipt.php?id="+id

})

}

</script>

</body>
</html>