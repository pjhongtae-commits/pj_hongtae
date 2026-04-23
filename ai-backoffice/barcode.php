<?php
require "config/db.php";

// ดึงสินค้าทั้งหมด
$products = $pdo->query("SELECT * FROM products ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Barcode Sticker PDF</title>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<style>
body{font-family:Arial;margin:0;background:#f1f2f6;}
.header{background:#2f3542;color:white;padding:15px;font-size:20px;}
.container{padding:20px;}
.controls{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:15px;align-items:center;}
.controls input, .controls select, .controls button{padding:8px;font-size:14px;border-radius:5px;border:1px solid #ccc;}
.controls button{background:#2ed573;color:white;border:none;cursor:pointer;}
.controls button:hover{background:#27ae60;}
table{width:100%;background:white;border-collapse:collapse;}
th,td{padding:8px;border-bottom:1px solid #eee;text-align:center;}
.qty{width:60px;}
#printArea{display:grid;grid-template-columns:repeat(auto-fill,minmax(3cm,1fr));gap:2mm;}
.sticker{display:flex;flex-direction:column;justify-content:center;align-items:center;border:1px solid #ccc;padding:2px;box-sizing:border-box;text-align:center;width:3cm;height:2cm;}
.sticker .name{font-size:8px;height:10px;overflow:hidden;}
.sticker .price{font-size:9px;}
.sticker .code{font-size:7px;}
svg{width:100%;height:30px;}
@media print{.no-print{display:none;}}
@media(max-width:768px){#printArea{grid-template-columns:repeat(auto-fill,minmax(4cm,1fr));}table, th, td{font-size:14px;}.controls{flex-direction:column;align-items:flex-start;}}
</style>
</head>

<body>

<div class="header no-print">🏷️ พิมพ์ / PDF สติ๊กเกอร์สินค้า</div>

<div class="container no-print">

<div class="controls">
    <input type="text" id="search" placeholder="ค้นหาสินค้า...">
    <label>ขนาดสติ๊กเกอร์:
        <select id="stickerSize">
            <option value="3x2">3x2 cm</option>
            <option value="4x3">4x3 cm</option>
        </select>
    </label>
    <button onclick="previewSticker()">Preview</button>
    <button onclick="printSticker()">Print</button>
    <button onclick="exportPDF()">Export PDF</button>
</div>

<table>
<thead>
<tr>
<th>สินค้า</th>
<th>Barcode</th>
<th>ราคา</th>
<th>จำนวน</th>
</tr>
</thead>
<tbody>
<?php foreach($products as $p): ?>
<tr>
<td><?= htmlspecialchars($p['name']) ?></td>
<td><?= $p['barcode'] ?: $p['id'] ?></td>
<td>฿<?= number_format($p['price'],2) ?></td>
<td>
<input type="number" class="qty" min="0" value="0"
data-name="<?= htmlspecialchars($p['name']) ?>"
data-price="<?= $p['price'] ?>"
data-code="<?= $p['barcode'] ?: $p['id'] ?>">
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

</div>

<!-- preview / print area -->
<div id="printArea"></div>

<script>
// search filter
document.getElementById('search').addEventListener('input', function(){
    let filter = this.value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(tr=>{
        let name = tr.cells[0].innerText.toLowerCase();
        tr.style.display = name.includes(filter) ? '' : 'none';
    });
});

function previewSticker(){ generateStickers(false); }
function printSticker(){ generateStickers(true); }

function generateStickers(doPrint){
    let html='';
    const size = document.getElementById('stickerSize').value.split('x');
    const width = size[0]+'cm';
    const height = size[1]+'cm';
    const stickers = document.querySelectorAll('.qty');

    stickers.forEach(i=>{
        const qty = parseInt(i.value);
        if(qty <= 0) return;
        const name = i.dataset.name;
        const price = i.dataset.price;
        let code = i.dataset.code || (parseInt(price) || i.dataset.name.replace(/\D/g,'')) + '';
        code = code.toString();
        for(let x=0;x<qty;x++){
            html+=`
            <div class="sticker" style="width:${width};height:${height}">
                <div class="name">${name}</div>
                <svg class="barcode"></svg>
                <div class="code">${code}</div>
                <div class="price">฿${parseFloat(price).toFixed(2)}</div>
            </div>`;
        }
    });

    const area = document.getElementById('printArea');
    area.innerHTML = html;

    setTimeout(()=>{
        document.querySelectorAll('.sticker svg').forEach((e,i)=>{
            let code = document.querySelectorAll('.sticker .code')[i].innerText;
            JsBarcode(e, code.toString(), {format:"CODE128", displayValue:false, height:30});
        });
        if(doPrint) window.print();
    },200);
}

// Export PDF
async function exportPDF(){
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p','mm','a4');
    const printArea = document.getElementById('printArea');

    // แบ่งหน้า ถ้ามีหลายสติ๊กเกอร์
    await html2canvas(printArea).then(canvas=>{
        const imgData = canvas.toDataURL('image/png');
        const imgProps = pdf.getImageProperties(imgData);
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
        pdf.addImage(imgData,'PNG',0,0,pdfWidth,pdfHeight);
        pdf.save('stickers.pdf');
    });
}
</script>

</body>
</html>