<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>البحث عن اختبار - مشروع مسار</title>

  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    :root{
      --brand:#e67e22;
      --bg-grad:linear-gradient(135deg,#f39c12 0%,#e67e22 50%,#d35400 100%);
      --glass-bg:rgba(255,255,255,.20);
      --glass-border:rgba(255,255,255,.35);
      --text:#2c3e50;
      --white:#fff;
      --ok:#18a999;
      --err:#d72638;
    }
    body{font-family:'Cairo',sans-serif;overflow-x:hidden;color:var(--text);background:#fff}

    /* ===== Navbar ===== */
    .navbar{position:fixed;top:0;left:0;right:0;background:rgba(255,255,255,.95);backdrop-filter:blur(10px);padding:15px 0;z-index:1000;transition:.3s;box-shadow:0 2px 20px rgba(0,0,0,.1)}
    .navbar.scrolled{background:rgba(255,255,255,.98);box-shadow:0 2px 30px rgba(0,0,0,.15)}
    .nav-container{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;padding:0 20px}
    .logo{font-size:1.8rem;font-weight:800;color:var(--brand);text-decoration:none;display:flex;gap:10px;align-items:center}
    .logo i{font-size:2rem}
    .dark-toggle{cursor:pointer;font-size:1.3rem;color:#2c3e50;padding:8px;border-radius:999px;transition:.25s}
    .dark-toggle:hover{background:#00000014}

    /* ===== Hero background ===== */
    .hero{min-height:100vh;background:var(--bg-grad);position:relative;display:flex;align-items:center;justify-content:center;padding:120px 16px}
    .hero::before{content:'';position:absolute;inset:0;background-image:
      radial-gradient(circle at 20% 80%,rgba(255,255,255,.10) 0%,transparent 50%),
      radial-gradient(circle at 80% 20%,rgba(255,255,255,.08) 0%,transparent 50%),
      radial-gradient(circle at 40% 40%,rgba(255,255,255,.05) 0%,transparent 50%);
      animation:float 20s ease-in-out infinite}
    @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}

    /* ===== Card ===== */
    .card{position:relative;z-index:2;width:min(520px,92vw);background:var(--glass-bg);
      border:1px solid var(--glass-border);backdrop-filter:blur(12px);border-radius:22px;
      padding:26px 22px;box-shadow:0 20px 60px rgba(0,0,0,.18);color:#fff;text-align:center}
    .card h1{font-size:1.5rem;font-weight:900;margin-bottom:8px}
    .card p{opacity:.95;margin-bottom:16px}

    .field{text-align:right;margin-top:12px}
    label{display:block;font-weight:800;margin-bottom:6px;color:#fefefe}
    .input{width:100%;padding:12px 14px;border-radius:12px;border:1px solid var(--glass-border);
      background:rgba(255,255,255,.15);color:#fff;outline:none;transition:.2s}
    .input:focus{box-shadow:0 0 0 3px rgba(255,255,255,.25)}
    .input::placeholder{color:#f0f0f0cc}
    .hint{font-size:.85rem;color:#fff;opacity:.9;margin-top:6px}
    .mini-error{margin-top:6px;font-size:.9rem;background:#fff;border-radius:10px;padding:8px 10px;color:var(--err);display:none}
    .mini-ok{margin-top:6px;font-size:.9rem;background:#fff;border-radius:10px;padding:8px 10px;color:var(--ok);display:none}

    .btn{margin-top:18px;width:100%;padding:12px 16px;border:none;border-radius:999px;
      font-weight:900;cursor:pointer;background:#fff;color:var(--brand);display:inline-flex;
      justify-content:center;align-items:center;gap:8px;transition:transform .15s ease}
    .btn:hover{transform:translateY(-2px)}
    .btn[disabled]{opacity:.7;cursor:not-allowed;transform:none}

    /* Home icon button (bottom of card) */
    .actions{display:flex;justify-content:center;margin-top:14px}
    .home-btn{
      width:48px;height:48px;border-radius:999px;border:1px solid var(--glass-border);
      background:#ffffff; color:var(--brand); display:inline-flex; align-items:center; justify-content:center;
      box-shadow:0 6px 16px rgba(0,0,0,.15); text-decoration:none; transition:.2s}
    .home-btn:hover{transform:translateY(-2px)}

    /* ===== Error/flash boxes ===== */
    .alert{color:#2b2b2b;background:rgba(255,255,255,.9);border:2px solid #eaeaea;border-radius:12px;padding:14px;margin:14px 0;text-align:right}
    .alert.error{color:#4b0008;border-color:#f5c6cb}
    .alert.info{border-color:#d9edf7}
    .alert strong{display:block;margin-bottom:6px}
    .alert ul{margin:0;padding-right:18px}
    .input.error{border-color:#f5c6cb;background:rgba(255,255,255,.25)}

    /* ===== Dark mode ===== */
    body.dark-mode{color:#eaeaea;background:#0f0f0f}
    body.dark-mode .navbar{background:rgba(18,18,18,.95);box-shadow:0 2px 20px rgba(0,0,0,.4)}
    body.dark-mode .dark-toggle{color:#eaeaea}
    body.dark-mode .hero{background:linear-gradient(135deg,#1f1f1f 0%,#171717 50%,#101010 100%)}
    body.dark-mode .hero::before{background-image:
      radial-gradient(circle at 20% 80%,rgba(255,255,255,.06) 0%,transparent 50%),
      radial-gradient(circle at 80% 20%,rgba(255,255,255,.05) 0%,transparent 50%),
      radial-gradient(circle at 40% 40%,rgba(255,255,255,.04) 0%,transparent 50%)}
    body.dark-mode .card{background:rgba(18,18,18,.55);border-color:rgba(255,255,255,.18);color:#f1f1f1}
    body.dark-mode label{color:#f1f1f1}
    body.dark-mode .input{background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.22);color:#fff}
    body.dark-mode .input::placeholder{color:#ffffffb4}
    body.dark-mode .btn{background:#f39c12;color:#fff}
    body.dark-mode .btn:hover{background:#ffa424}
    body.dark-mode .home-btn{background:#1d1d1d;color:#f39c12;border-color:#2c2c2c}
    body.dark-mode .home-btn:hover{background:#262626}
    body.dark-mode .alert{background:#1b1b1b;border-color:#2a2a2a;color:#eee}
    body.dark-mode .alert.error{background:#2a1618;border-color:#8a2b34}
    body.dark-mode .mini-error, body.dark-mode .mini-ok{background:#1f1f1f}
    @media (max-width:420px){ .card{padding:22px 18px} .card h1{font-size:1.25rem} }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar" id="navbar">
    <div class="nav-container">
      <a href="{{ route('landing') }}" class="logo"><i class="fas fa-brain"></i> مشروع مسار</a>
      <i class="fas fa-moon dark-toggle" id="darkToggle" aria-label="تبديل الوضع"></i>
    </div>
  </nav>

  <!-- Hero -->
  <section class="hero" id="home">
    <div class="card" id="card">
      <h1>اختبار ما بعد المحاضرة</h1>
      <p>للبدء في الاختبار، أدخل رقم الهاتف الذي سجّلت به في اختبار ما قبل المحاضرة.</p>

      @if (session('info'))
        <div class="alert info">
          <strong><i class="fa-solid fa-circle-info"></i> ملاحظة:</strong>
          {{ session('info') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="alert error">
          <strong><i class="fa-solid fa-triangle-exclamation"></i> خطأ:</strong>
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form id="postForm" action="{{ route('post-test.submit') }}" method="POST" novalidate>
        @csrf
        <div class="field">
          <label for="whatsapp_number"><i class="fa-solid fa-phone"></i> رقم الهاتف</label>
          <input
            class="input @error('whatsapp_number') error @enderror"
            type="text"
            id="whatsapp_number"
            name="whatsapp_number"
            placeholder="مثال: 771234567"
            value="{{ old('whatsapp_number') }}"
            inputmode="numeric"
            autocomplete="tel-national"
            maxlength="15"
            required
            autofocus
          >
          <div class="hint">أدخل رقمك بالأرقام فقط بدون رموز أو مسافات.</div>
          <div class="mini-error" id="miniErr"><i class="fa-solid fa-circle-xmark"></i> الرجاء إدخال 7 إلى 15 رقمًا.</div>
          <div class="mini-ok" id="miniOk"><i class="fa-solid fa-circle-check"></i> رقم صالح — جاهز للمتابعة.</div>
        </div>

        <button class="btn" id="submitBtn" type="submit">
          <i class="fa-solid fa-play"></i> بدء الاختبار
        </button>
      </form>

      <!-- زر "الرئيسية" -->
      <div class="actions">
        <a class="home-btn" href="{{ route('landing') }}" aria-label="العودة للرئيسية">
          <i class="fa-solid fa-house"></i>
        </a>
      </div>
    </div>
  </section>

  <script>
    // Navbar scroll effect
    window.addEventListener('scroll', () => {
      const navbar = document.getElementById('navbar');
      if (window.scrollY > 50) navbar.classList.add('scrolled'); else navbar.classList.remove('scrolled');
    });

    // Dark mode toggle with persistence
    (function(){
      const darkToggle = document.getElementById('darkToggle');
      const body = document.body;

      if (!localStorage.getItem('dark-mode') && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        body.classList.add('dark-mode');
        darkToggle.classList.replace('fa-moon','fa-sun');
      }
      try {
        if (localStorage.getItem('dark-mode') === 'enabled') {
          body.classList.add('dark-mode');
          darkToggle.classList.replace('fa-moon','fa-sun');
        }
      } catch(e){}

      if (body.classList.contains('dark-mode')) darkToggle.classList.replace('fa-moon','fa-sun');

      darkToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        const enabled = body.classList.contains('dark-mode');
        try { localStorage.setItem('dark-mode', enabled ? 'enabled' : 'disabled'); } catch(e){}
        if (enabled) darkToggle.classList.replace('fa-moon','fa-sun'); else darkToggle.classList.replace('fa-sun','fa-moon');
      });
    })();

    // Helpers
    const form  = document.getElementById('postForm');
    const btn   = document.getElementById('submitBtn');
    const input = document.getElementById('whatsapp_number');
    const miniErr = document.getElementById('miniErr');
    const miniOk  = document.getElementById('miniOk');

    const isValidPhone = (val) => /^[0-9]{7,15}$/.test(val);

    // تنسيق/تنظيف أثناء الكتابة + رسائل فورية
    input.addEventListener('input', () => {
      // أرقام فقط
      input.value = (input.value || '').replace(/\D+/g,'');
      const ok = isValidPhone(input.value);
      input.classList.toggle('error', !ok && input.value.length > 0);
      miniErr.style.display = (!ok && input.value.length > 0) ? 'block' : 'none';
      miniOk.style.display  = ok ? 'block' : 'none';
    });

    // تحقق نهائي قبل الإرسال
    form.addEventListener('submit', (e)=>{
      input.value = (input.value || '').replace(/\D+/g,'');
      if(!isValidPhone(input.value)){
        e.preventDefault();
        input.classList.add('error');
        miniOk.style.display = 'none';
        miniErr.style.display = 'block';
        input.focus();
        return;
      }
      btn.setAttribute('disabled','disabled');
      btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> جاري التحقق...';
    });

    // إرسال بالـ Enter من داخل البطاقة
    document.getElementById('card').addEventListener('keydown', (e)=>{
      if(e.key === 'Enter' && !e.target.matches('button')) form.requestSubmit();
    });
  </script>
</body>
</html>
