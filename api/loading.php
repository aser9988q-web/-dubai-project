<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>جاري التحقق...</title>
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
            width: 50px; height: 50px;
            border: 5px solid #ddd;
            border-top: 5px solid var(--main-green);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .text { color: #333; font-size: 18px; font-weight: 600; }
        .sub-text { color: #6e6e6e; font-size: 14px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="loading-container">
        <div class="spinner"></div>
        <div class="text">جاري الاتصال بالنظام المركزي...</div>
        <div class="sub-text">يرجى عدم إغلاق الصفحة أو عمل تحديث</div>
    </div>

    <script>
        // هنا الكود اللي بيراقب حالة الطلب في Firebase لو حبيت
        // أو بيحول المستخدم لصفحة النتائج بعد 5 ثواني مثلاً
        setTimeout(() => {
            const orderId = sessionStorage.getItem("last_order_id");
            if(orderId) {
                // هنا تحوله لصفحة النتيجة (بشرط تخليها برضه .html أو تظبط الـ vercel.json)
                // window.location.href = "results.html"; 
            }
        }, 5000);
    </script>
</body>
</html>
