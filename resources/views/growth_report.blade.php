<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>تقرير تطوّرك - مشروع مسار</title>

  <!-- ملاحظة: لو عندك CSP صارمة، قد تحتاج السماح لـ fonts.googleapis.com و fonts.gstatic.com و cdnjs.cloudflare.com -->
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Cairo',sans-serif;overflow-x:hidden;color:#2c3e50}

    /* ===== Navbar ===== */
    .navbar { position: fixed; top: 0; left: 0; right: 0; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 15px 0; z-index: 1000; transition: all .3s ease; box-shadow: 0 2px 20px rgba(0,0,0,0.1) }
    .navbar.scrolled { background: rgba(255,255,255,0.98); box-shadow: 0 2px 30px rgba(0,0,0,0.15) }
    .nav-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 20px }
    .logo { font-size: 1.8rem; font-weight: 800; color: #e67e22; text-decoration: none; display: flex; align-items: center; gap: 10px }
    .logo i { font-size: 2rem }
    .dark-toggle { cursor: pointer; font-size: 1.3rem; margin-right: 15px; color: #2c3e50; transition: color 0.3s ease }

    /* ===== Hero ===== */
    .hero{min-height:100vh; background:linear-gradient(135deg,#f39c12 0%,#e67e22 50%,#d35400 100%);
      position:relative; display:flex; align-items:flex-start; justify-content:center; padding:120px 16px 64px}
    .hero::before{
      content:''; position:absolute; inset:0;
      background-image:
        radial-gradient(circle at 20% 80%, rgba(255,255,255,.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255,255,255,.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(255,255,255,.05) 0%, transparent 50%);
      animation:float 20s ease-in-out infinite;
    }
    @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}

    /* ===== Wrapper ===== */
    .container{position:relative;z-index:2; width:min(1200px,94vw); display:flex; flex-direction:column; gap:16px; color:#fff}

    /* Glass cards */
    .card{
      background:rgba(255,255,255,.20); border:1px solid rgba(255,255,255,.35); backdrop-filter:blur(12px);
      border-radius:24px; padding:22px; box-shadow:0 20px 60px rgba(0,0,0,.18); color:#fff;
    }
    .title{font-size:1.5rem; font-weight:900; display:flex; align-items:center; gap:10px; margin-bottom:8px}
    .subtitle{opacity:.95; font-weight:600; font-size:1rem}

    /* Grid */
    .grid{display:grid; gap:16px}
    .grid-3{grid-template-columns:repeat(3,1fr)}
    @media (max-width: 980px){ .grid-3{grid-template-columns:1fr} }

    /* Top 3 cards */
    .top-card{display:flex; flex-direction:column; gap:10px; border-radius:18px; border:1px solid rgba(255,255,255,.28); background:rgba(255,255,255,.14); padding:16px}
    .top-head{display:flex; align-items:center; justify-content:space-between; gap:10px}
    .top-name{font-weight:900}
    .badge{display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; font-weight:900; font-size:.95rem; background:rgba(0,0,0,.22)}
    .bar{height:8px; background:rgba(255,255,255,.18); border-radius:999px; overflow:hidden}
    .bar>span{display:block; height:100%; width:0; background:linear-gradient(90deg,#fff,#ffe0b2); transition:width .8s ease .1s}
    .chips{display:flex; flex-wrap:wrap; gap:8px}
    .chip{background:rgba(0,0,0,.24); border:1px solid rgba(255,255,255,.28); padding:6px 10px; border-radius:999px; font-weight:700; font-size:.9rem}

    .top-card.best{border-color:#ffd54f; box-shadow:0 14px 38px rgba(255,213,79,.35)}
    .top-card.best .badge{background:linear-gradient(135deg,#ffd54f,#ffb300); color:#222}
    .top-card.best .bar>span{background:linear-gradient(90deg,#ffd54f,#ffb300)}

    .actions{display:flex; gap:8px; justify-content:flex-end; flex-wrap:wrap}
    .btn{padding:10px 14px; border:none; border-radius:999px; font-weight:800; cursor:pointer; display:inline-flex; align-items:center; gap:8px; text-decoration:none}
    .btn-primary{background:#fff; color:#e67e22}
    .btn-outline{background:transparent; color:#fff; border:2px solid rgba(255,255,255,.6)}

    /* Table */
    table{width:100%; border-collapse:collapse}
    th,td{border:1px solid rgba(255,255,255,.25); padding:10px; text-align:center}
    th{background:rgba(0,0,0,.18)}
    .up{color:#d1ffd1} .down{color:#ffd1d1}
    .muted{opacity:.8}

    /* Modal (تفاصيل) */
    .modal-backdrop{position:fixed; inset:0; background:rgba(0,0,0,.6); display:none; align-items:center; justify-content:center; z-index:1200}
    .modal{width:min(720px,92vw); background:#1f1f1f; color:#eee; border-radius:18px; padding:18px; border:1px solid #333; position:relative}
    .modal h3{margin-bottom:8px}
    .modal .close{position:absolute; top:12px; left:12px; background:transparent; color:#fff; border:0; font-size:1.2rem; cursor:pointer}

    /* Dark mode */
    body.dark-mode { color:#eaeaea }
    body.dark-mode .navbar { background: rgba(18,18,18,0.95); box-shadow: 0 2px 20px rgba(0,0,0,0.4) }
    body.dark-mode .logo { color:#f39c12 }
    body.dark-mode .hero { background: linear-gradient(135deg,#1f1f1f 0%, #171717 50%, #101010 100%) }
    body.dark-mode .card { background: rgba(18,18,18,.42); border-color: rgba(255,255,255,.22) }
    body.dark-mode th { background: rgba(255,255,255,.08) }
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
      <div style="display:flex; align-items:center; gap:12px">
        <!-- رجوع للنتيجة بدون أي ID -->
        <a href="{{ route('results.show') }}" class="btn btn-outline"><i class="fa-solid fa-rotate-left"></i> رجوع للنتيجة</a>
        <!-- تحميل PDF بدون ID (اختياري) -->
        <a href="{{ route('results.pdf') }}" class="btn btn-outline"><i class="fa-solid fa-file-pdf"></i> تحميل PDF</a>
        <i class="fas fa-moon dark-toggle" id="darkToggle" aria-label="تبديل الوضع"></i>
      </div>
    </div>
  </nav>

  <!-- Hero -->
  <section class="hero">
    <div class="container">

      <!-- Header -->
      <div class="card">
        <div class="title"><i class="fa-solid fa-chart-line"></i> تقرير تطوّرك</div>
        <div class="subtitle">مرحبًا {{ $student->full_name ?? 'طالبنا العزيز' }} — هنا مقارنة بين نتيجتك <strong>القبلية</strong> و <strong>البعدية</strong> مع أعلى 3 مسارات مناسبة لك.</div>
      </div>

      <!-- Top 3 (full width) -->
      <div class="card">
        <div class="title"><i class="fa-solid fa-ranking-star"></i> أعلى 3 ذكاءات (بعديًا)</div>
        <div class="grid grid-3">
          @foreach($topThree as $idx => $t)
            <div class="top-card {{ $idx===0 ? 'best' : '' }}">
              <div class="top-head">
                <div class="top-name">
                  @if($idx===0)<i class="fa-solid fa-crown" style="color:#ffecb3; margin-left:6px"></i>@endif
                  {{ $t['name'] }}
                </div>
                <span class="badge"><i class="fa-solid fa-percent"></i> {{ $t['post_percent'] }}%</span>
              </div>

              <div class="bar" aria-hidden="true">
                <span data-width="{{ $t['post_percent'] }}%"></span>
              </div>

              @if(!empty($t['careers']))
                <div class="chips" aria-label="تخصصات مقترحة">
                  @foreach(array_slice($t['careers'],0,4) as $career)
                    <span class="chip">{{ $career }}</span>
                  @endforeach
                </div>
              @endif

              <div class="actions">
                <button class="btn btn-primary" data-open="modal-{{ $t['id'] }}"><i class="fa-solid fa-circle-info"></i> تفاصيل</button>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Table -->
      <div class="card">
        <div class="title"><i class="fa-solid fa-table"></i> جدول التطوّر التفصيلي</div>
        <div style="overflow:auto">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>نوع الذكاء</th>
                <th>قبلي %</th>
                <th>بعدي %</th>
                <th>الفرق</th>
              </tr>
            </thead>
            <tbody>
              @php $i=1; @endphp
              @foreach($tableRows as $row)
                <tr>
                  <td>{{ $i++ }}</td>
                  <td>{{ $row['name'] }}</td>
                  <td class="{{ ($row['pre'] ?? 0)>=70 ? 'up' : 'muted' }}">{{ $row['pre'] }}</td>
                  <td class="{{ is_null($row['post']) ? 'muted' : (($row['post']>=70)?'up':'') }}">{{ is_null($row['post']) ? '—' : $row['post'] }}</td>
                  <td>
                    @php $d = $row['diff']; @endphp
                    @if(is_null($d))
                      <span class="muted">—</span>
                    @else
                      <strong class="{{ $d>=0 ? 'up' : 'down' }}">{{ $d>=0 ? '+'.$d.'%' : $d.'%' }}</strong>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <!-- Call to action -->
      <div class="card" style="display:flex; justify-content:space-between; align-items:center; gap:12px">
        <div class="subtitle">هل تريد خطة تطوير مبنية على أقوى ذكاءاتك؟</div>
        <a href="{{ route('landing') }}" class="btn btn-primary"><i class="fa-solid fa-house"></i> الصفحة الرئيسية</a>
      </div>

    </div>
  </section>

  <!-- Modals for Top3 details -->
  @foreach($topThree as $t)
    <div class="modal-backdrop" id="modal-{{ $t['id'] }}-wrap" aria-hidden="true">
      <div class="modal" role="dialog" aria-modal="true" aria-labelledby="ttl-{{ $t['id'] }}">
        <button class="close" data-close="modal-{{ $t['id'] }}"><i class="fa-solid fa-xmark"></i></button>
        <h3 id="ttl-{{ $t['id'] }}">{{ $t['name'] }}</h3>

        @if(!empty($t['description']))
          <p style="margin:.5rem 0 1rem; opacity:.9">{{ $t['description'] }}</p>
        @endif

        @if(!empty($t['careers']))
          <div style="margin-top:8px">
            <div style="font-weight:800; margin-bottom:8px">التخصصات/المسارات المقترحة:</div>
            <ul style="display:grid; grid-template-columns:repeat(2,1fr); gap:8px; list-style:none; padding:0">
              @foreach($t['careers'] as $career)
                <li style="background:#2a2a2a; border:1px solid #3a3a3a; padding:8px 10px; border-radius:10px">{{ $career }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div style="margin-top:14px; display:flex; gap:8px; flex-wrap:wrap">
          <span class="badge"><i class="fa-solid fa-percent"></i> بعدي: {{ $t['post_percent'] }}%</span>
          @if(!is_null($t['diff_percent']))
            <span class="badge">{{ $t['diff_percent']>=0 ? 'تحسّن +' . $t['diff_percent'].'%' : 'انخفاض ' . $t['diff_percent'].'%' }}</span>
          @endif
        </div>
      </div>
    </div>
  @endforeach

  <script>
    // Navbar scroll effect
    window.addEventListener('scroll', () => {
      const navbar = document.getElementById('navbar');
      if (window.scrollY > 50) navbar.classList.add('scrolled'); else navbar.classList.remove('scrolled');
    });

    // Dark mode toggle
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

      if (body.classList.contains('dark-mode')) {
        darkToggle.classList.replace('fa-moon','fa-sun');
      }

      darkToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        const enabled = body.classList.contains('dark-mode');
        try { localStorage.setItem('dark-mode', enabled ? 'enabled' : 'disabled'); } catch(e){}
        if (enabled) darkToggle.classList.replace('fa-moon','fa-sun'); else darkToggle.classList.replace('fa-sun','fa-moon');
      });
    })();

    // تعبئة عرض الأشرطة من data-width
    window.addEventListener('load', () => {
      document.querySelectorAll('.bar > span').forEach((el) => {
        const target = el.getAttribute('data-width') || '0%';
        el.style.width = '0%';
        requestAnimationFrame(() => { el.style.width = target; });
      });
    });

    // Modals
    document.querySelectorAll('[data-open]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const id = btn.getAttribute('data-open');
        const wrap = document.getElementById(id+'-wrap');
        if(wrap){ wrap.style.display='flex'; }
      });
    });
    document.querySelectorAll('[data-close]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const id = btn.getAttribute('data-close');
        const wrap = document.getElementById(id+'-wrap');
        if(wrap){ wrap.style.display='none'; }
      });
    });
    document.querySelectorAll('.modal-backdrop').forEach(b=>{
      b.addEventListener('click', (e)=>{ if(e.target === b) b.style.display='none'; });
    });
  </script>
</body>
</html>
