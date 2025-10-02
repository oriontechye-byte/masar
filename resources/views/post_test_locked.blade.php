<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>الاختبار البعدي غير متاح</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
  <style>
    *{box-sizing:border-box}
    body{margin:0;font-family:'Cairo',sans-serif;background:#f7f9fc;color:#2c3e50}
    .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
    .card{background:#fff;border:1px solid #eef1f4;border-radius:14px;box-shadow:0 10px 30px rgba(0,0,0,.08);max-width:720px;width:100%;padding:28px}
    .title{font-size:22px;font-weight:800;margin:0 0 10px}
    .msg{line-height:1.9;color:#586174;margin:0 0 18px}
    .note{background:#fff8e6;border:1px solid #ffe1a3;color:#7a5d00;padding:10px 12px;border-radius:10px;margin-bottom:18px}
    .btn{display:inline-block;padding:10px 16px;border-radius:10px;background:#2563eb;color:#fff;text-decoration:none}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1 class="title">الاختبار البعدي غير متاح حالياً</h1>
      <p class="msg">
        تم إيقاف الوصول إلى الاختبار البعدي من قبل الإدارة. الهدف هو أن يقوم الطالب أولاً بـ
        <strong>الاختبار القبلي</strong> قبل أي تدريب، وبعد الانتهاء من الدورة التدريبية سيتم فتح
        <strong>الاختبار البعدي</strong> للمقارنة وقياس التغيّر.
      </p>
      <div class="note">
        عند إتمام الدورة ستظهر لك إمكانية الدخول إلى الاختبار البعدي تلقائياً. إذا كنت ترى أن هذا خطأ،
        يُرجى التواصل مع المشرف.
      </div>
      <a class="btn" href="{{ route('landing') }}">العودة للصفحة الرئيسية</a>
    </div>
  </div>
</body>
</html>
