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
        <select id="plateSource" class="input-style" required>
          <option value="">اختر</option>
        </select>
/* manus temp edit */
