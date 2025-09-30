<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>اختبار تحديد الذكاء - مشروع مسار</title>
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
    body{font-family:'Cairo',sans-serif; color:var(--ink); background:var(--bg);}

    /* ======= Animated backdrop (same vibe as landing) ======= */
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

    /* ======= Navbar (keeps landing feel) ======= */
    .navbar{position:sticky; top:0; inset-inline:0; z-index:10; background:rgba(255,255,255,.55); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); border-bottom:1px solid rgba(255,255,255,.5)}
    .nav-wrap{max-width:1100px; margin:auto; padding:12px 18px; display:flex; align-items:center; justify-content:space-between}
    .brand{display:flex; align-items:center; gap:10px; color:var(--brand-2); text-decoration:none; font-weight:800; font-size:1.2rem}
    .brand i{font-size:1.4rem}
    .dark-toggle{width:38px;height:38px; display:grid; place-items:center; border-radius:50%; cursor:pointer; color:var(--ink); background:rgba(255,255,255,.7); border:1px solid rgba(0,0,0,.06); transition:transform .2s, background .3s, color .3s}
    .dark-toggle:hover{transform:translateY(-2px); background:#fff}

    /* ======= Quiz Card ======= */
    .wrap{min-height:calc(100svh - 60px); display:grid; place-items:center; padding:32px 16px}
    .quiz-card{width:min(820px, 94vw); background:rgba(255,255,255,.8); backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px); border:1px solid rgba(255,255,255,.65); border-radius:22px; box-shadow:var(--shadow); padding:24px 22px; transform:translateY(14px); opacity:0; transition:all .6s ease}
    .quiz-card.visible{transform:translateY(0); opacity:1}

    .header-row{display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px}
    .step-label{font-weight:800; color:var(--ink)}
    .progress{height:10px; width:100%; background:#ecf0f1; border-radius:999px; overflow:hidden; box-shadow:inset 0 1px 2px rgba(0,0,0,.06)}
    .progress > span{display:block; height:100%; width:0%; background:linear-gradient(90deg,var(--brand-1),var(--brand-2)); transition:width .3s ease}

    .question{margin:18px 0 10px; font-size:1.25rem; font-weight:800; color:#2c3e50}

    .options{display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-top:18px}
    .option{display:flex; align-items:center; justify-content:center; padding:14px 12px; border-radius:14px; border:1px solid #dfe6e9; background:#fff; cursor:pointer; font-weight:800; transition:transform .12s ease, box-shadow .25s ease, border-color .25s ease}
    .option:hover{transform:translateY(-2px); box-shadow:0 10px 22px var(--ring); border-color:var(--brand-2)}

    .hidden-radio{position:absolute; opacity:0; pointer-events:none}

    .done{display:none; text-align:center; padding:10px 4px}
    .done h2{font-size:1.3rem; margin-bottom:10px}

    .submit-btn{display:none; width:100%; padding:14px 18px; background:linear-gradient(135deg,var(--brand-1),var(--brand-2)); color:#fff; border:none; border-radius:50px; cursor:pointer; font-size:17px; font-weight:800; transition:transform .15s, box-shadow .3s}
    .submit-btn:hover{transform:translateY(-2px); box-shadow:0 16px 36px rgba(230,126,34,.35)}

    /* ======= Footer ======= */
    .footer{margin-top:24px; color:var(--muted); text-align:center; font-size:.9rem}

    /* ======= Dark mode (matching landing tweaks) ======= */
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
    body.dark-mode .quiz-card{background:rgba(20,20,22,.62); border-color:rgba(255,255,255,.08); box-shadow:0 10px 30px rgba(0,0,0,.55)}
    body.dark-mode .question{color:#fff}
    body.dark-mode .option{background:#15171b; color:#f1f1f1; border-color:#2a2e35}
    body.dark-mode .option:hover{box-shadow:0 10px 22px rgba(243,156,18,.22); border-color:#f39c12}

    @media (max-width:700px){ .options{grid-template-columns:1fr} }
    @media (prefers-reduced-motion: reduce){ .glow,.dot,.quiz-card{animation:none !important; transition:none !important} }
  </style>
</head>
<body>
  <!-- animated backdrop -->
  <div class="scene"></div>
  <div class="glow"></div>
  <div class="particles" aria-hidden="true">
    <span class="dot"></span><span class="dot"></span><span class="dot"></span>
    <span class="dot"></span><span class="dot"></span><span class="dot"></span>
  </div>

  <!-- navbar matching brand -->
  <nav class="navbar">
    <div class="nav-wrap">
      <a class="brand" href="{{ route('landing') }}"><i class="fas fa-brain"></i> مشروع مسار</a>
      <button class="dark-toggle" id="darkToggle" aria-label="تبديل الوضع"><i class="fas fa-moon"></i></button>
    </div>
  </nav>

  <main class="wrap">
    <div class="quiz-card" id="quizCard">
      <div class="header-row">
        <strong class="step-label" id="stepText">السؤال 1 من {{ count($questions) }}</strong>
        <div class="progress" aria-hidden="true"><span id="bar"></span></div>
      </div>

      <form action="/submit-test" method="POST" id="quizForm">
        @csrf
        <!-- hidden data preserved exactly -->
        <input type="hidden" name="student_id" value="{{ request('student_id') }}">
        <input type="hidden" name="test_type" value="{{ request('test_type') }}">

        <!-- questions rendered by Blade; JS controls visibility -->
        <div id="questions">
          @foreach ($questions as $question)
            <section class="q-block" data-q-index="{{ $loop->index }}" style="display:none">
              <h2 class="question">{{ $loop->iteration }}. {{ $question->text }}</h2>

              <!-- real radios kept for backend -->
              <input class="hidden-radio" type="radio" id="q{{ $question->id }}_2" name="answers[{{ $question->id }}]" value="2" />
              <input class="hidden-radio" type="radio" id="q{{ $question->id }}_1" name="answers[{{ $question->id }}]" value="1" />
              <input class="hidden-radio" type="radio" id="q{{ $question->id }}_0" name="answers[{{ $question->id }}]" value="0" />

              <!-- pretty options -->
              <div class="options">
                <button class="option" type="button" data-value="2">تنطبق بشدة</button>
                <button class="option" type="button" data-value="1">تنطبق أحياناً</button>
                <button class="option" type="button" data-value="0">لا تنطبق</button>
              </div>
            </section>
          @endforeach
        </div>

        <div class="done" id="doneBox">
          <h2><i class="fa-solid fa-check-circle" style="margin-left:6px"></i> لقد أكملت الاختبار بنجاح!</h2>
          <p>اضغط أدناه لإرسال النتيجة.</p>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn">
          <i class="fa-solid fa-paper-plane" style="margin-left:8px"></i> إرسال النتيجة
        </button>
      </form>

      <div class="footer">© {{ date('Y') }} مشروع مسار</div>
    </div>
  </main>

  <script>
    // reveal card on load
    window.addEventListener('load', ()=>{
      const card=document.getElementById('quizCard'); if(card){ requestAnimationFrame(()=> card.classList.add('visible')); }
    });

    // dark mode behavior identical to landing
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

    // single-question flow
    (function(){
      const blocks=[...document.querySelectorAll('.q-block')];
      if(blocks.length===0) return;
      let i=0; const total=blocks.length;
      const stepText=document.getElementById('stepText');
      const bar=document.getElementById('bar');
      const doneBox=document.getElementById('doneBox');
      const submitBtn=document.getElementById('submitBtn');

      function show(idx){
        blocks.forEach((b,j)=> b.style.display = j===idx ? 'block' : 'none');
        stepText.textContent = `السؤال ${Math.min(idx+1,total)} من ${total}`;
        bar.style.width = `${(idx)/total*100}%`;
      }

      function goNext(){
        i++;
        if(i<total){
          show(i);
        } else {
          // finished
          blocks.forEach(b=> b.style.display='none');
          bar.style.width = '100%';
          stepText.textContent = `اكتمل ${total} / ${total}`;
          doneBox.style.display='block';
          submitBtn.style.display='block';
        }
      }

      // attach handlers for all option buttons
      document.getElementById('questions').addEventListener('click', (e)=>{
        const btn = e.target.closest('.option');
        if(!btn) return;
        const section = btn.closest('.q-block');
        const qIndex = parseInt(section.getAttribute('data-q-index'));
        // map value to hidden radios inside this section
        const val = btn.getAttribute('data-value');
        const radios = section.querySelectorAll('.hidden-radio');
        // choose the matching radio by value
        const target = Array.from(radios).find(r=> r.value===val);
        if(target){ target.checked = true; }
        // brief delay for click feedback
        setTimeout(goNext, 200);
      });

      // start on first question
      show(0);
    })();
  </script>
</body>
</html>
