<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>نتيجتك النهائية - مشروع مسار</title>

  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Cairo',sans-serif;overflow-x:hidden;color:#2c3e50}

    /* ===== خلفية متحركة (مطابقة للصفحة الرئيسية) ===== */
    .hero-section{
      min-height:100vh;
      background:linear-gradient(135deg,#f39c12 0%,#e67e22 50%,#d35400 100%);
      position:relative; display:flex; align-items:center; justify-content:center; overflow:hidden;
      padding-top:90px;          /* إزاحة للنافبار */
      padding-bottom:64px;       /* المسافة أسفل الهيرو بدلاً من margin للفوتر */
    }
    .hero-section::before{
      content:''; position:absolute; inset:0;
      background-image:
        radial-gradient(circle at 20% 80%, rgba(255,255,255,.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255,255,255,.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(255,255,255,.05) 0%, transparent 50%);
      animation:float 20s ease-in-out infinite;
    }
    @keyframes float{0%,100%{transform:translateY(0) rotate(0)}50%{transform:translateY(-20px) rotate(180deg)}}

    .particles{position:absolute; inset:0; overflow:hidden}
    .particle{position:absolute; background:rgba(255,255,255,.3); border-radius:50%; animation:particle-float 15s infinite linear}
    .particle:nth-child(1){width:4px;height:4px;left:10%;animation-delay:0s}
    .particle:nth-child(2){width:6px;height:6px;left:20%;animation-delay:2s}
    .particle:nth-child(3){width:3px;height:3px;left:30%;animation-delay:4s}
    .particle:nth-child(4){width:5px;height:5px;left:40%;animation-delay:6s}
    .particle:nth-child(5){width:4px;height:4px;left:50%;animation-delay:8s}
    .particle:nth-child(6){width:7px;height:7px;left:60%;animation-delay:10s}
    .particle:nth-child(7){width:3px;height:3px;left:70%;animation-delay:12s}
    .particle:nth-child(8){width:5px;height:5px;left:80%;animation-delay:14s}
    .particle:nth-child(9){width:4px;height:4px;left:90%;animation-delay:16s}
    @keyframes particle-float{0%{transform:translateY(100vh) rotate(0); opacity:0}10%{opacity:1}90%{opacity:1}100%{transform:translateY(-100px) rotate(360deg); opacity:0}}

    /* ===== شريط التنقل ===== */
    .navbar { position: fixed; top: 0; left: 0; right: 0; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 15px 0; z-index: 1000; transition: all .3s ease; box-shadow: 0 2px 20px rgba(0,0,0,0.1) }
    .navbar.scrolled { background: rgba(255,255,255,0.98); box-shadow: 0 2px 30px rgba(0,0,0,0.15) }
    .nav-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 20px }
    .logo { font-size: 1.8rem; font-weight: 800; color: #e67e22; text-decoration: none; display: flex; align-items: center; gap: 10px }
    .logo i { font-size: 2rem }
    .nav-links { display:flex; list-style:none; gap:30px; align-items:center }
    .nav-links a { text-decoration:none; color:#2c3e50; font-weight:600; transition:all .3s ease; position:relative }
    .nav-links a:hover{ color:#e67e22; transform:translateY(-2px) }
    .nav-links a::after{ content:''; position:absolute; bottom:-5px; left:0; width:0; height:2px; background:#e67e22; transition:width .3s ease }
    .nav-links a:hover::after{ width:100% }
    .dark-toggle { cursor: pointer; font-size: 1.3rem; margin-right: 15px; color: #2c3e50; transition: color 0.3s ease }
    .dark-toggle:hover { color: #f39c12 }

    /* ===== بطاقة النتائج ===== */
    .shell{
      position:relative; z-index:2; width:min(1100px,92vw);
      background:rgba(255,255,255,.20); border:1px solid rgba(255,255,255,.35); backdrop-filter:blur(12px);
      border-radius:24px; padding:26px 22px; box-shadow:0 20px 60px rgba(0,0,0,.18); color:#fff;
      transform: translateY(18px); opacity: 0; transition: transform .7s ease, opacity .7s ease;
    }
    .shell.visible{ transform: translateY(0); opacity: 1 }
    .head{display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:16px}
    .title{font-size:1.6rem; font-weight:900; display:flex; align-items:center; gap:10px}
    .sub{opacity:.95; font-weight:600}

    .top-grid{ display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-top:12px }
    .top-card{ background:rgba(255,255,255,.14); border:1px solid rgba(255,255,255,.28); border-radius:18px; padding:18px; display:flex; flex-direction:column; gap:10px; transition:transform .12s ease, box-shadow .25s ease, border-color .25s ease }
    .top-card:hover{transform:translateY(-3px); box-shadow:0 18px 40px rgba(0,0,0,.28)}
    .top-head{display:flex; align-items:center; justify-content:space-between; gap:10px}
    .top-name{font-weight:900; font-size:1.05rem}
    .badge{display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; font-weight:900; font-size:.95rem; background:rgba(0,0,0,.22)}
    .bar{height:10px; background:rgba(255,255,255,.18); border-radius:999px; overflow:hidden}
    .bar>span{display:block; height:100%; width:0; background:linear-gradient(90deg,#fff,#ffe0b2); transition:width .8s ease .1s}

    .top-card.best{border-color:#ffd54f; box-shadow:0 14px 38px rgba(255,213,79,.35)}
    .top-card.best .badge{background:linear-gradient(135deg,#ffd54f,#ffb300); color:#222}
    .top-card.best .bar>span{background:linear-gradient(90deg,#ffd54f,#ffb300)}

    .others{margin-top:18px; background:rgba(0,0,0,.12); border:1px solid rgba(255,255,255,.18); border-radius:18px; padding:16px}
    .others-title{display:flex; align-items:center; gap:8px; font-weight:900; margin-bottom:10px}
    .pill{display:flex; align-items:center; gap:10px; background:rgba(255,255,255,.14); border:1px solid rgba(255,255,255,.22); border-radius:14px; padding:10px 12px; margin:8px 0}
    .pill-name{flex:0 0 220px; font-weight:700}
    .track{flex:1; height:8px; background:rgba(255,255,255,.18); border-radius:999px; overflow:hidden}
    .track>span{display:block; height:100%; width:0; background:linear-gradient(90deg,#ffe0b2,#fff3e0); transition:width .7s ease}
    .pill .badge{background:rgba(0,0,0,.18)}

    /* ===== زر أسفل البطاقة (غير عائم) ===== */
    .actions{ display:flex; justify-content:center; margin-top:18px }
    .btn{padding:12px 18px; border:none; border-radius:50px; font-weight:800; cursor:pointer; display:inline-flex; align-items:center; gap:8px; text-decoration:none}
    .btn-primary{background:#fff; color:#e67e22}
    .btn:hover{transform:translateY(-2px)}

    /* ===== Footer (بدون margin-top لتفادي الشريط الأبيض) ===== */
    .footer { background:#2c3e50; color:#ecf0f1; padding:60px 0 30px }
    .footer .container { max-width:1200px; margin:0 auto; padding:0 20px }
    .footer-grid { display:grid; grid-template-columns: repeat(2,1fr); gap:30px }
    .footer h3 { color:#f39c12; font-weight:800; margin-bottom:14px; font-size:1.2rem }
    .footer p { color:#cfd8dc; line-height:1.8 }
    .footer a { color:#cfd8dc; text-decoration:none }
    .footer a:hover { color:#ffffff }
    .footer .socials { display:flex; gap:14px; margin-top:14px }
    .footer .socials a { width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; background:#34495e; transition:.2s }
    .footer .socials a:hover { transform:translateY(-2px) }
    .footer .list { list-style:none; padding:0; margin:0 }
    .footer .list li { margin:8px 0 }
    .footer .contact li { display:flex; align-items:center; gap:8px }
    .footer-bottom { text-align:center; padding-top:30px; border-top:1px solid #34495e; color:#95a5a6; margin-top:30px }

    /* الوضع الليلي */
    body.dark-mode { color:#eaeaea }
    body.dark-mode .navbar { background: rgba(18,18,18,0.95); box-shadow: 0 2px 20px rgba(0,0,0,0.4) }
    body.dark-mode .logo { color:#f39c12 }
    body.dark-mode .nav-links a { color:#eaeaea }
    body.dark-mode .nav-links a::after { background:#f39c12 }
    body.dark-mode .hero-section { background: linear-gradient(135deg,#1f1f1f 0%, #171717 50%, #101010 100%) }
    body.dark-mode .hero-section::before {
      background-image:
        radial-gradient(circle at 20% 80%, rgba(255,255,255,0.06) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255,255,255,0.05) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(255,255,255,0.03) 0%, transparent 50%);
    }
    body.dark-mode .shell { background: rgba(18,18,18,.42); border-color: rgba(255,255,255,.22) }
    body.dark-mode .footer { background:#0f0f0f }
    body.dark-mode .footer-bottom { border-top:1px solid #222; color:#9e9e9e }

    /* Responsive */
    @media (max-width: 992px){ .top-grid{ grid-template-columns:1fr } .pill-name{ flex-basis:160px } .nav-links{ display:none } }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar" id="navbar">
    <div class="nav-container">
      <a href="{{ route('landing') }}" class="logo">
        <i class="fas fa-brain"></i>
        مشروع مسار
      </a>
      <ul class="nav-links">
        
        <li><a href="#contact">اتصل بنا</a></li>
      </ul>
      <i class="fas fa-moon dark-toggle" id="darkToggle" aria-label="تبديل الوضع"></i>
    </div>
  </nav>

  <!-- واجهة النتائج -->
  <section class="hero-section" id="home">
    <div class="particles" aria-hidden="true">
      <div class="particle"></div><div class="particle"></div><div class="particle"></div>
      <div class="particle"></div><div class="particle"></div><div class="particle"></div>
      <div class="particle"></div><div class="particle"></div><div class="particle"></div>
    </div>

    @php
      $scores = $postScores ?: $preScores;
      $rawMax = (isset($maxPerType) && $maxPerType > 0) ? (float)$maxPerType : 100;
      $ordered = [];
      foreach(($scores ?? []) as $typeId => $val){
        $percent = $rawMax > 0 ? round(((float)$val / $rawMax) * 100) : 0;
        if ($percent < 0) $percent = 0; if ($percent > 100) $percent = 100;
        $ordered[] = [ 'id'=>$typeId, 'name'=>$intelligenceTypes[$typeId]->name ?? ('ID '.$typeId), 'percent'=>$percent ];
      }
      usort($ordered, fn($a,$b)=> $b['percent'] <=> $a['percent']);
      $top = array_slice($ordered, 0, 3);
      $bestId = $top[0]['id'] ?? null;
      $rest = array_slice($ordered, 3);
      $studentName = $student->full_name ?? 'طالبنا العزيز';
    @endphp

    <div class="shell" id="resultsCard">
      <div class="head">
        <div>
          <div class="title"><i class="fa-solid fa-chart-simple"></i> نتيجتك النهائية</div>
          <div class="sub">مرحبًا {{ $studentName }} — هذه نسب ذكاءاتك (من 100%)</div>
        </div>
        @if($postScores)
          <span class="badge"><i class="fa-solid fa-rotate"></i> الاختبار البَعدي</span>
        @else
          <span class="badge"><i class="fa-solid fa-list-check"></i> الاختبار القبلي</span>
        @endif
      </div>

      <!-- أعلى ثلاثة -->
      <div class="top-grid">
        @foreach($top as $t)
          <div class="top-card {{ $t['id']===$bestId ? 'best' : '' }}">
            <div class="top-head">
              <div class="top-name">
                @if($t['id']===$bestId)
                  <i class="fa-solid fa-crown" style="color:#ffecb3; margin-left:6px"></i>
                @endif
                {{ $t['name'] }}
              </div>
              <span class="badge"><i class="fa-solid fa-percent"></i> {{ $t['percent'] }}%</span>
            </div>
            <div class="bar" aria-hidden="true"><span data-width="{{ $t['percent'] }}%"></span></div>
          </div>
        @endforeach
      </div>

      <!-- باقي الذكاءات -->
      @if(count($rest))
        <div class="others" style="margin-top:18px">
          <div class="others-title"><i class="fa-solid fa-layer-group"></i> باقي الذكاءات</div>
          @foreach($rest as $r)
            <div class="pill">
              <div class="pill-name"><i class="fa-regular fa-circle" style="opacity:.8";></i> {{ $r['name'] }}</div>
              <div class="track" aria-hidden="true"><span data-width="{{ $r['percent'] }}%"></span></div>
              <span class="badge"><i class="fa-solid fa-percent"></i> {{ $r['percent'] }}%</span>
            </div>
          @endforeach
        </div>
      @endif

      <!-- زر العودة للرئيسية -->
      <div class="actions">
        <a href="{{ route('landing') }}" class="btn btn-primary"><i class="fa-solid fa-house"></i> الرئيسية</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer" id="contact">
    <div class="container">
      <div class="footer-grid">
        <!-- مشروع مسار -->
        <div>
          <h3>مشروع مسار</h3>
          <p>نساعدك في اكتشاف أنواع ذكائك المختلفة واتخاذ القرارات الأكاديمية والمهنية المناسبة لك.</p>
          <div class="socials" aria-label="روابط تواصل اجتماعي">
            <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          </div>
        </div>

        <!-- تواصل معنا -->
        <div>
          <h3>تواصل معنا</h3>
          <ul class="list contact">
            <li><i class="fa-regular fa-envelope"></i><a href="mailto:info@masar.com">info@masar.com</a></li>
            <li><i class="fa-solid fa-phone"></i><a href="tel:+967774198483">+967774198483</a></li>
            <li><i class="fa-solid fa-location-dot"></i>مأرب، اليمن</li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">&copy; {{ date('Y') }} مشروع مسار جميع الحقوق محفوظة.</div>
    </div>
  </footer>

  <script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
      const navbar = document.getElementById('navbar');
      if (window.scrollY > 50) navbar.classList.add('scrolled'); else navbar.classList.remove('scrolled');
    });

    // Dark mode
    const darkToggle = document.getElementById('darkToggle');
    const body = document.body;
    if (!localStorage.getItem('dark-mode') && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      body.classList.add('dark-mode');
    }
    try { if (localStorage.getItem('dark-mode') === 'enabled') { body.classList.add('dark-mode'); darkToggle.classList.replace('fa-moon','fa-sun'); } } catch(e){}
    if (body.classList.contains('dark-mode')) { darkToggle.classList.replace('fa-moon','fa-sun'); }
    darkToggle.addEventListener('click', () => {
      body.classList.toggle('dark-mode');
      const enabled = body.classList.contains('dark-mode');
      try { localStorage.setItem('dark-mode', enabled ? 'enabled' : 'disabled'); } catch(e){}
      if (enabled) darkToggle.classList.replace('fa-moon','fa-sun'); else darkToggle.classList.replace('fa-sun','fa-moon');
    });

    // إظهار البطاقة + تحريك الأشرطة
    window.addEventListener('load', ()=>{
      const card = document.getElementById('resultsCard');
      if(card){ card.classList.add('visible'); }
      document.querySelectorAll('.bar > span, .track > span').forEach(el=>{
        const w = el.getAttribute('data-width') || '0%';
        requestAnimationFrame(()=> el.style.width = w);
      });
    });
  </script>
</body>
</html>
