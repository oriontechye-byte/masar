<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>تسجيل بيانات الطالب - مشروع مسار</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <style>
    :root{
      --brand-1:#f39c12;
      --brand-2:#e67e22;
      --ink:#2c3e50;
      --muted:#7f8c8d;
      --card:#ffffff;
      --bg:#f8f9fb;
      --glass: rgba(255,255,255,0.08);
      --ring: rgba(230,126,34,0.18);
      --shadow: 0 10px 40px rgba(0,0,0,0.12);
    }
    *{margin:0;padding:0;box-sizing:border-box}
    html,body{height:100%}
    body{
      font-family:'Cairo',sans-serif;
      color:var(--ink);
      background: var(--bg);
      overflow-x:hidden;
    }

    /* ===== خلفية بصرية فخمة (تدرج + موجة + ضباب خفيف) ===== */
    .scene{
      position:fixed; inset:0; z-index:-2;
      background:
        radial-gradient(1200px 600px at 100% -10%, rgba(243,156,18,.22), transparent 60%),
        radial-gradient(1000px 600px at -10% 110%, rgba(230,126,34,.18), transparent 60%),
        linear-gradient(135deg,#fbfcff 0%,#f3f6ff 50%,#eef5ff 100%);
    }
    .glow{
      position:fixed; inset:0; z-index:-1; pointer-events:none;
      filter: blur(60px);
      background:
        radial-gradient(500px 240px at 20% 20%, rgba(243,156,18,.28), transparent 60%),
        radial-gradient(520px 260px at 80% 70%, rgba(230,126,34,.22), transparent 60%);
      animation: floatGlow 16s ease-in-out infinite;
    }
    @keyframes floatGlow{
      0%,100%{transform:translateY(0)}
      50%{transform:translateY(-14px)}
    }

    /* جزيئات خفيفة جدًا */
    .particles{position:fixed; inset:0; pointer-events:none; z-index:-1; overflow:hidden}
    .dot{
      position:absolute; width:6px; height:6px; border-radius:50%;
      background: linear-gradient(135deg, #fff, rgba(255,255,255,.5));
      box-shadow:0 2px 8px rgba(0,0,0,.1);
      opacity:.7;
      animation: rise linear infinite;
    }
    .dot:nth-child(1){left:8%; animation-duration:14s; animation-delay:0s}
    .dot:nth-child(2){left:24%; width:4px; height:4px; animation-duration:18s; animation-delay:2s}
    .dot:nth-child(3){left:42%; animation-duration:16s; animation-delay:1s}
    .dot:nth-child(4){left:61%; width:5px; height:5px; animation-duration:17s; animation-delay:3s}
    .dot:nth-child(5){left:78%; animation-duration:15s; animation-delay:2s}
    .dot:nth-child(6){left:90%; width:4px; height:4px; animation-duration:19s; animation-delay:4s}
    @keyframes rise{
      0%{transform:translateY(110vh); opacity:0}
      10%{opacity:.85}
      90%{opacity:.85}
      100%{transform:translateY(-10vh); opacity:0}
    }

    /* ===== شريط علوي زجاجي ===== */
    .navbar{
      position:sticky; top:0; z-index:10;
      background: rgba(255,255,255,.65);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-bottom:1px solid rgba(255,255,255,.4);
    }
    .nav-wrap{
      max-width:1100px; margin:auto; padding:14px 20px;
      display:flex; align-items:center; justify-content:space-between;
    }
    .brand{
      display:flex; align-items:center; gap:10px;
      color:var(--brand-2); text-decoration:none; font-weight:800; font-size:1.3rem;
    }
    .brand i{font-size:1.5rem}
    .nav-actions{display:flex; align-items:center; gap:12px}
    .dark-toggle{
      width:40px; height:40px; display:grid; place-items:center;
      border-radius:50%; cursor:pointer; color:var(--ink);
      background: rgba(255,255,255,.6);
      border:1px solid rgba(0,0,0,.06);
      transition: transform .2s ease, background .3s ease, color .3s ease;
    }
    .dark-toggle:hover{ transform: translateY(-2px); background:#fff; }

    /* ===== هيرو مختصر أنيق ===== */
    .hero{
      max-width:1100px; margin: 40px auto 0; padding: 10px 20px 0;
      text-align:center;
    }
    .title{
      font-size: clamp(1.8rem, 2.2rem, 2.6rem);
      font-weight:800; color:var(--ink);
      letter-spacing:.2px;
      animation: slideUp .8s ease both;
    }
    .subtitle{
      color:var(--muted); margin-top:8px; font-size:1.05rem;
      animation: slideUp .8s ease .08s both;
    }
    @keyframes slideUp{
      from{opacity:0; transform:translateY(18px)}
      to{opacity:1; transform:translateY(0)}
    }

    /* ===== بطاقة فورم زجاجية فخمة ===== */
    .wrap{
      max-width:1100px; margin: 22px auto 70px; padding: 0 20px;
    }
    .card{
      max-width:700px; margin:auto;
      background: rgba(255,255,255,.7);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border:1px solid rgba(255,255,255,.6);
      border-radius:22px;
      box-shadow: var(--shadow);
      padding: 26px 24px;
      transform: translateY(14px);
      opacity:0;
      transition: all .6s ease;
    }
    .card.visible{ transform:translateY(0); opacity:1; }

    .hint{ text-align:center; color:var(--muted); margin-bottom:12px; }

    .grid{
      display:grid; gap:16px;
      grid-template-columns: 1fr 1fr;
    }
    .grid .full{ grid-column:1/-1; }

    .field{
      position:relative;
    }
    .label{
      display:block; margin-bottom:8px; font-weight:700; color:var(--ink);
    }
    .control{
      width:100%; border-radius:14px; font-size:16px;
      padding: 14px 14px;
      border:1px solid #dfe6e9; background:#fff; color:var(--ink);
      transition: border-color .25s ease, box-shadow .25s ease, transform .08s ease;
      outline:none;
    }
    .control::placeholder{ color:#a7b0b4; }
    .control:focus{
      border-color: var(--brand-2);
      box-shadow: 0 8px 22px var(--ring), 0 0 0 3px rgba(230,126,34,.12);
    }

    .submit{
      width:100%; border:none; cursor:pointer;
      background: linear-gradient(135deg, var(--brand-1), var(--brand-2));
      color:#fff; padding: 14px 18px; font-size:18px; font-weight:800;
      border-radius: 50px; margin-top:6px;
      position:relative; overflow:hidden;
      transition: transform .15s ease, box-shadow .3s ease, filter .2s ease;
    }
    .submit:hover{ transform: translateY(-2px); box-shadow: 0 16px 36px rgba(230,126,34,.35); }
    .submit:active{ transform: translateY(0); }
    .submit::before{
      content:''; position:absolute; inset:0; left:-100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.18), transparent);
      transition:left .6s;
    }
    .submit:hover::before{ left:100%; }

    .errors{
      color:#8a1c23; background:#fde9ec; border:1px solid #f7c9d0;
      padding:14px; border-radius:14px; margin-bottom:10px;
    }
    .errors strong{display:block; margin-bottom:6px}
    .errors ul{margin:0; padding-right:18px}

    /* ===== وضع ليلي ===== */
    body.dark-mode{
      --ink:#eaeaea; --muted:#bdbdbd; --card:#1f1f1f; --bg:#0f1115;
      --glass: rgba(255,255,255,0.06); --shadow: 0 10px 30px rgba(0,0,0,.55);
    }
    body.dark-mode .navbar{
      background: rgba(14,14,16,.55);
      border-bottom:1px solid rgba(255,255,255,.08);
    }
    body.dark-mode .dark-toggle{ color:#f1f1f1; background: rgba(255,255,255,.08); border-color:rgba(255,255,255,.06); }
    body.dark-mode .scene{
      background:
        radial-gradient(1200px 600px at 100% -10%, rgba(243,156,18,.13), transparent 60%),
        radial-gradient(1000px 600px at -10% 110%, rgba(230,126,34,.11), transparent 60%),
        linear-gradient(135deg,#0f1115 0%,#101319 50%,#0e1218 100%);
    }
    body.dark-mode .glow{
      background:
        radial-gradient(500px 240px at 20% 20%, rgba(243,156,18,.2), transparent 60%),
        radial-gradient(520px 260px at 80% 70%, rgba(230,126,34,.16), transparent 60%);
      filter: blur(70px);
    }
    body.dark-mode .title{ color:#fff }
    body.dark-mode .subtitle{ color:#cfcfcf }
    body.dark-mode .card{
      background: rgba(20,20,22,.6);
      border-color: rgba(255,255,255,.06);
      box-shadow: var(--shadow);
    }
    body.dark-mode .control{
      background:#15171b; color:#f1f1f1; border-color:#2a2e35;
    }
    body.dark-mode .control:focus{
      border-color:#f39c12;
      box-shadow: 0 8px 22px rgba(243,156,18,.22), 0 0 0 3px rgba(243,156,18,.12);
    }
    body.dark-mode .errors{
      color:#ffd6da; background:#4b1f26; border-color:#7b2e38;
    }

    /* تقليل الحركة للي يفضلون ذلك */
    @media (prefers-reduced-motion: reduce){
      .glow, .dot, .submit::before, .title, .subtitle, .card{ animation: none !important; transition: none !important;}
    }

    @media (max-width: 760px){
      .grid{ grid-template-columns:1fr; }
    }
  </style>
</head>
<body>
  <!-- خلفية -->
  <div class="scene"></div>
  <div class="glow"></div>
  <div class="particles" aria-hidden="true">
    <span class="dot"></span><span class="dot"></span><span class="dot"></span>
    <span class="dot"></span><span class="dot"></span><span class="dot"></span>
  </div>

  <!-- شريط علوي -->
  <header class="navbar">
    <div class="nav-wrap">
      <a class="brand" href="{{ route('landing') }}"><i class="fas fa-brain"></i> مشروع مسار</a>
      <div class="nav-actions">
        <button class="dark-toggle" id="darkToggle" aria-label="تبديل الوضع"><i class="fas fa-moon"></i></button>
      </div>
    </div>
  </header>

  <!-- هيرو -->
  <section class="hero">
    <h1 class="title">تسجيل بيانات الطالب</h1>
    <p class="subtitle">املأ بياناتك بخطوة واحدة لتبدأ اختبار الذكاءات المتعددة</p>
  </section>

  <!-- بطاقة النموذج -->
  <main class="wrap">
    <div class="card" id="formCard">
      @if ($errors->any())
        <div class="errors">
          <strong>الرجاء تصحيح الأخطاء التالية:</strong>
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <p class="hint">جميع الحقول مطلوبة ما لم يُذكر أنها اختيارية</p>

      <form action="/register" method="POST" novalidate>
        @csrf

        <div class="grid">
          <div class="field full">
            <label class="label" for="full_name">الاسم الرباعي</label>
            <input class="control" type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" placeholder="مثال: أحمد ياسين عبده داغم " required />
          </div>

          <div class="field">
            <label class="label" for="whatsapp_number">رقم الهاتف (9 أرقام يبدأ بـ 7)</label>
            <input class="control" type="text" id="whatsapp_number" name="whatsapp_number"
                   value="{{ old('whatsapp_number') }}" placeholder="771234567" inputmode="numeric" pattern="7[0-9]{8}" required />
          </div>

          <div class="field">
            <label class="label" for="email">البريد الإلكتروني (اختياري)</label>
            <input class="control" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" />
          </div>

          <div class="field">
            <label class="label" for="governorate">المحافظة</label>
            <select class="control" id="governorate" name="governorate" required>
              <option value="" disabled selected>-- اختر المحافظة --</option>
              @foreach ($governorates as $governorate)
                <option value="{{ $governorate }}" {{ old('governorate') == $governorate ? 'selected' : '' }}>{{ $governorate }}</option>
              @endforeach
            </select>
          </div>

          <div class="field">
            <label class="label" for="gpa">المعدل في الثانوية</label>
            <input class="control" type="text" id="gpa" name="gpa" placeholder="مثال: 95.50" value="{{ old('gpa') }}" required />
          </div>

          <div class="field">
            <label class="label" for="graduation_year">سنة التخرج</label>
            <select class="control" id="graduation_year" name="graduation_year" required>
              <option value="" disabled selected>-- اختر السنة --</option>
              @foreach ($years as $year)
                <option value="{{ $year }}" {{ old('graduation_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
              @endforeach
            </select>
          </div>

          <div class="full">
            <button type="submit" class="submit">
              <i class="fa-solid fa-check" style="margin-left:8px"></i> بدء الاختبار
            </button>
          </div>
        </div>
      </form>
    </div>
  </main>

  <script>
    // ظهور البطاقة بسلاسة
    window.addEventListener('load', ()=> {
      const card = document.getElementById('formCard');
      if(card){ requestAnimationFrame(()=> card.classList.add('visible')); }
    });

    // وضع ليلي (يبدأ نهاري دائمًا، يتذكر الاختيار)
    const darkToggle = document.getElementById('darkToggle');
    const body = document.body;

    try{
      if(localStorage.getItem('dark-mode') === 'enabled'){
        body.classList.add('dark-mode');
        darkToggle.innerHTML = '<i class="fas fa-sun"></i>';
      }
    }catch(e){}

    darkToggle.addEventListener('click', ()=>{
      body.classList.toggle('dark-mode');
      const enabled = body.classList.contains('dark-mode');
      try{ localStorage.setItem('dark-mode', enabled ? 'enabled' : 'disabled'); }catch(e){}
      darkToggle.innerHTML = enabled ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    });
  </script>
</body>
</html>
