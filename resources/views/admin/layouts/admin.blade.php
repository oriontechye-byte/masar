<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'لوحة التحكم') - مشروع مسار</title>

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    :root{
      /* Light */
      --bg:#f7f9fc; --card:#ffffff; --ink:#2c3e50; --muted:#7f8c8d;
      --brand-1:#f39c12; --brand-2:#e67e22; --ring:rgba(230,126,34,.18);
      --glass:rgba(52,58,64,.92); --line:#eef1f4; --shadow:0 12px 36px rgba(0,0,0,.10);
      --sidebar-w:260px;
    }
    body.dark-mode{
      /* Dark */
      --bg:#0f1115; --card:#151821; --ink:#e9edf2; --muted:#b6bdc6;
      --glass:rgba(18,20,26,.72); --line:#1f2430; --shadow:0 18px 42px rgba(0,0,0,.45);
    }

    *{margin:0;padding:0;box-sizing:border-box}
    html,body{height:100%}
    body{font-family:'Cairo',sans-serif; background:var(--bg); color:var(--ink); display:flex; min-height:100vh}

    /* ===== Ambient prestige glow (subtle) ===== */
    .glow{position:fixed; inset:0; pointer-events:none; z-index:-1; filter:blur(70px);
      background:
        radial-gradient(420px 220px at 85% 6%, rgba(243,156,18,.22), transparent 60%),
        radial-gradient(420px 220px at 10% 92%, rgba(230,126,34,.18), transparent 60%);
      animation:float 16s ease-in-out infinite
    }
    body.dark-mode .glow{background:
      radial-gradient(420px 220px at 80% 10%, rgba(243,156,18,.16), transparent 60%),
      radial-gradient(420px 220px at 14% 86%, rgba(230,126,34,.12), transparent 60%)}
    @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}

    /* ===== Sidebar (glass) ===== */
    .sidebar{
      width:var(--sidebar-w); background:var(--glass); backdrop-filter:blur(12px); color:#fff;
      height:100vh; position:fixed; right:0; top:0; padding:20px 0 12px; box-shadow:-5px 0 18px rgba(0,0,0,.18)
    }
    .brand{
      margin:0 16px 22px; display:flex; align-items:center; justify-content:space-between;
      color:#fff;
    }
    .brand-title{display:flex; align-items:center; gap:8px; font-weight:800}
    .brand-title i{color:var(--brand-1)}
    .sidebar-toggle{
      display:none; width:42px; height:42px; border-radius:10px; border:1px solid rgba(255,255,255,.2);
      background:rgba(255,255,255,.06); color:#fff; cursor:pointer;
    }

    .sidebar-nav a{
      display:flex; align-items:center; gap:10px; color:#cfd4da; padding:14px 20px; text-decoration:none;
      transition:background .25s, color .25s, padding-right .25s; border-right:3px solid transparent
    }
    .sidebar-nav a i{width:20px; text-align:center}
    .sidebar-nav a:hover{background:rgba(255,255,255,.08); color:#fff; padding-right:26px}
    .sidebar-nav a.active{background:linear-gradient(135deg,var(--brand-1),var(--brand-2)); color:#fff; border-right-color:#f8f9fa; font-weight:700}
    body.dark-mode .sidebar-nav a{color:#aeb6c2}

    /* ===== Main ===== */
    .main-content{margin-right:var(--sidebar-w); padding:28px; width:calc(100% - var(--sidebar-w))}
    .content-header{
      display:flex; justify-content:space-between; align-items:center; background:var(--card);
      padding:16px 18px; border-radius:14px; box-shadow:var(--shadow); border:1px solid var(--line);
      margin-bottom:22px; animation:fadeInDown .6s ease
    }
    .content-header h1{display:flex; align-items:center; gap:10px; font-size:1.25rem; font-weight:800; color:var(--ink)}
    .right-tools{display:flex; align-items:center; gap:10px}

    .theme-toggle{
      width:42px; height:42px; display:grid; place-items:center; border:none; border-radius:50px; cursor:pointer;
      background:linear-gradient(135deg,var(--brand-1),var(--brand-2)); color:#fff;
      box-shadow:0 10px 26px rgba(230,126,34,.28); transition:transform .15s
    }
    .theme-toggle:hover{transform:translateY(-2px)}

    .logout-form button{
      background:transparent; color:var(--ink); border:1px solid var(--line); padding:10px 14px; border-radius:12px;
      cursor:pointer; font-weight:800; display:flex; align-items:center; gap:8px; transition:background .2s, box-shadow .3s
    }
    .logout-form button:hover{background:rgba(0,0,0,.03); box-shadow:0 6px 20px rgba(0,0,0,.10)}
    body.dark-mode .logout-form button{color:#eaeef3; border-color:#273041; background:#1a1f2b}
    body.dark-mode .logout-form button:hover{background:#232a38}

    /* Cards */
    .stat-card{background:var(--card); border-radius:14px; box-shadow:var(--shadow); padding:24px; border:1px solid var(--line); transition:transform .25s, box-shadow .25s}
    .stat-card:hover{transform:translateY(-6px); box-shadow:0 18px 36px rgba(0,0,0,.12)}

    /* Alerts */
    .flash{margin-bottom:14px}
    .alert{
      padding:12px 14px; border-radius:12px; border:1px solid var(--line); background:var(--card);
      box-shadow:var(--shadow); display:flex; align-items:center; gap:10px; font-weight:700
    }
    .alert-success{border-color:#2e7d3233; color:#2e7d32}
    .alert-danger{border-color:#c6282833; color:#c62828}
    .alert-info{border-color:#0288d133; color:#0277bd}

    /* Pagination */
    .pagination{display:flex; justify-content:center; padding-left:0; list-style:none; border-radius:.25rem; margin-top:20px}
    .pagination li a, .pagination li span{padding:.55rem .9rem; font-size:1rem; border-radius:10px; margin:0 2px; border:1px solid var(--line); background:var(--card); color:var(--ink); text-decoration:none}
    .pagination li a:hover{box-shadow:0 6px 18px rgba(0,0,0,.08)}
    .pagination li.active span{z-index:1; color:#fff; background:linear-gradient(135deg,var(--brand-1),var(--brand-2)); border-color:transparent}
    .pagination li.disabled span{color:#6c757d; pointer-events:none; cursor:auto}

    @keyframes fadeInDown{from{opacity:0; transform:translateY(-16px)} to{opacity:1; transform:translateY(0)}}

    /* ===== Responsive ===== */
    @media (max-width: 992px){
      .sidebar{transform:translateX(100%); transition:transform .25s ease}
      .sidebar.open{transform:translateX(0)}
      .sidebar-toggle{display:inline-grid; place-items:center}
      .main-content{margin-right:0; width:100%}
    }
  </style>

  {{-- يسمح للصفحات بحقن CSS إضافي --}}
  @stack('styles')
</head>
<body>
  <div class="glow" aria-hidden="true"></div>

  <aside class="sidebar" id="sidebar">
    <div class="brand">
      <div class="brand-title"><i class="fa-solid fa-brain"></i> مشروع مسار</div>
      <button class="sidebar-toggle" id="sidebarToggle" aria-label="إظهار/إخفاء القائمة"><i class="fa-solid fa-bars"></i></button>
    </div>

    <nav class="sidebar-nav">
      <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge"></i> لوحة التحكم
      </a>
      <a href="{{ route('admin.students.index') }}" class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
        <i class="fa-solid fa-user-graduate"></i> إدارة الطلاب
      </a>
      <a href="{{ route('admin.questions.index') }}" class="{{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
        <i class="fa-regular fa-circle-question"></i> إدارة الأسئلة
      </a>
      <a href="{{ route('admin.types.index') }}" class="{{ request()->routeIs('admin.types.*') ? 'active' : '' }}">
        <i class="fa-solid fa-brain"></i> إدارة أنواع الذكاء
      </a>
      <a href="{{ route('admin.profile.edit') }}" class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
        <i class="fa-solid fa-user-gear"></i> إعدادات الحساب
      </a>
    </nav>
  </aside>

  <main class="main-content">
    <header class="content-header">
      <h1><i class="fa-solid fa-sitemap"></i> @yield('title')</h1>
      <div class="right-tools">
        <button class="theme-toggle" id="themeToggle" aria-label="تبديل الوضع"><i class="fa-solid fa-moon"></i></button>
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
          @csrf
          <button type="submit"><i class="fa-solid fa-arrow-right-from-bracket"></i> تسجيل الخروج</button>
        </form>
      </div>
    </header>

    {{-- رسائل فلاش عامة --}}
    <div class="flash">
      @if(session('success')) <div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> {{ session('success') }}</div> @endif
      @if(session('error'))   <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div> @endif
      @if(session('info'))    <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i> {{ session('info') }}</div> @endif
    </div>

    <div class="content-body">
      @yield('content')
    </div>
  </main>

  <script>
    // Dark mode
    (function(){
      const body = document.body;
      const btn = document.getElementById('themeToggle');
      try{
        if(localStorage.getItem('masar-admin-theme') === 'dark'){
          body.classList.add('dark-mode');
          btn.innerHTML = '<i class="fa-solid fa-sun"></i>';
        }
      }catch(e){}
      btn.addEventListener('click',()=>{
        body.classList.toggle('dark-mode');
        const dark = body.classList.contains('dark-mode');
        try{ localStorage.setItem('masar-admin-theme', dark ? 'dark' : 'light'); }catch(e){}
        btn.innerHTML = dark ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
      });
    })();

    // Sidebar toggle (mobile)
    (function(){
      const aside = document.getElementById('sidebar');
      const toggle = document.getElementById('sidebarToggle');
      if(toggle){
        toggle.addEventListener('click', ()=> aside.classList.toggle('open'));
      }
    })();
  </script>

  {{-- يسمح للصفحات بحقن سكربتات إضافية --}}
  @stack('scripts')
</body>
</html>
