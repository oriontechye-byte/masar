<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>مشروع مسار - اكتشف ذكاءاتك المتعددة</title>

  <!-- SEO & Social -->
  <meta name="description" content="اختبار الذكاءات المتعددة مع توصيات أكاديمية ومهنية مخصّصة. اكتشف ميولك واتخذ قرارك الأكاديمي والمهني بثقة.">
  <meta property="og:title" content="مشروع مسار - اكتشف ذكاءاتك المتعددة">
  <meta property="og:description" content="اختبر ذكاءاتك واحصل على تحليل وتوصيات تناسبك.">
  <meta property="og:type" content="website">
  <meta name="theme-color" content="#e67e22">
  <meta name="color-scheme" content="light dark">

  <!-- Favicon -->
  <link rel="icon" href="/favicon.ico">

  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700;800;900&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

  @verbatim
  <style>
    /* ================= Base & Tokens ================= */
    *{margin:0;padding:0;box-sizing:border-box}
    html{scroll-behavior:smooth}
    body{font-family:'Cairo',sans-serif;overflow-x:hidden;background:var(--surface);color:var(--text)}
    img{max-width:100%;height:auto}

    :root{
      --brand:#e67e22; /* برتقالي */
      --brand-2:#f39c12; /* ذهبي */
      --radius:20px;
      --shadow-sm:0 6px 20px rgba(0,0,0,.06);
      --shadow-md:0 10px 40px rgba(0,0,0,.10);
      --shadow-lg:0 20px 60px rgba(0,0,0,.15);

      /* Light theme */
      --surface:#f8f9fa;
      --surface-2:#ffffff;
      --text:#2c3e50;
      --text-muted:#7f8c8d;
      --border:#f1f1f1;
      --nav-text:#ecf0f1;
      --footer-bg:#2c3e50;
      --footer-text:#bdc3c7;
      --chip-bg:#fff;

      /* Hero gradient (light) */
      --hero-a:#f39c12;
      --hero-b:#e67e22;
      --hero-c:#d35400;
    }
    /* Dark overrides via [data-theme="dark"] on <html> */
    [data-theme="dark"]{
      --surface:#121212;
      --surface-2:#1f1f1f;
      --text:#eaeaea;
      --text-muted:#bdbdbd;
      --border:#2a2a2a;
      --nav-text:#f1f1f1;
      --footer-bg:#0f0f0f;
      --footer-text:#9e9e9e;
      --chip-bg:#1f1f1f;

      /* Hero gradient (dark) */
      --hero-a:#1b1b1b;
      --hero-b:#131313;
      --hero-c:#0b0b0b;
    }

    /* Skip link */
    .skip-link{position:absolute;top:-100px;right:1rem;background:#111;color:#fff;padding:.6rem 1rem;border-radius:.5rem;z-index:10000;text-decoration:none}
    .skip-link:focus{top:1rem}
    :focus-visible{outline:3px solid var(--brand-2);outline-offset:2px;border-radius:6px}

    /* ================= Navbar ================= */
    .navbar{
      position:fixed;inset:0 auto auto 0;right:0;z-index:1000;
      transition:all .3s ease;
      background:transparent;           /* شفاف افتراضياً */
      border-bottom:1px solid transparent;
      backdrop-filter:saturate(120%) blur(8px);
    }
    .navbar.scrolled{
      background:var(--footer-bg);      /* عند السحب يصبح مثل لون الفوتر */
      border-bottom-color:rgba(255,255,255,.08);
      box-shadow:0 2px 20px rgba(0,0,0,.2)
    }
    .nav-container{max-width:1200px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;padding:0 20px;gap:16px;height:92px}
    .nav-left,.nav-center,.nav-right{display:flex;align-items:center;gap:18px}
    .nav-center{flex-grow:1;justify-content:center}
    .logo{font-size:1.8rem;font-weight:800;color:#fffbf8ff;text-decoration:none;display:flex;align-items:center;gap:10px;white-space:nowrap}
    .logo i{font-size:2rem}
    .sponsor-logo-top{height:77px;width:auto;object-fit:contain;display:block;margin:0}

    .nav-links{display:flex;list-style:none;gap:30px;align-items:center}
    .nav-links a{text-decoration:none;color:var(--nav-text);font-weight:700;letter-spacing:.2px;transition:all .3s ease;position:relative}
    .nav-links a:hover{color:var(--brand-2);transform:translateY(-2px)}
    .nav-links a::after{content:'';position:absolute;bottom:-5px;left:0;width:0;height:2px;background:var(--brand-2);transition:width .3s ease}
    .nav-links a:hover::after{width:100%}
    .nav-toggle{display:none;border:0;background:transparent;color:var(--nav-text);font-size:1.4rem;cursor:pointer}

    /* ===== Theme toggle (Sun/Moon) ===== */
    .theme-toggle{border:0;background:transparent;cursor:pointer;padding:6px;border-radius:999px;line-height:0}
    .sun-and-moon> :is(.moon,.sun,.sun-beams){transform-origin:center}
    .sun-and-moon> :is(.moon,.sun){fill:var(--icon-fill)}
    .sun-and-moon>.sun-beams{stroke:var(--icon-fill);stroke-width:2px}
    .theme-toggle:is(:hover,:focus-visible) .sun-and-moon> :is(.moon,.sun){fill:var(--icon-fill-hover)}
    .theme-toggle:is(:hover,:focus-visible) .sun-and-moon>.sun-beams{stroke:var(--icon-fill-hover)}
    :root{--icon-fill:#ffffff;--icon-fill-hover:#ffe7c2}
    [data-theme="dark"]{--icon-fill:#f1f1f1;--icon-fill-hover:#ffd596}
    [data-theme="dark"] .sun-and-moon>.sun{transform:scale(1.75)}
    [data-theme="dark"] .sun-and-moon>.sun-beams{opacity:0}
    [data-theme="dark"] .sun-and-moon>.moon>circle{transform:translateX(-7px)}
    @supports (cx:1){[data-theme="dark"] .sun-and-moon>.moon>circle{cx:17;transform:translateX(0)}}
    @media (prefers-reduced-motion:no-preference){
      .sun-and-moon>.sun{transition:transform .5s}
      .sun-and-moon>.sun-beams{transition:transform .5s,opacity .5s}
      .sun-and-moon .moon>circle{transition:transform .25s}
      @supports (cx:1){.sun-and-moon .moon>circle{transition:cx .25s}}
      [data-theme="dark"] .sun-and-moon>.sun{transition-duration:.25s;transform:scale(1.75)}
      [data-theme="dark"] .sun-and-moon>.sun-beams{transition-duration:.15s;transform:rotateZ(-25deg)}
      [data-theme="dark"] .sun-and-moon>.moon>circle{transition-duration:.5s;transition-delay:.25s}
    }

    /* ================= Hero ================= */
    .hero-section{
      min-height:100vh;
      background:linear-gradient(135deg,var(--hero-a) 0%,var(--hero-b) 50%,var(--hero-c) 100%);
      position:relative;display:flex;align-items:center;justify-content:center;overflow:hidden;padding-top:92px
    }
    .hero-section::before{content:'';position:absolute;inset:0;background-image:
      radial-gradient(circle at 20% 80%,rgba(255,255,255,.1) 0%,transparent 50%),
      radial-gradient(circle at 80% 20%,rgba(255,255,255,.1) 0%,transparent 50%),
      radial-gradient(circle at 40% 40%,rgba(255,255,255,.05) 0%,transparent 50%);animation:float 20s ease-in-out infinite}
    [data-theme="dark"] .hero-section::before{background-image:
      radial-gradient(circle at 20% 80%,rgba(255,255,255,.06) 0%,transparent 50%),
      radial-gradient(circle at 80% 20%,rgba(255,255,255,.05) 0%,transparent 50%),
      radial-gradient(circle at 40% 40%,rgba(255,255,255,.03) 0%,transparent 50%)}
    @keyframes float{0%,100%{transform:translateY(0) rotate(0)}50%{transform:translateY(-20px) rotate(180deg)}}
    .particles{position:absolute;inset:0;overflow:hidden}
    .particle{position:absolute;background:rgba(255,255,255,.3);border-radius:50%;animation:particle-float 15s infinite linear}
    .particle:nth-child(1){width:4px;height:4px;left:10%;animation-delay:0s}
    .particle:nth-child(2){width:6px;height:6px;left:20%;animation-delay:2s}
    .particle:nth-child(3){width:3px;height:3px;left:30%;animation-delay:4s}
    .particle:nth-child(4){width:5px;height:5px;left:40%;animation-delay:6s}
    .particle:nth-child(5){width:4px;height:4px;left:50%;animation-delay:8s}
    .particle:nth-child(6){width:7px;height:7px;left:60%;animation-delay:10s}
    .particle:nth-child(7){width:3px;height:3px;left:70%;animation-delay:12s}
    .particle:nth-child(8){width:5px;height:5px;left:80%;animation-delay:14s}
    .particle:nth-child(9){width:4px;height:4px;left:90%;animation-delay:16s}
    @keyframes particle-float{0%{transform:translateY(100vh) rotate(0);opacity:0}10%,90%{opacity:1}100%{transform:translateY(-100px) rotate(360deg);opacity:0}}

    .hero-content{text-align:center;color:#fff;z-index:2;position:relative;max-width:900px;padding:0 20px}
    .hero-title{font-size:4rem;font-weight:800;margin-bottom:20px;text-shadow:2px 2px 4px rgba(0,0,0,.3);animation:slideInUp 1s ease-out}
    .hero-subtitle{font-size:1.5rem;margin-bottom:30px;opacity:.95;font-weight:600;animation:slideInUp 1s ease-out .2s both}
    .hero-description{font-size:1.12rem;margin-bottom:40px;opacity:.95;line-height:1.9;animation:slideInUp 1s ease-out .4s both}
    @keyframes slideInUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}

    .cta-buttons{display:flex;gap:16px;justify-content:center;flex-wrap:wrap;animation:slideInUp 1s ease-out .6s both}
    .btn{padding:14px 28px;border:none;border-radius:999px;font-size:1.05rem;font-weight:800;cursor:pointer;transition:all .25s ease;text-decoration:none;display:inline-flex;align-items:center;gap:10px;font-family:'Cairo',sans-serif;position:relative;overflow:hidden}
    .btn::before{content:'';position:absolute;top:0;left:-110%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.25),transparent);transition:left .55s}
    .btn:hover::before{left:110%}
    .btn-primary{background:rgba(255,255,255,.2);color:#fff;border:2px solid rgba(255,255,255,.3);backdrop-filter:blur(8px)}
    .btn-primary:hover{background:rgba(255,255,255,.28);transform:translateY(-2px);box-shadow:0 10px 30px rgba(0,0,0,.2)}
    .btn-secondary{background:#fff;color:var(--brand);border:2px solid #fff}
    .btn-secondary:hover{background:var(--brand);color:#fff;transform:translateY(-2px);box-shadow:0 10px 30px rgba(0,0,0,.2)}

    /* Trust badges */
    .trust{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:18px}
    .trust-badge{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:999px;background:var(--chip-bg);border:1px solid var(--border);box-shadow:var(--shadow-sm);font-weight:800;color:var(--text);font-size:.95rem}

    /* ================= Sections ================= */
    .section{padding:90px 0}
    .container{max-width:1200px;margin:0 auto;padding:0 20px}
    .section-title{text-align:center;font-size:3rem;font-weight:800;color:var(--text);margin-bottom:14px;letter-spacing:.2px}
    .section-subtitle{text-align:center;font-size:1.15rem;color:var(--text-muted);margin-bottom:54px;max-width:680px;margin-inline:auto;line-height:1.9}

    /* Features */
    .features-section{padding:100px 0;background:var(--surface-2)}
    .features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:26px;margin-top:40px}
    .feature-card{background:var(--surface-2);padding:34px 26px;border-radius:var(--radius);text-align:center;box-shadow:var(--shadow-md);transition:all .25s ease;position:relative;overflow:hidden;border:1px solid var(--border)}
    .feature-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--brand),var(--brand-2));transform:scaleX(0);transition:transform .3s ease;transform-origin:right}
    .feature-card:hover::before{transform:scaleX(1);transform-origin:left}
    .feature-card:hover{transform:translateY(-8px);box-shadow:var(--shadow-lg)}
    .feature-icon{font-size:3rem;color:var(--brand);margin-bottom:16px;display:block}
    .feature-title{font-size:1.45rem;font-weight:800;color:var(--text);margin-bottom:10px}
    .feature-description{color:var(--text-muted);line-height:1.9}

    /* ================= NEW: Acknowledgments grid (4 boxes) ================= */
    .ack-section{padding:90px 0;background:var(--surface)}
    .ack-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:22px;margin-top:28px}
    .ack-card{background:var(--surface-2);border-radius:18px;box-shadow:var(--shadow-md);overflow:hidden;transition:transform .25s ease,box-shadow .25s ease,border .25s ease;border:1px solid var(--border);padding:22px}
    .ack-card:hover{transform:translateY(-6px);box-shadow:0 16px 50px rgba(0,0,0,.12)}
    .ack-head{display:flex;align-items:center;gap:12px;margin-bottom:10px}
    .ack-icon{width:52px;height:52px;border-radius:14px;background:#fff;display:grid;place-items:center;box-shadow:inset 0 0 0 2px #f3f3f3}
    [data-theme="dark"] .ack-icon{background:#0f0f0f;box-shadow:inset 0 0 0 2px #1f1f1f}
    .ack-title{font-size:1.2rem;font-weight:900;color:var(--text)}
    .ack-body{color:var(--text-muted);line-height:1.9}

    /* Testimonials */
    .testimonials{padding:90px 0;background:var(--surface-2)}
    .t-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:22px;margin-top:30px}
    .t-card{background:var(--surface-2);border-radius:16px;box-shadow:var(--shadow-sm);padding:20px;border:1px solid var(--border)}
    .t-head{display:flex;align-items:center;gap:12px;margin-bottom:10px;color:var(--text)}
    .t-avatar{width:44px;height:44px;border-radius:50%;background:#eee;display:grid;place-items:center;font-weight:900;color:var(--brand)}
    [data-theme="dark"] .t-avatar{background:#2a2a2a}
    .t-card p{line-height:1.9;color:var(--text-muted)}

    /* Stats */
    .stats-section{padding:80px 0;background:linear-gradient(135deg,#2c3e50 0%,#34495e 100%);color:#fff}
    [data-theme="dark"] .stats-section{background:linear-gradient(135deg,#0f0f0f,#1a1a1a)}
    .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:32px;text-align:center}
    .stat-item{padding:22px}
    .stat-number{font-size:3rem;font-weight:900;color:var(--brand-2);margin-bottom:10px;display:block}
    .stat-label{font-size:1.1rem;opacity:.92}

    /* FAQ */
    .faq{padding:90px 0;background:var(--surface)}
    .faq-wrap{max-width:900px;margin-inline:auto}
    .faq-item{background:var(--surface-2);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow-sm);margin-bottom:12px;overflow:hidden}
    .faq-q{display:flex;align-items:center;justify-content:space-between;padding:16px 18px;cursor:pointer;font-weight:900;color:var(--text)}
    .faq-q i{transition:transform .25s ease}
    .faq-item[aria-expanded="true"] .faq-q i{transform:rotate(180deg)}
    .faq-a{max-height:0;opacity:0;overflow:hidden;transition:all .3s ease;padding:0 18px}
    .faq-item[aria-expanded="true"] .faq-a{max-height:300px;opacity:1;padding:0 18px 16px}
    .faq-a p{line-height:1.9;color:var(--text-muted)}

    /* Footer */
    .footer{background:var(--footer-bg);color:#fff;padding:60px 0 30px}
    .footer-nav-tags{text-align:center;margin-bottom:40px}
    .footer-nav-tags h3{font-size:1.4rem;font-weight:800;margin-bottom:20px;color:var(--brand-2)}
    .nav-tags{display:flex;justify-content:center;gap:12px;flex-wrap:wrap}
    .nav-tag{background:#34495e;color:#fff;padding:8px 16px;border-radius:25px;text-decoration:none;font-weight:800;font-size:.95rem;transition:all .25s ease;border:2px solid transparent}
    .nav-tag:hover{background:var(--brand-2);color:#2c3e50;transform:translateY(-2px);box-shadow:0 5px 15px rgba(243,156,18,.3)}
    .footer-content{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:34px;margin-bottom:40px}
    .footer-section h3{font-size:1.25rem;font-weight:900;margin-bottom:16px;color:var(--brand-2)}
    .footer-section p,.footer-section a,.footer-section li{color:var(--footer-text);line-height:1.9;text-decoration:none;transition:color .25s ease;list-style:none}
    .footer-section ul{padding:0}
    .footer-section li{margin-bottom:10px;display:flex;align-items:center}
    .footer-section li i{margin-left:10px;color:var(--brand-2);width:20px;text-align:center}
    .footer-section a:hover{color:var(--brand-2)}
    .social-links{display:flex;gap:12px;margin-top:14px}
    .social-link{width:40px;height:40px;background:#34495e;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;transition:all .25s ease}
    .social-link:hover{background:var(--brand-2);transform:translateY(-3px)}
    .footer-bottom{text-align:center;padding-top:30px;border-top:1px solid #34495e;color:var(--footer-text)}
    .footer-bottom .note{margin-top:8px;display:block}

    /* Floating WhatsApp & Mobile CTA */
    .wa-float{position:fixed;bottom:90px;left:16px;width:56px;height:56px;border-radius:50%;background:#25d366;display:flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;box-shadow:0 8px 24px rgba(0,0,0,.2);z-index:999}
    .mobile-cta{position:fixed;bottom:0;right:0;left:0;background:#ffffffee;border-top:1px solid #eee;backdrop-filter:blur(8px);padding:10px 12px;display:none;z-index:998}
    .mobile-cta .row{max-width:1200px;margin:0 auto;display:flex;gap:10px}
    .mobile-cta .btn{flex:1;justify-content:center}

    /* Animations on scroll */
    .fade-in{opacity:0;transform:translateY(30px);transition:all .6s ease}
    .fade-in.visible{opacity:1;transform:translateY(0)}

    /* Responsive */
    @media (max-width:992px){
      .nav-links{display:none}
      .nav-toggle{display:inline-flex}
      .nav-center{order:2;flex-grow:1;justify-content:flex-start}
      .nav-left{order:1}.nav-right{order:3}
      .mobile-cta{display:block}
    }
    @media (max-width:768px){
      .nav-center{justify-content:center}
      .hero-title{font-size:2.5rem}
      .hero-subtitle{font-size:1.2rem}
      .cta-buttons{flex-direction:column;align-items:center}
      .btn{width:100%;max-width:320px;justify-content:center}
      .section-title{font-size:2rem}
      .features-grid,.ack-grid{grid-template-columns:1fr}
    }

    /* Reduced motion */
    @media (prefers-reduced-motion:reduce){
      *{scroll-behavior:auto!important}
      .hero-section::before,.particle,.fade-in,.btn::before{animation:none!important;transition:none!important}
    }
  </style>
  @endverbatim

  <!-- JSON-LD: Organization & Website -->
  <script type="application/ld+json">
  {
    "@@context":"https://schema.org",
    "@@type":"Organization",
    "name":"مشروع مسار",
    "url":"{{ url('/') }}",
    "logo":"{{ asset('assets/logos/moys-ye.png') }}",
    "sameAs":[]
  }
  </script>
  <script type="application/ld+json">
  {
    "@@context":"https://schema.org",
    "@@type":"WebSite",
    "name":"مشروع مسار",
    "url":"{{ url('/') }}",
    "potentialAction":{
      "@@type":"SearchAction",
      "target":"{{ url('/') }}?q={search_term_string}",
      "query-input":"required name=search_term_string"
    }
  }
  </script>
</head>
<body>
  <a class="skip-link" href="#main">تخطي إلى المحتوى</a>

  <!-- ========== Navbar ========== -->
  <nav class="navbar" id="navbar">
    <div class="nav-container">
      <div class="nav-left">
        <a href="{{ route('landing') }}" class="logo">
          <i class="fas fa-brain" aria-hidden="true"></i> مشروع مسار
        </a>
      </div>

      <div class="nav-center">
        <ul class="nav-links" id="primary-nav">
          <li><a href="#home">الرئيسية</a></li>
          <li><a href="#features">المميزات</a></li>
          <li><a href="#ack">الركائز</a></li>
          <li><a href="#testimonials">الآراء</a></li>
          <li><a href="#faq">الأسئلة الشائعة</a></li>
          <li><a href="#contact">اتصل بنا</a></li>
        </ul>
      </div>

      <div class="nav-right">
        <button class="theme-toggle" id="theme-toggle" title="تبديل الثيم" aria-label="auto" aria-live="polite">
          <svg class="sun-and-moon" aria-hidden="true" width="24" height="24" viewBox="0 0 24 24">
            <mask class="moon" id="moon-mask">
              <rect x="0" y="0" width="100%" height="100%" fill="white" />
              <circle cx="24" cy="10" r="6" fill="black" />
            </mask>
            <circle class="sun" cx="12" cy="12" r="6" mask="url(#moon-mask)" fill="currentColor" />
            <g class="sun-beams" stroke="currentColor">
              <line x1="12" y1="1" x2="12" y2="3" />
              <line x1="12" y1="21" x2="12" y2="23" />
              <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
              <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
              <line x1="1" y1="12" x2="3" y2="12" />
              <line x1="21" y1="12" x2="23" y2="12" />
              <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
              <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
            </g>
          </svg>
        </button>
        <a href="#" title="وزارة الشباب والرياضة اليمنية">
          <img src="{{ asset('assets/logos/moys-ye.png') }}" loading="lazy" alt="شعار وزارة الشباب والرياضة اليمنية" class="sponsor-logo-top">
        </a>
        <button class="nav-toggle" id="navToggle" aria-label="فتح القائمة" aria-controls="primary-nav" aria-expanded="false">
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>
  </nav>

  <!-- ========== Main ========== -->
  <main id="main">
    <!-- Hero -->
    <section class="hero-section" id="home">
      <div class="particles">
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
      </div>

      <div class="hero-content">
        <h1 class="hero-title">اكتشف مستقبلك</h1>
        <p class="hero-subtitle">اختبار الذكاءات المتعددة - فحص الميول التعليمية</p>
        <p class="hero-description">
          اكتشف أنواع ذكائك المختلفة واحصل على توصيات مهنية وأكاديمية مخصصة لك.
          رحلة شخصية لفهم قدراتك وإمكاناتك الحقيقية.
        </p>

        <div class="cta-buttons">
          <a href="{{ route('register') }}" class="btn btn-primary" data-evt="cta_pre">
            <i class="fas fa-play"></i> الاختبار القبلي (قبل الدورة)
          </a>
          <a href="{{ route('post-test.lookup') }}" class="btn btn-secondary" data-evt="cta_post">
            <i class="fas fa-redo-alt"></i> الاختبار البعدي (بعد الدورة)
          </a>
        </div>

        <div class="trust" aria-label="شارات الثقة">
          <span class="trust-badge"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> حماية البيانات</span>
          <span class="trust-badge"><i class="fa-solid fa-certificate" aria-hidden="true"></i> إشراف علمي</span>
          <span class="trust-badge"><i class="fa-solid fa-handshake-angle" aria-hidden="true"></i> راعي رسمي</span>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section class="features-section" id="features">
      <div class="container">
        <h2 class="section-title fade-in">ما هو اختبار الذكاءات المتعددة؟</h2>
        <p class="section-subtitle fade-in">
          نحن نساعدك على اتخاذ واحد من أهم القرارات في حياتك، وهو اختيار مسارك التعليمي والمهني بثقة.
        </p>

        <div class="features-grid">
          <div class="feature-card fade-in">
            <i class="fas fa-brain feature-icon" aria-hidden="true"></i>
            <h3 class="feature-title">8 أنواع ذكاء</h3>
            <p class="feature-description">
              اكتشف أنواع ذكائك الثمانية: اللغوي، المنطقي، المكاني، الحركي، الموسيقي، الاجتماعي، الذاتي، والطبيعي.
            </p>
          </div>

          <div class="feature-card fade-in">
            <i class="fas fa-chart-line feature-icon" aria-hidden="true"></i>
            <h3 class="feature-title">تحليل شامل</h3>
            <p class="feature-description">
              احصل على تحليل مفصل لنتائجك مع مقارنة بين الاختبار القبلي والبعدي لقياس تطورك.
            </p>
          </div>

          <div class="feature-card fade-in">
            <i class="fas fa-graduation-cap feature-icon" aria-hidden="true"></i>
            <h3 class="feature-title">توصيات مهنية</h3>
            <p class="feature-description">
              احصل على توصيات مخصصة للمسارات المهنية والتخصصات الأكاديمية المناسبة لنوع ذكائك.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= NEW: Acknowledgments / الركائز ================= -->
    <section class="ack-section" id="ack">
      <div class="container">
        <h2 class="section-title fade-in">الركائز الداعمة</h2>
        <p class="section-subtitle fade-in">تعريف موجز بالجهات والأشخاص الذين يقفون خلف نجاح هذه الدورة.</p>

        <div class="ack-grid">
          <article class="ack-card fade-in" id="ministry">
            <div class="ack-head">
              <div class="ack-icon"><i class="fas fa-landmark" style="color:#e67e22;font-size:1.6rem" aria-hidden="true"></i></div>
              <h3 class="ack-title">وزارة الشباب والرياضة</h3>
            </div>
            <div class="ack-body">
              <p>
                برعاية معالي وزير الشباب والرياضة <strong>نايف البكري</strong>، تبذل الوزارة جهودًا حثيثة في إصلاح وتطوير هذه الدورة التدريبية المتقدمة
                لبناء جيل واعٍ ومؤهل أكاديميًّا ومهنيًّا.
              </p>
            </div>
          </article>

          <article class="ack-card fade-in" id="coordinator">
            <div class="ack-head">
              <div class="ack-icon"><i class="fas fa-user-tie" style="color:#e67e22;font-size:1.6rem" aria-hidden="true"></i></div>
              <h3 class="ack-title">المنسّق</h3>
            </div>
            <div class="ack-body">
              <p><strong>عبدالله منصور الحسامي</strong></p>
            </div>
          </article>

          <article class="ack-card fade-in" id="trainer">
            <div class="ack-head">
              <div class="ack-icon"><i class="fas fa-chalkboard-teacher" style="color:#e67e22;font-size:1.6rem" aria-hidden="true"></i></div>
              <h3 class="ack-title">مدرب الدورة</h3>
            </div>
            <div class="ack-body">
              <p>
                <strong>صلاح الدين المصنف</strong> — مدرب ملتزم يتميّز بأسلوب تعليمي واضح وعملي، يبسّط المفاهيم ويقرّبها من واقع الطلاب.
              </p>
            </div>
          </article>

          <article class="ack-card fade-in" id="team">
            <div class="ack-head">
              <div class="ack-icon"><i class="fas fa-code" style="color:#e67e22;font-size:1.6rem" aria-hidden="true"></i></div>
              <h3 class="ack-title">فريق التنفيذ: ديفو تك</h3>
            </div>
            <div class="ack-body">
              <p>
                الأعضاء: أحمد القباطي، عمر النهمي، عبدالغني الغانمي.
              </p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials" id="testimonials">
      <div class="container">
        <h2 class="section-title fade-in">ماذا يقول المتدربون؟</h2>
        <p class="section-subtitle fade-in">آراء سريعة عن التجربة والنتائج.</p>
        <div class="t-grid">
          <div class="t-card fade-in">
            <div class="t-head">
              <div class="t-avatar">أ</div><strong>أمينة.س</strong>
            </div>
            <p>الاختبار ساعدني أفهم نقاط قوّتي، واخترت تخصص يناسبني أكثر.</p>
          </div>
          <div class="t-card fade-in">
            <div class="t-head">
              <div class="t-avatar">م</div><strong>محمد.ع</strong>
            </div>
            <p>التحليل واضح والتوصيات مفيدة جداً — خصوصًا مقارنة القبلي والبعدي.</p>
          </div>
          <div class="t-card fade-in">
            <div class="t-head">
              <div class="t-avatar">ر</div><strong>ريم.ك</strong>
            </div>
            <p>واجهة بسيطة وخطوات قليلة — أنصح أي طالب يجربه قبل اختيار التخصص.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats -->
    <section class="stats-section">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-item fade-in"><span class="stat-number" data-count="8">0</span><span class="stat-label">أنواع ذكاء</span></div>
          <div class="stat-item fade-in"><span class="stat-number" data-count="64">0</span><span class="stat-label">سؤال شامل</span></div>
          <div class="stat-item fade-in"><span class="stat-number" data-count="15">0</span><span class="stat-label">دقيقة فقط</span></div>
          <div class="stat-item fade-in"><span class="stat-number" data-count="100">0</span><span class="stat-label">% مجاني</span></div>
        </div>
      </div>
    </section>

    <!-- FAQ -->
    <section class="faq" id="faq">
      <div class="container">
        <h2 class="section-title fade-in">الأسئلة الشائعة</h2>
        <p class="section-subtitle fade-in">كل ما تحتاج معرفته بسرعة.</p>

        <div class="faq-wrap">
          <div class="faq-item" aria-expanded="false">
            <div class="faq-q">هل الاختبار مجاني؟ <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></div>
            <div class="faq-a"><p>نعم، الاختبار مجاني بالكامل ويمكنك إعادته بعد الدورة لمقارنة التحسن.</p></div>
          </div>

          <div class="faq-item" aria-expanded="false">
            <div class="faq-q">كم يستغرق من الوقت؟ <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></div>
            <div class="faq-a"><p>حوالي 15 دقيقة، بواجهة بسيطة وسهلة على الجوال والكمبيوتر.</p></div>
          </div>

          <div class="faq-item" aria-expanded="false">
            <div class="faq-q">ماذا أحصل بعده؟ <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></div>
            <div class="faq-a"><p>تحليل لأنواع ذكائك وتوصيات مهنية وأكاديمية تناسبك، مع مقارنة قبلي/بعدي.</p></div>
          </div>
        </div>
      </div>
    </section>
  </main>

  Footer (merged sponsor note here)
  <footer class="footer" id="contact">
    <div class="container">
      <div class="footer-nav-tags">
        <h3>الانتقال السريع</h3>
        <div class="nav-tags">
          <a href="#home" class="nav-tag">الرئيسية</a>
          <a href="#features" class="nav-tag">المميزات</a>
          <a href="#ministry" class="nav-tag">الوزارة</a>
          <a href="#coordinator" class="nav-tag">المنسّق</a>
          <a href="#trainer" class="nav-tag">المدرب</a>
          <a href="#team" class="nav-tag">ديفو تك</a>
          <a href="#testimonials" class="nav-tag">الآراء</a>
          <a href="#faq" class="nav-tag">الأسئلة الشائعة</a>
          <a href="#contact" class="nav-tag">التواصل</a>
        </div>
      </div>

      <div class="footer-content">
        <div class="footer-section">
          <h3><i class="fas fa-link" aria-hidden="true"></i> وسائل التواصل</h3>
          <ul>
            <li><a href="mailto:info@masar.com"><i class="fas fa-envelope" aria-hidden="true"></i> info@masar.com</a></li>
            <li><a href="tel:+967774198483"><i class="fas fa-phone" aria-hidden="true"></i> +967 774198483</a></li>
            <li><i class="fas fa-map-marker-alt" aria-hidden="true"></i> مأرب، اليمن</li>
          </ul>
          <div class="social-links">
            <a href="#" class="social-link" aria-label="Facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
            <a href="#" class="social-link" aria-label="Twitter"><i class="fab fa-twitter" aria-hidden="true"></i></a>
            <a href="#" class="social-link" aria-label="Instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
          </div>
        </div>

        <div class="footer-section">
          <h3><i class="fas fa-info-circle" aria-hidden="true"></i> عن المشروع</h3>
          <p>منصة عربية تساعد الشباب على اكتشاف أنواع ذكاءاتهم واتخاذ قرارات أكاديمية ومهنية واعية.</p>
        </div>

        <div class="footer-section">
          <h3><i class="fas fa-shield-alt" aria-hidden="true"></i> سياسة الخصوصية</h3>
          <p>نلتزم بحماية بياناتك، وتُستخدم نتائج الاختبار لتحسين التوصيات فقط.</p>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; 2025 مشروع مسار. جميع الحقوق محفوظة.</p>
        <span class="note">برعاية وزارة الشباب والرياضة - الجمهورية اليمنية</span>
      </div>
    </div>
  </footer>

  <!-- Floating WhatsApp -->
  <a class="wa-float" href="https://wa.me/967774198483" target="_blank" rel="noopener" aria-label="تواصل عبر واتساب">
    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
  </a>

  <!-- Mobile CTA bar -->
  <div class="mobile-cta" role="region" aria-label="أزرار سريعة للجوال">
    <div class="row">
      <a href="{{ route('register') }}" class="btn btn-primary" data-evt="cta_pre_bottom"><i class="fas fa-play"></i> ابدأ الاختبار</a>
      <a href="{{ route('post-test.lookup') }}" class="btn btn-secondary" data-evt="cta_post_bottom"><i class="fas fa-redo-alt"></i> الاختبار البعدي</a>
    </div>
  </div>

  <!-- JSON-LD: FAQPage -->
  <script type="application/ld+json">
  {
    "@@context":"https://schema.org",
    "@@type":"FAQPage",
    "mainEntity":[
      {"@@type":"Question","name":"هل الاختبار مجاني؟","acceptedAnswer":{"@@type":"Answer","text":"نعم، مجاني بالكامل ويمكنك إعادة الاختبار بعد الدورة."}},
      {"@@type":"Question","name":"كم يستغرق من الوقت؟","acceptedAnswer":{"@@type":"Answer","text":"حوالي 15 دقيقة بواجهة بسيطة وسهلة."}},
      {"@@type":"Question","name":"ماذا أحصل بعده؟","acceptedAnswer":{"@@type":"Answer","text":"تحليل لأنواع الذكاء وتوصيات مهنية وأكاديمية، مع مقارنة قبلي/بعدي."}}
    ]
  }
  </script>

  <script>
    /* ================= Theme preference (Sun/Moon) ================= */
    const storageKey='theme-preference';
    const theme={ value:getColorPreference() };

    function getColorPreference(){
      if(localStorage.getItem(storageKey)) return localStorage.getItem(storageKey);
      return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    function reflectPreference(){
      document.firstElementChild.setAttribute('data-theme', theme.value);
      document.querySelector('#theme-toggle')?.setAttribute('aria-label', theme.value);
    }
    function setPreference(){ localStorage.setItem(storageKey, theme.value); reflectPreference(); }

    // Set early to avoid FOUC
    reflectPreference();

    const onClick=()=>{ theme.value = theme.value==='light' ? 'dark' : 'light'; setPreference(); };
    window.onload=()=>{
      reflectPreference();
      document.querySelector('#theme-toggle').addEventListener('click', onClick);
    };
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', ({matches:isDark})=>{
      theme.value = isDark ? 'dark' : 'light'; setPreference();
    });

    /* ================= Mobile nav & navbar scroll ================= */
    const navToggle=document.getElementById('navToggle');
    const navbar=document.getElementById('navbar');
    const body=document.body;
    navToggle.addEventListener('click',()=>{
      body.classList.toggle('nav-open');
      const isOpen=body.classList.contains('nav-open');
      navToggle.setAttribute('aria-expanded',isOpen);
      navToggle.querySelector('i').classList.toggle('fa-bars',!isOpen);
      navToggle.querySelector('i').classList.toggle('fa-times',isOpen);
    });
    // شغل تغيير لون النافبار عند السحب (لنفس لون الفوتر)
    const onScroll=()=>{ if(window.scrollY>10){ navbar.classList.add('scrolled') } else { navbar.classList.remove('scrolled') } };
    window.addEventListener('scroll', onScroll); onScroll();

    /* ================= Smooth anchors ================= */
    document.querySelectorAll('a[href^="#"]').forEach(a=>{
      a.addEventListener('click',e=>{
        const href=a.getAttribute('href'); if(!href||href==='#') return;
        e.preventDefault(); const t=document.querySelector(href);
        if(t){ t.scrollIntoView({behavior:'smooth',block:'start'}); }
        body.classList.remove('nav-open'); navToggle.setAttribute('aria-expanded','false');
        navToggle.querySelector('i').classList.add('fa-bars'); navToggle.querySelector('i').classList.remove('fa-times');
      });
    });

    /* ================= Fade-in on scroll ================= */
    const observer=new IntersectionObserver(entries=>{entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add('visible')}})},{threshold:.1,rootMargin:'0px 0px -50px 0px'});
    document.querySelectorAll('.fade-in').forEach(el=>observer.observe(el));

    /* ================= Stats counters ================= */
    function animateCounter(el,target){let c=0;const inc=Math.max(1,target/100);const timer=setInterval(()=>{c+=inc;if(c>=target){c=target;clearInterval(timer)}el.textContent=Math.floor(c)},20)}
    const statsObserver=new IntersectionObserver(entries=>{entries.forEach(entry=>{if(entry.isIntersecting){const counter=entry.target.querySelector('.stat-number');const target=parseInt(counter.getAttribute('data-count'));animateCounter(counter,target);statsObserver.unobserve(entry.target)}})},{threshold:.5});
    document.querySelectorAll('.stat-item').forEach(it=>statsObserver.observe(it));

    /* ================= FAQ accordion ================= */
    document.querySelectorAll('.faq-item').forEach(item=>{
      const q=item.querySelector('.faq-q');
      q.addEventListener('click',()=>{const ex=item.getAttribute('aria-expanded')==='true';
        document.querySelectorAll('.faq-item').forEach(i=>i.setAttribute('aria-expanded','false'));
        item.setAttribute('aria-expanded',String(!ex));
      });
    });

    /* ================= CTA tracking (optional) ================= */
    function track(label){ if(typeof gtag==='function'){ gtag('event','click',{event_category:'CTA',event_label:label}) } else { console.log('[track]',label) } }
    document.querySelectorAll('[data-evt]').forEach(el=>{ el.addEventListener('click',()=>track(el.getAttribute('data-evt'))); });
  </script>
</body>
</html>
