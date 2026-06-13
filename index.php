<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quotation Dashboard</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>

<body>

<!-- ===== Sidebar ===== -->
<div class="sidebar">

  <div class="section">
    <h6>📋 QUOTATION DETAILS</h6>

    <label>QUOTATION NUMBERS</label>
    <input type="text" name="ref" value="AA-2026">


    <label>DATE</label>
    <input type="date" name="date" value="2026-06-10">


    <label>CLIENT ADDRESS</label>
    <input type="text" name="address" placeholder="Al Sufouh, Dubai - UAE">


    <label>CLIENT PHONE</label>
    <input type="text" name="phone" placeholder="NA">


    <label>CLIENT EMAIL</label>
    <input type="text" name="email" placeholder="NA">

    <label>SUBJECT / WORK TYPE</label>
    <input type="text" name="subject" placeholder="Supply & Installation of Glass Room Work">
  </div>

  <div class="section">
    <h6>📦 LINE ITEMS</h6>

    <button class="btn btn-green">📦 Load All Glass Room Items</button>

    <label>Quick Select</label>
    <select name="quick_item" class="form-select">
  <option value="">-- Select Item --</option>
  <option value="glass_door">Structure</option>
  <option value="window_frame">Swing Door</option>
  <option value="aluminium_structure">3 Side closing with Cement board and Gypsum</option>
  <option value="sliding_door">SandWich panel Roof</option>
  <option value="sliding_door">Gypsum Ceiling</option>
  <option value="sliding_door">Electric</option>
  <option value="sliding_door">AC</option>
  <option value="sliding_door">Custom</option>

</select>

    <label>Item Name</label>
<input type="text" name="item_name" class="form-control">

    <label>Description</label>
    <textarea rows="2"></textarea>

    <div class="row">
      <div class="col-6">
        <label>Width</label>
<input type="number" name="width" class="form-control">
      </div>
      <div class="col-6">
        <label>Height</label>
<input type="number" name="height" class="form-control">

      </div>
    </div>

    <div class="row">
      <div class="col-6">
        <label>Quantity</label>
<input type="number" name="qty" value="1" class="form-control">
      </div>
      <div class="col-6">
<label>Price</label>
<input type="number" name="price" class="form-control">
      </div>
    </div>

    <label>OR Fixed Price (AED)</label>
    <input type="text">

    <button class="btn btn-outline-custom">+ Add Custom Item</button>

    <button id="generateBtn" class="btn btn-red">
⚡ Generate Quotation
</button>

  </div>

</div>

<!-- ===== Main Content ===== -->
<div class="main-content">

  <div class="top-buttons">
    <button onclick="downloadAllPDF()" class="btn btn-pdf me-2">📄 Download PDF</button>
    <button class="btn btn-print">🖨 Print</button>
  </div>

  <!-- INVOICE AREA (PDF BANANE KE LIYE) -->
  <div id="invoice">

    <div class="main-card">
      <p>
        Fill the form and click 
      </p>
    </div>

  </div>

  <!-- DATABASE TABLE -->
  <div class="mt-5">
    <h4>Saved Quotations</h4>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Ref</th>
          <th>Date</th>
          <th>Total</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="quotTable"></tbody>
    </table>
  </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    loadQuotations();

    // ✅ SAFE NAVBAR BUTTON
    const btn = document.getElementById("generateBtn");

    if (btn) {
        btn.addEventListener("click", saveQuotation);
    } else {
        console.error("generateBtn not found in navbar");
    }
});


// ================= SAVE QUOTATION =================
function saveQuotation() {

    let itemNameEl = document.querySelector('[name="item_name"]');
    let widthEl = document.querySelector('[name="width"]');
    let heightEl = document.querySelector('[name="height"]');
    let qtyEl = document.querySelector('[name="qty"]');
    let priceEl = document.querySelector('[name="price"]');

    // ✅ SAFE CHECK (NO CRASH)
    if (!itemNameEl || !qtyEl || !priceEl) {
        alert("Form inputs missing ❌ HTML check karo");
        return;
    }

    let qty = parseInt(qtyEl.value || 1);
    let price = parseFloat(priceEl.value || 0);
    let amount = qty * price;

    let data = {
        ref: document.querySelector('[name="ref"]').value,
        date: document.querySelector('[name="date"]').value,
        address: document.querySelector('[name="address"]').value,
        phone: document.querySelector('[name="phone"]').value,
        email: document.querySelector('[name="email"]').value,
        subject: document.querySelector('[name="subject"]').value,

        items: [
            {
                name: itemNameEl.value,
                width: parseInt(widthEl?.value || 0),
                height: parseInt(heightEl?.value || 0),
                qty: qty,
                price: price,
                amount: amount
            }
        ]
    };

    axios.post("save.php", data)
    .then(res => {

        if(res.data.success){
            alert("Quotation Saved ✅");
            loadQuotations();
        } else {
            alert("Save Failed ❌");
        }

    })
    .catch(err => {
        console.error(err);
        alert("Server Error ❌");
    });
}

function loadQuotations() {

    axios.get("get_quotations.php")
    .then(res => {

        let html = "";

        res.data.forEach(q => {

            let i = q.items[0];

            html += `
            <tr>
                <td>${q.id}</td>
                <td>${q.ref}</td>
                <td>${q.quot_date}</td>
                <td>${q.total_amount}</td>
                
                <td>
                    ${i.name}<br>
                    W:${i.width} H:${i.height}<br>
                    Qty:${i.qty} Price:${i.price}
                </td>

                <td>
                    <button class="btn btn-sm btn-primary" onclick="viewQuotation(${q.id})">
                        View
                    </button>

                    <button class="btn btn-sm btn-danger" onclick="downloadQuotation(${q.id})">
                        PDF
                    </button>
                </td>
            </tr>`;
        });

        document.getElementById("quotTable").innerHTML = html;
    })
    .catch(err => {
        console.error("Load Error:", err);
    });
}


// ================= VIEW SINGLE QUOTATION =================
function viewQuotation(id) {

    axios.get("get_single.php?id=" + id)
    .then(res => {

        let q = res.data;

        let html = `
            <h3>Quotation #${q.ref}</h3>
            <p><b>Date:</b> ${q.quot_date}</p>
            <p><b>Address:</b> ${q.address}</p>
            <p><b>Phone:</b> ${q.phone}</p>
            <p><b>Email:</b> ${q.email}</p>
            <p><b>Subject:</b> ${q.subject}</p>
            <hr>
        `;

        q.items.forEach(item => {
            html += `
                <div>
                    ${item.name} | ${item.qty} x ${item.price} = ${item.amount}
                </div>
            `;
        });

        document.getElementById("invoice").innerHTML = html;
    });
}


// ================= DOWNLOAD PDF =================
function downloadQuotation(id) {

    axios.get("get_single.php?id=" + id)
    .then(res => {

        let q = res.data;

        let rows = "";

        q.items.forEach(item => {
            rows += `
            <tr>
                <td style="border:1px solid #000; padding:6px;">${item.name}</td>
                <td style="border:1px solid #000; padding:6px;">${item.width}</td>
                <td style="border:1px solid #000; padding:6px;">${item.height}</td>
                <td style="border:1px solid #000; padding:6px;">${item.qty}</td>
                <td style="border:1px solid #000; padding:6px;">${item.price}</td>
                <td style="border:1px solid #000; padding:6px;">${item.amount}</td>
            </tr>`;
        });

        let html = `
        <div style="
            font-family: Arial;
            padding: 20px;
            color: #000;
            background: #fff;
        ">

            <h2 style="text-align:center; margin-bottom:10px;">
                QUOTATION
            </h2>

            <hr>

            <p><b>Ref:</b> ${q.ref}</p>
            <p><b>Date:</b> ${q.quot_date}</p>
            <p><b>Address:</b> ${q.address}</p>
            <p><b>Phone:</b> ${q.phone}</p>
            <p><b>Email:</b> ${q.email}</p>
            <p><b>Subject:</b> ${q.subject}</p>

            <br>

            <table style="
                width:100%;
                border-collapse: collapse;
                font-size: 14px;
            ">
                <thead>
                    <tr>
                        <th style="border:1px solid #000; padding:8px;">Item</th>
                        <th style="border:1px solid #000; padding:8px;">Width</th>
                        <th style="border:1px solid #000; padding:8px;">Height</th>
                        <th style="border:1px solid #000; padding:8px;">Qty</th>
                        <th style="border:1px solid #000; padding:8px;">Price</th>
                        <th style="border:1px solid #000; padding:8px;">Total</th>
                    </tr>
                </thead>

                <tbody>
                    ${rows}
                </tbody>
            </table>

            <br><br>

            <h3 style="text-align:right;">
                TOTAL: ${q.total_amount}
            </h3>

        </div>
        `;

        let temp = document.createElement("div");
        temp.style.width = "800px";
        temp.style.background = "#fff";
        temp.innerHTML = html;

        document.body.appendChild(temp); // IMPORTANT FIX

        html2pdf().set({
            margin: 10,
            filename: `quotation-${q.ref}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: {
                scale: 2,
                useCORS: true,
                scrollY: 0
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            }
        }).from(temp).save().then(() => {
            document.body.removeChild(temp);
        });

    });
}


// ================= CURRENT PAGE PDF =================
function downloadAllPDF() {
    let element = document.getElementById("invoice");
    html2pdf().from(element).save("quotation.pdf");
}
</script>
</body>
</html>