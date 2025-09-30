<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>تسجيل دخول المسؤول - مشروع مسار</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <style>
    :root{
      --brand-1:#f39c12; --brand-2:#e67e22; --ink:#2c3e50; --muted:#7f8c8d;
      --bg:#f7f9fc; --card:#ffffff; --shadow:0 12px 40px rgba(0,0,0,.12);
      --ring: rgba(230,126,34,.18);
    }
    *{margin:0;padding:0;box-sizing:border-box}
    html,body{height:100%}
    body{font-family:'Cairo',sans-serif; color:var(--ink); background:var(--bg); overflow:hidden}

    /* خلفية مرئية لطيفة (تدرجات + توهج خفيف + جزيئات) */
    .scene{position:fixed; inset:0; z-index:-3; background:
      radial-gradient(1200px 600px at 100% -10%, rgba(243,156,18,.22), transparent 60%),
      radial-gradient(1000px 600px at -10% 110%, rgba(230,126,34,.18), transparent 60%),
      linear-gradient(135deg,#fbfcff 0%,#f1f5ff 50%,#eef5ff 100%);
    }
    .glow{position:fixed; inset:0; z-index:-2; filter:blur(60px); pointer-events:none; background:
      radial-gradient(520px 260px at 20% 30%, rgba(243,156,18,.26), transparent 60%),
      radial-gradient(520px 260px at 80% 70%, rgba(230,126,34,.20), transparent 60%);
      animation:floatGlow 16s ease-in-out infinite;
    }
    @keyframes floatGlow{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}

    .particles{position:fixed; inset:0; z-index:-1; pointer-events:none; overflow:hidden}
    .dot{position:absolute; width:6px; height:6px; border-radius:50%; background:linear-gradient(135deg,#fff,rgba(255,255,255,.5)); box-shadow:0 2px 8px rgba(0,0,0,.1); opacity:.7; animation:rise linear infinite}
    .dot:nth-child(1){left:10%; animation-duration:16s}
    .dot:nth-child(2){left:26%; width:4px;height:4px; animation-duration:18s; animation-delay:2s}
    .dot:nth-child(3){left:44%; animation-duration:15s; animation-delay:1s}
    .dot:nth-child(4){left:62%; width:5px;height:5px; animation-duration:17s; animation-delay:3s}
    .dot:nth-child(5){left:78%; animation-duration:15s; animation-delay:2s}
    .dot:nth-child(6){left:90%; width:4px;height:4px; animation-duration:19s; animation-delay:4s}
    @keyframes rise{0%{transform:translateY(110vh);opacity:0}10%,90%{opacity:.85}100%{transform:translateY(-10vh);opacity:0}}

    /* نافبار زجاجي صغير */
    .navbar{position:fixed; inset-inline:0; top:0; z-index:10; background:rgba(255,255,255,.55); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); border-bottom:1px solid rgba(255,255,255,.5)}
    .nav-wrap{max-width:980px; margin:auto; padding:12px 18px; display:flex; align-items:center; justify-content:space-between}
    .brand{display:flex; align-items:center; gap:10px; color:var(--brand-2); text-decoration:none; font-weight:800; font-size:1.2rem}
    .brand i{font-size:1.4rem}
    .dark-toggle{width:38px;height:38px; display:grid; place-items:center; border-radius:50%; cursor:pointer; color:var(--ink); background:rgba(255,255,255,.7); border:1px solid rgba(0,0,0,.06); transition:transform .2s, background .3s, color .3s}
    .dark-toggle:hover{transform:translateY(-2px); background:#fff}

    /* حاوية تسجيل الدخول (بطاقة زجاجية فخمة) */
    .wrap{min-height:100svh; display:grid; place-items:center; padding:80px 16px 24px}
    .login-container{width:min(420px, 94vw); background:rgba(255,255,255,.72); backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px); border:1px solid rgba(255,255,255,.65); border-radius:22px; box-shadow:var(--shadow); padding:28px 24px; transform:translateY(14px); opacity:0; transition:all .6s ease}
    .login-container.visible{transform:translateY(0); opacity:1}

    h1{margin-bottom:6px; font-size:1.4rem; color:var(--ink); font-weight:800}
    .subtitle{color:var(--muted); margin-bottom:18px; font-size:.98rem}

    .form-group{margin-bottom:14px; text-align:right}
    label{display:block; margin-bottom:8px; font-weight:700; color:var(--ink)}
    input{width:100%; padding:12px 14px; border:1px solid #dfe6e9; border-radius:14px; box-sizing:border-box; font-size:16px; background:#fff; color:var(--ink); transition:border-color .25s, box-shadow .25s}
    input::placeholder{color:#a7b0b4}
    input:focus{outline:none; border-color:var(--brand-2); box-shadow:0 8px 22px var(--ring), 0 0 0 3px rgba(230,126,34,.12)}

    button[type="submit"]{width:100%; padding:14px 18px; background:linear-gradient(135deg,var(--brand-1),var(--brand-2)); color:#fff; border:none; border-radius:50px; cursor:pointer; font-size:17px; font-weight:800; transition:transform .15s, box-shadow .3s}
    button[type="submit"]:hover{transform:translateY(-2px); box-shadow:0 16px 36px rgba(230,126,34,.35)}

    .error-message{color:#8a1c23; background:#fde9ec; border:1px solid #f7c9d0; padding:12px; border-radius:14px; margin-bottom:14px; text-align:right}

    /* وضع ليلي */
    body.dark-mode{--ink:#eaeaea; --muted:#bdbdbd; --bg:#0f1115}
    body.dark-mode .navbar{background:rgba(14,14,16,.55); border-bottom:1px solid rgba(255,255,255,.08)}
    body.dark-mode .dark-toggle{color:#f1f1f1; background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.06)}
    body.dark-mode .scene{background:
      radial-gradient(1200px 600px at 100% -10%, rgba(243,156,18,.13), transparent 60%),
      radial-gradient(1000px 600px at -10% 110%, rgba(230,126,34,.11), transparent 60%),
      linear-gradient(135deg,#0f1115 0%,#101319 50%,#0e1218 100%)}
    body.dark-mode .glow{background:
      radial-gradient(520px 260px at 20% 30%, rgba(243,156,18,.20), transparent 60%),
      radial-gradient(520px 260px at 80% 70%, rgba(230,126,34,.16), transparent 60%); filter:blur(70px)}
    body.dark-mode .login-container{background:rgba(20,20,22,.62); border-color:rgba(255,255,255,.08); box-shadow:0 10px 30px rgba(0,0,0,.55)}
    body.dark-mode h1{color:#fff}
    body.dark-mode .subtitle{color:#cfcfcf}
    body.dark-mode label{color:#f0f0f0}
    body.dark-mode input{background:#15171b; color:#f1f1f1; border-color:#2a2e35}
    body.dark-mode input:focus{border-color:#f39c12; box-shadow:0 8px 22px rgba(243,156,18,.22), 0 0 0 3px rgba(243,156,18,.12)}
    body.dark-mode .error-message{color:#ffd6da; background:#4b1f26; border-color:#7b2e38}

    @media (prefers-reduced-motion: reduce){ .glow,.dot,.login-container{animation:none !important; transition:none !important} }
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

  <!-- نافبار خفيف مع زر ليلي -->
  <nav class="navbar">
    <div class="nav-wrap">
      <a class="brand" href="{{ route('landing') }}"><i class="fas fa-brain"></i> مشروع مسار</a>
      <button class="dark-toggle" id="darkToggle" aria-label="تبديل الوضع"><i class="fas fa-moon"></i></button>
    </div>
  </nav>

  <!-- بطاقة تسجيل الدخول -->
  <main class="wrap">
    <div class="login-container" id="loginCard">
      <h1>لوحة تحكم مسار</h1>
      <p class="subtitle">الرجاء تسجيل الدخول للمتابعة</p>

      @if ($errors->any())
        <div class="error-message">{{ $errors->first() }}</div>
      @endif

      <form action="/admin/login" method="POST">
        @csrf
        <div class="form-group">
          <label for="email">البريد الإلكتروني</label>
          <input type="email" id="email" name="email" required />
        </div>
        <div class="form-group">
          <label for="password">كلمة المرور</label>
          <input type="password" id="password" name="password" required />
        </div>
        <button type="submit"><i class="fa-solid fa-right-to-bracket" style="margin-left:8px"></i> تسجيل الدخول</button>
      </form>
    </div>
  </main>

  <script>
    // إظهار البطاقة بنعومة
    window.addEventListener('load', ()=>{
      const card = document.getElementById('loginCard');
      if(card){ requestAnimationFrame(()=> card.classList.add('visible')); }
    });

    // وضع ليلي (يبدأ نهاري دائمًا، يتذكر اختيار المستخدم)
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
