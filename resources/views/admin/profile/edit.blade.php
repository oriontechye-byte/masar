@extends('admin.layouts.admin')

@section('title', 'إعدادات الحساب')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<style>
    :root{
        /* نفس هوية اللوحة */
        --brand:#e67e22; --brand2:#f39c12;
        --ink:#2c3e50; --muted:#7f8c8d;
        --bg:#f7f9fc; --line:#eef1f4;
        --glass:rgba(255,255,255,.75);
        --shadow:0 18px 48px rgba(0,0,0,.12);
        --ring:rgba(230,126,34,.18);
    }
    body.dark-mode{
        --bg:#0f1115; --ink:#e9edf2; --muted:#b6bdc6; --line:#1f2430;
        --glass:rgba(26,31,43,.68);
        --shadow:0 28px 60px rgba(0,0,0,.55);
        --ring:rgba(243,156,18,.22);
    }
    body{font-family:'Cairo',sans-serif; background:var(--bg); color:var(--ink)}

    /* حاوية تُوسّط البطاقة */
    .profile-wrap{
        min-height:calc(100vh - 160px);
        display:grid; place-items:center;
        position:relative; padding:28px;
    }
    /* وهج جانبي ناعم */
    .ambient{
        position:absolute; inset:0; pointer-events:none; filter:blur(70px); z-index:0;
        background:
            radial-gradient(380px 200px at 85% 10%, rgba(243,156,18,.20), transparent 60%),
            radial-gradient(380px 200px at 15% 90%, rgba(230,126,34,.15), transparent 60%);
        animation:float 16s ease-in-out infinite;
    }
    body.dark-mode .ambient{
        background:
            radial-gradient(380px 200px at 80% 14%, rgba(243,156,18,.14), transparent 60%),
            radial-gradient(380px 200px at 18% 86%, rgba(230,126,34,.12), transparent 60%);
    }
    @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}

    /* البطاقة الزجاجية */
    .glass-card{
        width:min(760px, 100%);
        border-radius:18px;
        background:var(--glass);
        backdrop-filter:blur(14px);
        border:1px solid var(--line);
        box-shadow:var(--shadow);
        overflow:hidden;
        transform:translateY(6px);
        transition:transform .25s ease, box-shadow .25s ease;
        z-index:1;
    }
    .glass-card:hover{ transform:translateY(0); box-shadow:0 24px 60px rgba(0,0,0,.16) }

    .glass-head{
        padding:16px 18px; border-bottom:1px solid var(--line);
        display:flex; align-items:center; justify-content:space-between; gap:12px;
    }
    .glass-title{margin:0; font-weight:800; display:flex; align-items:center; gap:.6rem}
    .glass-body{padding:20px}

    /* عناصر الإدخال */
    .form-grid{display:grid; grid-template-columns:1fr 1fr; gap:14px}
    @media (max-width:768px){ .form-grid{grid-template-columns:1fr} }

    .msr-field label{font-weight:700; color:var(--ink); margin-bottom:6px}
    .msr-input{
        display:flex; align-items:center; gap:8px;
        background:rgba(255,255,255,.75); border:1px solid var(--line);
        border-radius:12px; padding-inline:12px;
        transition:box-shadow .2s, border-color .2s, background .2s;
    }
    body.dark-mode .msr-input{background:rgba(21,26,36,.65)}
    .msr-input i{color:#94a3b8}
    .msr-input input{
        border:0; outline:0; background:transparent; width:100%;
        padding:12px 0; color:inherit; font-weight:600;
    }
    .msr-input:focus-within{ box-shadow:0 0 0 .18rem var(--ring); border-color:var(--brand) }

    /* زر الحفظ */
    .msr-btn{
        border-radius:999px; font-weight:800; padding:.7rem 1.2rem;
        border:2px solid transparent; display:inline-flex; align-items:center; gap:.5rem;
        transition:transform .2s, filter .2s; cursor:pointer;
    }
    .msr-btn-primary{
        background:linear-gradient(90deg,var(--brand2),var(--brand)); color:#fff;
        box-shadow:0 12px 26px rgba(230,126,34,.28);
    }
    .msr-btn-primary:hover{ transform:translateY(-2px); filter:brightness(.98) }

    /* تنسيقات تنبيه */
    .alert{border-radius:12px; border:1px solid var(--line)}
</style>
@endpush

@section('content')
<div class="profile-wrap" dir="rtl">
    <div class="ambient" aria-hidden="true"></div>

    <div class="glass-card">
        <div class="glass-head">
            <h4 class="glass-title">
                <i class="fa-solid fa-user-gear text-warning"></i>
                تعديل الملف الشخصي
            </h4>
        </div>

        <div class="glass-body">
            @if (session('success'))
                <div class="alert alert-success mb-3">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0 pr-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.profile.update') }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="msr-field">
                        <label for="name">الاسم</label>
                        <div class="msr-input">
                            <i class="fa-solid fa-id-card-clip"></i>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" />
                        </div>
                    </div>

                    <div class="msr-field">
                        <label for="email">البريد الإلكتروني</label>
                        <div class="msr-input">
                            <i class="fa-solid fa-envelope"></i>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" />
                        </div>
                    </div>

                    <div class="msr-field">
                        <label for="password">كلمة المرور الجديدة</label>
                        <div class="msr-input">
                            <i class="fa-solid fa-lock"></i>
                            <input id="password" type="password" name="password" placeholder="اتركها فارغة لعدم التغيير" />
                        </div>
                    </div>

                    <div class="msr-field">
                        <label for="password_confirmation">تأكيد كلمة المرور الجديدة</label>
                        <div class="msr-input">
                            <i class="fa-solid fa-shield-halved"></i>
                            <input id="password_confirmation" type="password" name="password_confirmation" />
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    <button type="submit" class="msr-btn msr-btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
