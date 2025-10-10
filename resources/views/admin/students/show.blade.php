@extends('admin.layouts.admin')

@section('title', 'تفاصيل الطالب: ' . ($student->name ?? '—'))

@push('styles')
    {{-- Cairo + أيقونات مثل صفحة الهبوط --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        :root{
            --brand:#e67e22;
            --brand-2:#f39c12;
            --ink:#2c3e50;
            --muted:#7f8c8d;
            --bg:#f8f9fa;
        }
        body{ font-family:'Cairo',sans-serif; background:var(--bg); }

        /* ======= شريط علوي (Sticky) ======= */
        .msr-toolbar-wrap{ position:sticky; top:0; z-index:9; }
        .msr-toolbar{
            display:flex; justify-content:space-between; gap:12px; align-items:center; margin-bottom:18px;
            background:#fff; padding:10px 14px; border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,.06);
        }

        /* ======= أزرار ======= */
        .msr-btn{
            border-radius: 999px; font-weight:700; padding:.6rem 1.1rem;
            border:2px solid transparent; display:inline-flex; align-items:center; gap:.5rem;
            transition:all .25s ease; cursor:pointer;
        }
        .msr-btn i{ font-size:1rem }
        .msr-btn-light{ background:#fff; border-color:#eee; color:var(--ink) }
        .msr-btn-light:hover{ transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.08) }
        .msr-btn-outline{ background:transparent; color:var(--brand); border-color:var(--brand) }
        .msr-btn-outline:hover{ background:var(--brand); color:#fff; box-shadow:0 8px 24px rgba(230,126,34,.35) }
        .msr-btn-muted{ background:#f3f4f6; color:#374151; border-color:#e5e7eb }
        .msr-btn-muted:hover{ background:#e5e7eb }

        /* ======= بطاقات ======= */
        .msr-card{ border:0; border-radius:20px; box-shadow:0 12px 40px rgba(0,0,0,.08); overflow:hidden }
        .msr-card .card-header{ background:#fff; border-bottom:1px solid #f0f0f0 }
        .msr-card .card-header h5{ font-weight:800; color:var(--ink) }

        /* شارة الحالة */
        .msr-badge{ border-radius:999px; padding:.35rem .7rem; font-weight:700 }
        .msr-badge--ok{ background:#e9f7ef; color:#2e7d32 }
        .msr-badge--off{ background:#fdecea; color:#c62828 }

        /* ======= Progress (مع أنيميشن) ======= */
        .msr-progress{ height:10px; background:#eee; border-radius:999px; overflow:hidden }
        .msr-progress .progress-bar{
            background: linear-gradient(90deg,var(--brand-2),var(--brand));
            width:0%; transition: width .9s cubic-bezier(.2,.7,.2,1);
        }
        .msr-progress--muted .progress-bar{ background:#cfd8dc }

        /* ======= Top 3 ======= */
        .msr-top3 .top-card{
            position:relative; border:0; border-radius:18px; box-shadow:0 10px 30px rgba(0,0,0,.06);
            overflow:hidden; background:#fff;
        }
        .msr-top3 .top-card .accent{ position:absolute; right:0; top:0; bottom:0; width:6px; opacity:.9 }
        .msr-top3 .rank-badge{
            border-radius:999px; padding:.25rem .6rem; font-weight:800; background:#fff3cd; color:#7a5d00;
        }

        .text-ink{ color:var(--ink) }
        .text-muted-2{ color:var(--muted) }

        /* جدول أنيق */
        .table thead th{ font-weight:800; color:var(--ink) }
        .table td, .table th{ vertical-align:middle }

        /* نسخ الواتساب */
        .copy-chip{
            display:inline-flex; align-items:center; gap:.4rem; font-weight:700;
            border-radius:999px; padding:.25rem .6rem; background:#f3f4f6; cursor:pointer;
        }
        .copy-chip:hover{ background:#e5e7eb }

        /* Responsive */
        @media (max-width: 576px){
            .msr-toolbar{ flex-direction:column; align-items:stretch }
            .msr-toolbar .btn-group{ width:100% }
            .msr-toolbar .btn-group .msr-btn{ flex:1; justify-content:center }
        }
    </style>
@endpush

@section('content')
    {{-- شريط علوي ثابت --}}
    <div class="msr-toolbar-wrap">
        <div class="msr-toolbar">
            <a href="{{ route('admin.students.index') }}" class="msr-btn msr-btn-light">
                <i class="fa-solid fa-arrow-right"></i> العودة إلى قائمة الطلاب
            </a>

            <div class="btn-group">
                <a href="{{ route('results.show', $student->id) }}" class="msr-btn msr-btn-outline">
                    <i class="fa-solid fa-chart-column"></i> عرض النتائج
                </a>
                <a href="{{ route('growth.report', $student->id) }}" class="msr-btn msr-btn-outline">
                    <i class="fa-solid fa-chart-line"></i> تقرير التطوّر
                </a>
            </div>
        </div>
    </div>

    {{-- فلاش --}}
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif
    @if(session('info'))    <div class="alert alert-info">{{ session('info') }}</div>      @endif

    @php
        use Illuminate\Support\Facades\Schema;

        $hasCol  = Schema::hasColumn('students','post_test_allowed');
        $allowed = $hasCol ? (bool)($student->post_test_allowed ?? false) : null;

        $result = $result ?? ($student->testResult ?? null);

        $slugById = [
            1 => 'social', 2 => 'visual', 3 => 'intrapersonal', 4 => 'kinesthetic',
            5 => 'logical', 6 => 'naturalist', 7 => 'linguistic', 8 => 'musical',
        ];
        $clamp = fn($v) => is_null($v) ? null : max(0, min(100, (int)$v));

        $preRows  = [];
        $postRows = [];
        $hasAnyPost = false;

        foreach ($intelligenceTypes as $id => $type) {
            $slug = $slugById[$id] ?? null;
            if (!$slug) continue;

            $pre  = $clamp($result->{'score_'.$slug}  ?? null);
            $post = $clamp($result->{'post_score_'.$slug} ?? null);

            $preRows[]  = ['id'=>$id,'name'=>$type->name ?? ('ID '.$id),'val'=>$pre];
            $postRows[] = ['id'=>$id,'name'=>$type->name ?? ('ID '.$id),'val'=>$post];

            if(!is_null($post)) $hasAnyPost = true;
        }

        $postSorted = collect($postRows)->filter(fn($r)=>!is_null($r['val']))->sortByDesc('val')->values()->all();
        $top3 = array_slice($postSorted, 0, 3);
        $accents = ['#fcd34d','#93c5fd','#d8b4fe']; // أصفر/أزرق/بنفسجي فاتح
    @endphp

    {{-- بطاقة: البيانات الشخصية --}}
    <div class="card msr-card mb-4" dir="rtl">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa-solid fa-id-card-clip text-warning"></i> &nbsp;البيانات الشخصية</h5>

            @if(!is_null($allowed))
                <div class="d-flex align-items-center">
                    <span class="msr-badge {{ $allowed ? 'msr-badge--ok' : 'msr-badge--off' }} mr-2">
                        {{ $allowed ? 'السماح بالاختبار البعدي: مفعّل' : 'السماح بالاختبار البعدي: موقّف' }}
                    </span>
                    <form action="{{ route('admin.students.toggle_post_test', $student->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit"
                                class="msr-btn msr-btn-outline"
                                title="{{ $allowed ? 'إيقاف السماح' : 'السماح بالبعدي' }}">
                            <i class="fa-{{ $allowed ? 'solid fa-ban' : 'solid fa-check' }}"></i>
                            {{ $allowed ? 'إيقاف السماح' : 'السماح بالبعدي' }}
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="card-body p-0">
            <table class="table mb-0">
                <tbody>
                    <tr>
                        <th style="width:220px;">الاسم الكامل</th>
                        <td class="text-ink">{{ $student->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>رقم الواتساب</th>
                        <td>
                            <span class="text-monospace" dir="ltr">{{ $student->whatsapp ?? '—' }}</span>
                            @if(!empty($student->whatsapp))
                                <button class="msr-btn msr-btn-muted msr-btn-sm copy-chip"
                                        data-copy="{{ $student->whatsapp }}"
                                        title="نسخ الرقم">
                                    <i class="fa-regular fa-copy"></i><span>نسخ</span>
                                </button>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>البريد الإلكتروني</th>
                        <td class="text-muted-2">{{ $student->email ?? 'لم يتم إدخاله' }}</td>
                    </tr>
                    <tr>
                        <th>المحافظة</th>
                        <td>{{ $student->governorate ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>المعدل وسنة التخرج</th>
                        <td>
                            @php $g = is_null($student->grade ?? null) ? null : round($student->grade,2); @endphp
                            <strong>{{ is_null($g) ? '—' : number_format($g,2) . '%' }}</strong>
                            @if(!empty($student->graduation_year))
                                <span class="text-muted-2"> — سنة {{ $student->graduation_year }}</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- أعلى 3 ذكاءات (بعديًا) --}}
    @if($hasAnyPost && count($top3))
        <div class="msr-top3 mb-4" dir="rtl">
            <h5 class="mb-3 text-ink"><i class="fa-solid fa-trophy text-warning"></i> أعلى 3 ذكاءات (بعديًا)</h5>
            <div class="row">
                @foreach($top3 as $rank => $r)
                    @php
                        $percent = (int) $r['val'];
                        $rankNo  = $rank + 1;
                        $accent  = $accents[$rank] ?? '#e5e7eb';
                    @endphp
                    <div class="col-md-4 mb-3">
                        <div class="card top-card h-100">
                            <div class="accent" style="background: {{ $accent }}"></div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 text-ink" style="font-weight:800">{{ $r['name'] }}</h6>
                                    <span class="rank-badge"><i class="fa-solid fa-crown"></i> #{{ $rankNo }}</span>
                                </div>

                                <div class="d-flex align-items-center">
                                    <div class="progress msr-progress flex-grow-1">
                                        <div class="progress-bar js-animate-width"
                                             data-target="{{ $percent }}"
                                             role="progressbar"
                                             aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="ml-2 font-weight-bold">{{ $percent }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- نتائج الاختبارات (قبلي/بعدي) --}}
    <div class="card msr-card" dir="rtl">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa-solid fa-poll"></i> نتائج الاختبارات</h5>
        </div>

        <div class="card-body p-0">
            @if ($result)
                <table class="table table-bordered mb-0 text-center align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-right" style="width:30%">نوع الذكاء</th>
                            <th style="width:35%">النتيجة القبلية</th>
                            <th style="width:35%">النتيجة البعدية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($intelligenceTypes as $id => $type)
                            @php
                                $pre  = collect($preRows)->firstWhere('id',$id)['val']  ?? null;
                                $post = collect($postRows)->firstWhere('id',$id)['val'] ?? null;

                                $prew  = is_null($pre)  ? 0 : (int)$pre;
                                $postw = is_null($post) ? 0 : (int)$post;

                                $preLabel  = is_null($pre)  ? 'N/A'         : ($prew.'%');
                                $postLabel = is_null($post) ? 'لم يُجرَ بعد' : ($postw.'%');

                                $isTop = $hasAnyPost && in_array($id, array_column($top3,'id'));
                            @endphp
                            <tr class="{{ $isTop ? 'table-success' : '' }}">
                                <td class="text-right">
                                    <strong>{{ $type->name }}</strong>
                                    @if($isTop)
                                        <span class="badge badge-success mr-1"><i class="fa-solid fa-trophy"></i> TOP 3</span>
                                    @endif
                                </td>

                                {{-- قبلي --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress msr-progress msr-progress--muted flex-grow-1">
                                            <div class="progress-bar js-animate-width" data-target="{{ $prew }}"
                                                 role="progressbar"></div>
                                        </div>
                                        <span class="ml-2 font-weight-bold">{{ $preLabel }}</span>
                                    </div>
                                </td>

                                {{-- بعدي --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress msr-progress flex-grow-1">
                                            <div class="progress-bar js-animate-width" data-target="{{ $postw }}"
                                                 role="progressbar"></div>
                                        </div>
                                        <span class="ml-2 font-weight-bold">{{ $postLabel }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-warning mb-0 text-center">
                    لم يقم هذا الطالب بإجراء أي اختبار بعد.
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // أنيميشن عرض الـ progress عند ظهورها
    const inView = (el) => {
        const r = el.getBoundingClientRect();
        return r.top < (window.innerHeight - 60) && r.bottom > 0;
    };
    function animateBars(){
        document.querySelectorAll('.js-animate-width').forEach(bar=>{
            const target = parseInt(bar.getAttribute('data-target') || '0', 10);
            if(!bar.dataset.done && inView(bar)){
                bar.dataset.done = '1';
                requestAnimationFrame(()=> bar.style.width = (target || 0) + '%');
            }
        });
    }
    document.addEventListener('scroll', animateBars, {passive:true});
    window.addEventListener('load', animateBars);

    // نسخ رقم الواتساب
    document.querySelectorAll('.copy-chip').forEach(btn=>{
        btn.addEventListener('click', async ()=>{
            try{
                await navigator.clipboard.writeText(btn.dataset.copy);
                btn.classList.add('copied');
                const old = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check"></i><span>تم النسخ</span>';
                setTimeout(()=>{ btn.innerHTML = old; btn.classList.remove('copied'); }, 1200);
            }catch(e){}
        });
    });
</script>
@endpush
