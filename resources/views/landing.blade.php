<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مشروع مسار - اكتشف ذكاءاتك المتعددة</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            overflow-x: hidden;
            background: #f8f9fa;
        }

        /* Animated Background */
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 50%, #d35400 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255,255,255,0.05) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Floating particles */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            animation: particle-float 15s infinite linear;
        }

        .particle:nth-child(1) { width: 4px; height: 4px; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 6px; height: 6px; left: 20%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 3px; height: 3px; left: 30%; animation-delay: 4s; }
        .particle:nth-child(4) { width: 5px; height: 5px; left: 40%; animation-delay: 6s; }
        .particle:nth-child(5) { width: 4px; height: 4px; left: 50%; animation-delay: 8s; }
        .particle:nth-child(6) { width: 7px; height: 7px; left: 60%; animation-delay: 10s; }
        .particle:nth-child(7) { width: 3px; height: 3px; left: 70%; animation-delay: 12s; }
        .particle:nth-child(8) { width: 5px; height: 5px; left: 80%; animation-delay: 14s; }
        .particle:nth-child(9) { width: 4px; height: 4px; left: 90%; animation-delay: 16s; }

        @keyframes particle-float {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 15px 0;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }

        .navbar.scrolled {
            background: rgba(255,255,255,0.98);
            box-shadow: 0 2px 30px rgba(0,0,0,0.15);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: #e67e22;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            font-size: 2rem;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #2c3e50;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: #e67e22;
            transform: translateY(-2px);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #e67e22;
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        /* Hero Content */
        .hero-content {
            text-align: center;
            color: white;
            z-index: 2;
            position: relative;
            max-width: 800px;
            padding: 0 20px;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            animation: slideInUp 1s ease-out;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 30px;
            opacity: 0.95;
            font-weight: 400;
            animation: slideInUp 1s ease-out 0.2s both;
        }

        .hero-description {
            font-size: 1.1rem;
            margin-bottom: 40px;
            opacity: 0.9;
            line-height: 1.8;
            animation: slideInUp 1s ease-out 0.4s both;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* CTA Buttons */
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            animation: slideInUp 1s ease-out 0.6s both;
        }

        .btn {
            padding: 15px 35px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: 'Cairo', sans-serif;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
            backdrop-filter: blur(10px);
        }

        .btn-primary:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .btn-secondary {
            background: white;
            color: #e67e22;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: #e67e22;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            background: white;
            position: relative;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section-title {
            text-align: center;
            font-size: 3rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .section-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: #7f8c8d;
            margin-bottom: 60px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }

        .feature-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #e67e22, #f39c12);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .feature-icon {
            font-size: 3.5rem;
            color: #e67e22;
            margin-bottom: 20px;
            display: block;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .feature-description {
            color: #7f8c8d;
            line-height: 1.7;
        }

        /* Stats Section */
        .stats-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            text-align: center;
        }

        .stat-item {
            padding: 20px;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: #f39c12;
            margin-bottom: 10px;
            display: block;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Footer */
        .footer {
            background: #2c3e50;
            color: white;
            padding: 60px 0 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #f39c12;
        }

        .footer-section p, .footer-section a {
            color: #bdc3c7;
            line-height: 1.7;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: #f39c12;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-link {
            width: 40px;
            height: 40px;
            background: #34495e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: #f39c12;
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #34495e;
            color: #95a5a6;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 10px 0;
            }
            .nav-links {
                display: none;
            }
            .hero-title {
                font-size: 2.5rem;
            }
            .hero-subtitle {
                font-size: 1.2rem;
            }
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
            .section-title {
                font-size: 2rem;
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Scroll animations */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===================== إضافات الوضع الليلي فقط (لا حذف للكود الأصلي) ===================== */
        body.dark-mode {
            background: #1a1a1a;
            color: #f1f1f1;
        }
        body.dark-mode .navbar {
            background: rgba(30,30,30,0.95);
        }
        body.dark-mode .nav-links a { color: #f1f1f1; }
        body.dark-mode .nav-links a:hover { color: #f39c12; }
        body.dark-mode .features-section { background: #2a2a2a; }
        body.dark-mode .feature-card { background: #333; color: #f1f1f1; }
        body.dark-mode .feature-description { color: #ccc; }
        body.dark-mode .stats-section { background: linear-gradient(135deg, #111, #222); }
        body.dark-mode .footer { background: #111; }
        body.dark-mode .footer-section p, body.dark-mode .footer-section a { color: #aaa; }
        body.dark-mode .footer-bottom { border-top: 1px solid #333; }

        /* زر التبديل للوضع الليلي */
        .dark-toggle {
            cursor: pointer;
            font-size: 1.3rem;
            margin-right: 15px;
            color: #2c3e50;
            transition: color 0.3s ease;
        }
        .dark-toggle:hover { color: #f39c12; }
        body.dark-mode .dark-toggle { color: #f1f1f1; }
            
        /* ===== تحسينات كاملة للوضع الليلي (دون حذف الأصل) ===== */
        body.dark-mode { background:#121212; color:#eaeaea; }
        body.dark-mode .navbar { background: rgba(18,18,18,0.95); box-shadow: 0 2px 20px rgba(0,0,0,0.4); }
        body.dark-mode .logo { color:#f39c12; }
        body.dark-mode .nav-links a { color:#eaeaea; }
        body.dark-mode .nav-links a::after { background:#f39c12; }

        /* بطل الهيرو غامق بدل التدرج البرتقالي */
        body.dark-mode .hero-section { background: linear-gradient(135deg,#1f1f1f 0%, #171717 50%, #101010 100%); }
        body.dark-mode .hero-section::before { 
            background-image:
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.06) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.05) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255,255,255,0.03) 0%, transparent 50%);
        }
        body.dark-mode .hero-title { color:#ffffff; text-shadow:none; }
        body.dark-mode .hero-subtitle { color:#d6d6d6; }
        body.dark-mode .hero-description { color:#c9c9c9; }

        /* البطاقات والقسمات */
        body.dark-mode .features-section { background:#171717; }
        body.dark-mode .feature-card { background:#1f1f1f; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        body.dark-mode .feature-title { color:#f0f0f0; }
        body.dark-mode .feature-description { color:#bdbdbd; }
        body.dark-mode .feature-card::before { background: linear-gradient(90deg,#f39c12,#e67e22); }
        body.dark-mode .feature-icon { color:#f39c12; }

        /* الأزرار */
        body.dark-mode .btn-primary { background: rgba(255,255,255,0.08); color:#fff; border-color: rgba(255,255,255,0.15); }
        body.dark-mode .btn-primary:hover { background: rgba(255,255,255,0.14); box-shadow:0 10px 30px rgba(0,0,0,0.6); }
        body.dark-mode .btn-secondary { background:#f39c12; color:#111; border-color:#f39c12; }
        body.dark-mode .btn-secondary:hover { background:#e67e22; color:#fff; }

        /* العناوين في الأقسام */
        body.dark-mode .section-title { color:#f0f0f0; }
        body.dark-mode .section-subtitle { color:#bdbdbd; }

        /* الإحصائيات والفوتر */
        body.dark-mode .stats-section { background: linear-gradient(135deg,#0f0f0f,#1a1a1a); }
        body.dark-mode .footer { background:#0f0f0f; }
        body.dark-mode .footer-section p, body.dark-mode .footer-section a { color:#b3b3b3; }
        body.dark-mode .footer-section a:hover { color:#f39c12; }
        body.dark-mode .footer-bottom { border-top:1px solid #222; color:#9e9e9e; }

        /* أيقونة التبديل */
        .dark-toggle{ cursor:pointer; font-size:1.3rem; margin-right:15px; color:#2c3e50; transition:color .3s ease; }
        .dark-toggle:hover{ color:#f39c12; }
        body.dark-mode .dark-toggle{ color:#f1f1f1; }

    </style>
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="{{ route('landing') }}" class="logo">
                <i class="fas fa-brain"></i>
                مشروع مسار
            </a>
            <ul class="nav-links">
                <li><a href="#home">الرئيسية</a></li>
                <li><a href="#features">المميزات</a></li>
                <li><a href="#contact">اتصل بنا</a></li>
            </ul>
            <!-- زر الوضع الليلي (إضافة فقط) -->
            <i class="fas fa-moon dark-toggle" id="darkToggle" aria-label="تبديل الوضع"></i>
        </div>
    </nav>

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
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="fas fa-play"></i>
                    الاختبار القبلي (قبل الدورة)
                </a>
                <a href="{{ route('post-test.lookup') }}" class="btn btn-secondary">
                    <i class="fas fa-redo-alt"></i>
                    الاختبار البعدي (بعد الدورة)
                </a>
            </div>
        </div>
    </section>

    <section class="features-section" id="features">
        <div class="container">
            <h2 class="section-title fade-in">ما هو اختبار الذكاءات المتعددة؟</h2>
            <p class="section-subtitle fade-in">
                يُعتبر قرار اختيار موضوع التعليم العالي من أهم القرارات التي يتخذها الإنسان في حياته.
                لذلك قمنا بتطوير هذا الاختبار لمساعدتك في اتخاذ القرار الأنسب.
            </p>

            <div class="features-grid">
                <div class="feature-card fade-in">
                    <i class="fas fa-brain feature-icon"></i>
                    <h3 class="feature-title">8 أنواع ذكاء</h3>
                    <p class="feature-description">
                        اكتشف أنواع ذكائك الثمانية: اللغوي، المنطقي، المكاني، الحركي، الموسيقي، الاجتماعي، الذاتي، والطبيعي
                    </p>
                </div>

                <div class="feature-card fade-in">
                    <i class="fas fa-chart-line feature-icon"></i>
                    <h3 class="feature-title">تحليل شامل</h3>
                    <p class="feature-description">
                        احصل على تحليل مفصل لنتائجك مع مقارنة بين الاختبار القبلي والبعدي لقياس تطورك
                    </p>
                </div>

                <div class="feature-card fade-in">
                    <i class="fas fa-graduation-cap feature-icon"></i>
                    <h3 class="feature-title">توصيات مهنية</h3>
                    <p class="feature-description">
                        احصل على توصيات مخصصة للمسارات المهنية والتخصصات الأكاديمية المناسبة لنوع ذكائك
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item fade-in">
                    <span class="stat-number" data-count="8">0</span>
                    <span class="stat-label">أنواع ذكاء</span>
                </div>
                <div class="stat-item fade-in">
                    <span class="stat-number" data-count="64">0</span>
                    <span class="stat-label">سؤال شامل</span>
                </div>
                <div class="stat-item fade-in">
                    <span class="stat-number" data-count="15">0</span>
                    <span class="stat-label">دقيقة فقط</span>
                </div>
                <div class="stat-item fade-in">
                    <span class="stat-number" data-count="100">0</span>
                    <span class="stat-label">% مجاني</span>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>مشروع مسار</h3>
                    <p>
                        نساعدك في اكتشاف أنواع ذكائك المختلفة واتخاذ القرارات الأكاديمية والمهنية المناسبة لك.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3>روابط سريعة</h3>
                    <p><a href="{{ route('register') }}">بدء الاختبار</a></p>
                
                    <p><a href="#features">المميزات</a></p>
                </div>

                <div class="footer-section">
                    <h3>تواصل معنا</h3>
                    <p><i class="fas fa-envelope"></i> info@masar.com</p>
                    <p><i class="fas fa-phone"></i> +967 774198483</p>
                    <p><i class="fas fa-map-marker-alt"></i>مأرب، اليمن</p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 مشروع مسار. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Fade in animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // Counter animation
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 2000; // 2 seconds
                let start = null;

                const step = timestamp => {
                    if (!start) start = timestamp;
                    const progress = Math.min((timestamp - start) / duration, 1);
                    counter.textContent = Math.floor(progress * target);
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                };
                window.requestAnimationFrame(step);
            });
        }

        // Trigger counter animation when stats section is visible
        const statsSection = document.querySelector('.stats-section');
        if (statsSection) {
            const statsObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        statsObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            statsObserver.observe(statsSection);
        }

        // ===================== إضافة جافاسكربت للوضع الليلي فقط =====================
        const darkToggle = document.getElementById('darkToggle');
        const body = document.body;

        // لو مافي تفضيل محفوظ، اتبع تفضيل النظام تلقائياً
        if (!localStorage.getItem('dark-mode') && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            body.classList.add('dark-mode');
        }

        // استرجاع الخيار المحفوظ مسبقاً
        try {
            if (localStorage.getItem('dark-mode') === 'enabled') {
                body.classList.add('dark-mode');
                if (darkToggle) darkToggle.classList.replace('fa-moon', 'fa-sun');
            }
        } catch (e) {}
        // عدّل الأيقونة حسب الوضع الحالي بعد الاسترجاع/التفضيل
        if (body.classList.contains('dark-mode') && darkToggle) {
            darkToggle.classList.replace('fa-moon','fa-sun');
        }

        if (darkToggle) {
            darkToggle.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                const enabled = body.classList.contains('dark-mode');
                try {
                    localStorage.setItem('dark-mode', enabled ? 'enabled' : 'disabled');
                } catch (e) {}
                if (enabled) darkToggle.classList.replace('fa-moon', 'fa-sun');
                else darkToggle.classList.replace('fa-sun', 'fa-moon');
            });
        }
    </script>
</body>
</html>
