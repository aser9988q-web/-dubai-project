<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>تم الدفع بنجاح - شرطة دبي</title>
  <style>
    :root { --main-green: #008b47; }
    body { margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; text-align: center; }
    .success-card { background: #fff; width: 90%; max-width: 400px; padding: 40px 20px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    .check-icon { width: 80px; height: 80px; background: var(--main-green); color: #fff; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 40px; margin: 0 auto 20px; }
    .title { font-size: 22px; font-weight: bold; color: #333; margin-bottom: 10px; }
    .msg { font-size: 14px; color: #666; line-height: 1.6; margin-bottom: 25px; }
    .ref-box { background: #f9f9f9; border: 1px dashed #ddd; padding: 15px; border-radius: 10px; font-size: 13px; color: #444; margin-bottom: 20px; }
    .home-btn { width: 100%; height: 50px; background: #333; color: #fff; border: none; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; }
  </style>
</head>
<body>

<div class="success-card">
  <div class="check-icon">✓</div>
  <div class="title">تمت عملية الدفع بنجاح</div>
  <div class="msg">شكراً لك. تم استلام مبلغ المخالفات بنجاح، وسيتم تحديث ملفك المروري خلال 24 ساعة عمل.</div>
  
  <div class="ref-box">
    رقم المرجع: <span id="refNum"></span><br>
    تاريخ العملية: <?php echo date('Y-m-d H:i'); ?>
  </div>

  <button class="home-btn" onclick="window.location.href='index.php'">العودة للرئيسية</button>
</div>

<script>
    // توليد رقم مرجع وهمي
    document.getElementById('refNum').innerText = 'DP-' + Math.floor(Math.random() * 90000000 + 10000000);
</script>
</body>
</html>