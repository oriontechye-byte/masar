@extends('admin.layouts.admin')

@section('title', 'لوحة التحكم الرئيسية')

@section('content')
    {{-- رسائل نجاح/خطأ --}}
    @if(session('success'))
        <div style="background:#e8fff3;border:1px solid #b7f0d0;color:#0f7a4c;padding:12px 14px;border-radius:10px;margin-bottom:14px">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#fff2f2;border:1px solid #ffcccc;color:#b00020;padding:12px 14px;border-radius:10px;margin-bottom:14px">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6 mt-4">
        <h1 style="font-size:20px;font-weight:800;margin-bottom:6px">لوحة التحكم الرئيسية</h1>
        <p style="color:#6b7280">أهلاً بك في لوحة التحكم الخاصة بمشروع مسار.</p>
    </div>

    {{-- فلاتر عامة --}}
    <form method="GET" action="{{ route('admin.dashboard') }}"
          style="background:#fff;border:1px solid #eef1f4;border-radius:14px;padding:16px;margin-bottom:16px">
        <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px">
            <div>
                <label style="font-size:13px;display:block;margin-bottom:4px">من تاريخ</label>
                <input type="date" name="from" value="{{ \Illuminate\Support\Str::of($stats['from'] ?? '')->substr(0,10) }}"
                       style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:10px">
            </div>
            <div>
                <label style="font-size:13px;display:block;margin-bottom:4px">إلى تاريخ</label>
                <input type="date" name="to" value="{{ \Illuminate\Support\Str::of($stats['to'] ?? '')->substr(0,10) }}"
                       style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:10px">
            </div>
            <div>
                <label style="font-size:13px;display:block;margin-bottom:4px">المحافظة</label>
                @if(!empty($stats['governorates'] ?? null))
                    <select name="governorate" style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:10px">
                        <option value="">كل المحافظات</option>
                        @foreach($stats['governorates'] as $gov)
                            <option value="{{ $gov }}" {{ ($stats['governorate'] ?? '') == $gov ? 'selected' : '' }}>
                                {{ $gov }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" name="governorate" value="{{ $stats['governorate'] ?? '' }}" placeholder="مثال: تعز"
                           style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:10px">
                @endif
            </div>
            <div style="display:flex;gap:8px;align-items:flex-end">
                <button type="submit" style="padding:9px 14px;border-radius:10px;background:#10b981;color:#fff;border:none">
                    تطبيق الفلاتر
                </button>
                <a href="{{ route('admin.dashboard') }}" style="padding:9px 14px;border-radius:10px;border:1px solid #e5e7eb;text-decoration:none;color:#111">
                    إلغاء
                </a>
            </div>
        </div>
    </form>

    {{-- بطاقات إحصائية --}}
    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-bottom:16px">
        <div class="stat-card">
            <div style="font-size:13px;color:#7f8c8d;margin-bottom:4px">إجمالي الطلاب</div>
            <div style="font-size:22px;font-weight:700">{{ $stats['students_count'] ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div style="font-size:13px;color:#7f8c8d;margin-bottom:4px">عدد النتائج المسجلة</div>
            <div style="font-size:22px;font-weight:700">{{ $stats['results_count'] ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div style="font-size:13px;color:#7f8c8d;margin-bottom:4px">متوسط الإجمالي (مجموع الدرجات/8)</div>
            <div style="font-size:22px;font-weight:700">{{ isset($stats['avg_total']) ? number_format($stats['avg_total'],2) : '0.00' }}</div>
        </div>
    </div>

    {{-- التحكم العام في الاختبار البعدي --}}
    <style>
        .state-ok    { color: #16a34a; }
        .state-block { color: #ef4444; }
    </style>
    @php
        $enabled = (bool)($stats['post_test_global_enabled'] ?? false);
    @endphp

    <div style="background:#fff;border:1px solid #eef1f4;border-radius:14px;padding:16px;margin-bottom:16px">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap">
            <div>
                <div style="font-weight:700;margin-bottom:6px">التحكم في الاختبار البعدي (عام)</div>
                <div style="font-size:13px;color:#6b7280">
                    الحالة الحالية:
                    <strong class="{{ $enabled ? 'state-ok' : 'state-block' }}">
                        {{ $enabled ? 'مفتوح' : 'مغلق' }}
                    </strong>
                </div>
            </div>

            <div style="display:flex;gap:8px;flex-wrap:wrap">
                {{-- فتح البعدي للجميع --}}
                <form method="POST" action="{{ route('admin.settings.toggle_post_test_global') }}">
                    @csrf
                    <input type="hidden" name="enabled" value="1">
                    <button type="submit"
                            style="padding:10px 14px;border-radius:10px;background:#2563eb;color:#fff;border:none">
                        فتح الاختبار البعدي
                    </button>
                </form>

                {{-- قفل/إلغاء البعدي للجميع --}}
                <form method="POST" action="{{ route('admin.settings.toggle_post_test_global') }}">
                    @csrf
                    <input type="hidden" name="enabled" value="0">
                    <button type="submit"
                            style="padding:10px 14px;border-radius:10px;background:#ef4444;color:#fff;border:none">
                        إلغاء/قفل الاختبار البعدي
                    </button>
                </form>
            </div>
        </div>

        <div style="font-size:12px;color:#7f8c8d;margin-top:8px">
            * هذا الإعداد عام ويؤثر على زر الاختبار البعدي في الصفحة الرئيسية. عند القفل تظهر رسالة
            "يتم فتح الاختبار البعدي بعد الدورة التدريبية".
        </div>
    </div>

    {{-- أحدث النشاطات --}}
    <div style="background:#fff;border:1px solid #eef1f4;border-radius:14px;padding:16px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
            <h3 style="font-weight:700">أحدث النشاطات</h3>
            <span style="font-size:12px;color:#7f8c8d">
                الفترة: {{ \Illuminate\Support\Str::of($stats['from'] ?? '—')->substr(0,10) }}
                —
                {{ \Illuminate\Support\Str::of($stats['to'] ?? '—')->substr(0,10) }}
                @if(!empty($stats['governorate']))
                    | المحافظة: {{ $stats['governorate'] }}
                @endif
            </span>
        </div>

        @if(!empty($stats['recent_activities']) && count($stats['recent_activities']))
            <div style="overflow-x:auto">
                <table style="width:100%;text-align:right">
                    <thead>
                        <tr style="font-size:13px;color:#7f8c8d">
                            <th style="padding:8px 6px">الطالب</th>
                            <th style="padding:8px 6px">الحدث</th>
                            <th style="padding:8px 6px">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['recent_activities'] as $act)
                            <tr style="border-top:1px solid #eef1f4">
                                <td style="padding:8px 6px">{{ $act->student_id }}</td>
                                <td style="padding:8px 6px">تم تسجيل نتيجة</td>
                                <td style="padding:8px 6px">{{ \Carbon\Carbon::parse($act->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color:#7f8c8d">لا توجد نشاطات حديثة ضمن الفلاتر الحالية.</p>
        @endif
    </div>

    <p style="font-size:12px;color:#7f8c8d;margin-top:10px">
        ⓘ “متوسط الإجمالي” = متوسط مجموع درجات محاور الذكاء لكل محاولة ÷ عدد المحاور.
        (لا يوجد تمييز قبلي/بعدي في قاعدة البيانات الحالية).
    </p>
@endsection
