@extends('admin.layouts.admin')

@section('title', 'إدارة الطلاب')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>
    :root{
        --brand:#e67e22; --brand2:#f39c12;
        --ink:#2c3e50; --muted:#7f8c8d;
        --bg:#f8f9fa; --card:#ffffff; --line:#e9edf3;
        --input-bg:#ffffff; --input-bd:#e6e8ec; --input-ink:#2c3e50; --input-ph:#9aa3ad;
        --table-head:#fafafa; --chip:#f3f4f6; --chip-bd:#e5e7eb;
        --shadow:0 10px 30px rgba(0,0,0,.06);
    }
    body.dark-mode{
        --bg:#0f1115; --card:#151821; --line:#1f2430;
        --ink:#e9edf2; --muted:#b6bdc6;
        --input-bg:#141a23; --input-bd:#253043; --input-ink:#e9edf2; --input-ph:#8d98a7;
        --table-head:#121720; --chip:#1b2230; --chip-bd:#273041;
        --shadow:0 14px 38px rgba(0,0,0,.42);
    }

    body{font-family:'Cairo',sans-serif;background:var(--bg);}

    /* ===== Toolbar ===== */
    .msr-toolbar-wrap{position:sticky; top:0; z-index:9;}
    .msr-toolbar{
        background:var(--card); border-radius:16px; padding:12px 16px; margin-bottom:18px;
        display:flex; align-items:center; justify-content:space-between; gap:12px;
        box-shadow:var(--shadow); border:1px solid var(--line);
    }
    .msr-btn{
        border-radius:999px; font-weight:700; padding:.58rem 1rem; border:2px solid transparent;
        display:inline-flex; align-items:center; gap:.5rem; transition:all .2s ease; cursor:pointer;
    }
    .msr-btn i{font-size:1rem}
    .msr-btn-light{background:var(--card); border-color:var(--line); color:var(--ink)}
    .msr-btn-light:hover{transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.10)}
    .msr-btn-outline{background:transparent; color:var(--brand); border-color:var(--brand)}
    .msr-btn-outline:hover{background:var(--brand); color:#fff; box-shadow:0 8px 24px rgba(230,126,34,.35)}
    .msr-btn-primary{
        background:linear-gradient(90deg,var(--brand2),var(--brand)); color:#fff; border-color:transparent
    }
    .msr-btn-primary:hover{filter:brightness(.95); transform:translateY(-2px)}
    .msr-btn-muted{background:var(--chip); color:#374151; border:1px solid var(--chip-bd)}
    body.dark-mode .msr-btn-muted{color:var(--ink)}
    .msr-btn-muted:hover{filter:brightness(.98)}

    .msr-chip{
        background:var(--chip); color:var(--ink); border:1px solid var(--chip-bd);
        padding:.28rem .65rem; border-radius:999px; font-weight:700; font-size:.85rem; display:inline-flex; gap:.35rem; align-items:center
    }
    .msr-chip i{font-size:.85rem}

    /* ===== Cards ===== */
    .msr-card{
        background:var(--card); border-radius:16px; box-shadow:var(--shadow); border:1px solid var(--line);
    }
    .msr-card-head{padding:14px 16px; border-bottom:1px solid var(--line)}
    .msr-card-body{padding:16px}
    .msr-card-title{font-weight:800; color:var(--ink); margin:0; display:flex; align-items:center; gap:.5rem}

    /* ===== Filters Grid ===== */
    .msr-grid{display:grid; grid-template-columns:repeat(12,1fr); gap:14px;}
    .col-2{grid-column:span 2} .col-3{grid-column:span 3} .col-4{grid-column:span 4} .col-12{grid-column:span 12}
    @media (max-width: 992px){ .col-2,.col-3,.col-4{grid-column:span 6} }
    @media (max-width: 600px){ .col-2,.col-3,.col-4{grid-column:span 12} }

    .msr-field label{font-weight:800; color:var(--ink); margin-bottom:6px; display:block}

    /* ===== Inputs (consistent sizes) ===== */
    .input-group{display:flex; align-items:center; width:100%}
    .input-group-text{
        background:var(--chip); color:#6b7280; border:1px solid var(--input-bd);
        width:44px; min-width:44px; height:44px; display:flex; align-items:center; justify-content:center;
        border-radius:10px 0 0 10px;
    }
    [dir="rtl"] .input-group-text{border-radius:0 10px 10px 0}

    .form-control, .custom-select{
        height:44px; border-radius:10px; border:1px solid var(--input-bd);
        background:var(--input-bg); color:var(--input-ink); padding-inline:12px;
        outline:none; transition:box-shadow .15s, border-color .15s;
    }
    .form-control::placeholder{color:var(--input-ph)}
    .form-control:focus, .custom-select:focus{
        box-shadow:0 0 0 .15rem rgba(230,126,34,.18); border-color:var(--brand);
    }
    /* attach with icon (no double radius) */
    .input-group .form-control, .input-group .custom-select{
        border-radius:10px 0 0 10px; width:100%;
    }
    [dir="rtl"] .input-group .form-control, [dir="rtl"] .input-group .custom-select{
        border-radius:0 10px 10px 0;
    }

    /* Remove inner arrow crowding on selects across browsers */
    .custom-select{appearance:none; -webkit-appearance:none; -moz-appearance:none; background-image:none}
    /* add our own tiny chevron on left */
    .select-wrap{position:relative; width:100%}
    .select-wrap .chev{
        position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9aa3ad; pointer-events:none;
    }
    [dir="rtl"] .select-wrap .chev{left:auto; right:10px}

    /* spacing helpers for icon + control */
    .ig{display:grid; grid-template-columns:auto 1fr; align-items:center}
    .ig .input-group-text{border-inline-end:0}
    .ig .form-control, .ig .custom-select{border-inline-start:0}

    /* ===== Table ===== */
    .table-wrap{overflow:auto; padding:0}
    .table{margin:0}
    .table thead th{
        background:var(--table-head); font-weight:800; color:var(--ink); border-bottom:1px solid var(--line);
    }
    .table tbody tr:hover{background:rgba(0,0,0,.02)}
    body.dark-mode .table tbody tr:hover{background:rgba(255,255,255,.03)}
    .table td, .table th{vertical-align:middle; border-color:var(--line); color:var(--ink)}

    .badge{border-radius:999px; font-weight:800; padding:.35rem .6rem}
    .badge-success{background:#e9f7ef; color:#2e7d32}
    .badge-primary{background:#e3f2fd; color:#1e88e5}
    .badge-warning{background:#fff3cd; color:#7a5d00}
    .badge-danger{background:#fdecea; color:#c62828}

    /* action cell */
    .action-buttons .msr-btn{padding:.45rem .8rem}
</style>
@endpush

@section('content')

@php
    $filters = [
        'q' => request('q'),
        'governorate' => request('governorate'),
        'start_date' => request('start_date'),
        'end_date' => request('end_date'),
        'post_test_allowed' => request('post_test_allowed'),
        'sort' => request('sort') && request('sort') !== 'latest' ? request('sort') : null,
        'per_page' => (int)request('per_page',20) !== 20 ? request('per_page') : null,
    ];
    $activeFilters = collect($filters)->filter(fn($v)=>filled($v))->count();
@endphp

{{-- Toolbar --}}
<div class="msr-toolbar-wrap" dir="rtl">
    <div class="msr-toolbar">
        <div class="d-flex align-items-center" style="gap:10px">
            <a href="{{ url()->current() }}" class="msr-btn msr-btn-light" title="تحديث">
                <i class="fa-solid fa-rotate-right"></i> تحديث
            </a>

            <span class="msr-chip" title="عدد النتائج">
                <i class="fa-solid fa-list"></i> {{ number_format($students->total()) }} نتيجة
            </span>
            <span class="msr-chip" title="الفلاتر المفعّلة">
                <i class="fa-solid fa-filter"></i> {{ $activeFilters }} فلتر
            </span>
        </div>

        <div class="d-flex" style="gap:10px">
            <button class="msr-btn msr-btn-outline" type="button" id="toggleFilters">
                <i class="fa-solid fa-sliders"></i> إظهار/إخفاء الفلاتر
            </button>
        </div>
    </div>
</div>

{{-- Filters + export --}}
<div class="msr-card mb-3" dir="rtl">
    <div class="msr-card-head">
        <h5 class="msr-card-title"><i class="fa-solid fa-filter text-warning"></i> فلترة وتصدير البيانات</h5>
    </div>

    <div class="msr-card-body" id="filtersPanel">
        <form action="{{ route('admin.students.index') }}" method="GET" class="mb-3">
            <div class="msr-grid">
                {{-- search --}}
                <div class="msr-field col-4">
                    <label for="q">بحث (اسم / واتساب / بريد)</label>
                    <div class="ig">
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="q" id="q" class="form-control"
                               value="{{ request('q') }}" placeholder="اكتب جزءًا من الاسم أو الرقم">
                    </div>
                </div>

                {{-- governorate --}}
                <div class="msr-field col-3">
                    <label for="governorate">المحافظة</label>
                    <div class="ig select-wrap">
                        <span class="input-group-text"><i class="fa-solid fa-location-dot"></i></span>
                        <select name="governorate" id="governorate" class="custom-select">
                            <option value="">كل المحافظات</option>
                            @foreach($governorates as $gov)
                                <option value="{{ $gov }}" {{ request('governorate') == $gov ? 'selected' : '' }}>{{ $gov }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down chev"></i>
                    </div>
                </div>

                {{-- dates --}}
                <div class="msr-field col-2">
                    <label for="start_date">من تاريخ</label>
                    <div class="ig">
                        <span class="input-group-text"><i class="fa-solid fa-calendar-day"></i></span>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                               value="{{ request('start_date') }}">
                    </div>
                </div>

                <div class="msr-field col-2">
                    <label for="end_date">إلى تاريخ</label>
                    <div class="ig">
                        <span class="input-group-text"><i class="fa-solid fa-calendar-check"></i></span>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                               value="{{ request('end_date') }}">
                    </div>
                </div>

                {{-- post-test allow --}}
                <!-- <div class="msr-field col-3">
                    <label for="post_test_allowed">سماح البعدي</label>
                    <div class="ig select-wrap">
                        <span class="input-group-text"><i class="fa-solid fa-user-check"></i></span>
                        <select name="post_test_allowed" id="post_test_allowed" class="custom-select">
                            <option value="">الكل</option>
                            <option value="1" {{ request('post_test_allowed')==='1' ? 'selected' : '' }}>مسموح</option>
                            <option value="0" {{ request('post_test_allowed')==='0' ? 'selected' : '' }}>غير مسموح</option>
                        </select>
                        <i class="fa-solid fa-chevron-down chev"></i>
                    </div>
                </div> -->

                {{-- sort --}}
                <div class="msr-field col-3">
                    <label for="sort">ترتيب حسب</label>
                    <div class="ig select-wrap">
                        <span class="input-group-text"><i class="fa-solid fa-arrow-down-wide-short"></i></span>
                        <select name="sort" id="sort" class="custom-select">
                            <option value="latest"     {{ request('sort','latest')==='latest' ? 'selected' : '' }}>الأحدث أولًا</option>
                            <option value="name_az"    {{ request('sort')==='name_az' ? 'selected' : '' }}>الاسم A→Z</option>
                            <option value="grade_desc" {{ request('sort')==='grade_desc' ? 'selected' : '' }}>المعدل (تنازلي)</option>
                            <option value="grade_asc"  {{ request('sort')==='grade_asc' ? 'selected' : '' }}>المعدل (تصاعدي)</option>
                        </select>
                        <i class="fa-solid fa-chevron-down chev"></i>
                    </div>
                </div>

                {{-- per page --}}
                <div class="msr-field col-2">
                    <label for="per_page">عدد بالسطر</label>
                    <div class="ig select-wrap">
                        <span class="input-group-text"><i class="fa-solid fa-list-ol"></i></span>
                        <select name="per_page" id="per_page" class="custom-select">
                            @foreach([10,15,20,25,50,100] as $n)
                                <option value="{{ $n }}" {{ (int)request('per_page',20)===$n ? 'selected' : '' }}>{{ $n }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down chev"></i>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex align-items-center" style="gap:10px; flex-wrap:wrap">
                        <button type="submit" class="msr-btn msr-btn-primary">
                            <i class="fa-solid fa-magnifying-glass"></i> تصفية
                        </button>
                        <a href="{{ route('admin.students.index') }}" class="msr-btn msr-btn-muted">
                            <i class="fa-solid fa-eraser"></i> مسح
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <hr>

        {{-- export --}}
        <form action="{{ route('admin.students.export') }}" method="GET"
              class="d-flex align-items-center" style="gap:10px; flex-wrap:wrap">
            <input type="hidden" name="q" value="{{ request('q') }}">
            <input type="hidden" name="governorate" value="{{ request('governorate') }}">
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            <input type="hidden" name="post_test_allowed" value="{{ request('post_test_allowed') }}">
            <input type="hidden" name="sort" value="{{ request('sort','latest') }}">

            <button type="submit" name="test_type" value="pre" class="msr-btn msr-btn-outline" title="CSV">
                <i class="fa-solid fa-file-csv"></i> تصدير نتائج الاختبار القبلي
            </button>

            <button type="submit" name="test_type" value="post" class="msr-btn msr-btn-outline" title="CSV">
                <i class="fa-solid fa-file-csv"></i> تصدير نتائج الاختبار البعدي (مع المهن)
            </button>
            <small class="text-muted d-block" style="margin-inline-start:4px">* يتم التصدير بحسب الفلاتر الحالية</small>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="msr-card table-wrap" dir="rtl">
    <div class="msr-card-body" style="padding:0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>الاسم</th>
                        <th style="width:170px">رقم الواتساب</th>
                        <th style="width:120px">المحافظة</th>
                        <th style="width:120px">المعدل</th>
                        <th style="width:140px">تاريخ التسجيل</th>
                        <th style="width:160px">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($students as $i => $student)
                    <tr>
                        <td>{{ $students->firstItem() + $i }}</td>
                        <td class="font-weight-bold">{{ $student->name ?? '—' }}</td>
                        <td class="text-monospace" dir="ltr">{{ $student->whatsapp }}</td>
                        <td>{{ $student->governorate ?? '—' }}</td>
                        <td>
                            @php $g = is_null($student->grade) ? null : round($student->grade,2); @endphp
                            @if($g === null)
                                —
                            @else
                                <span class="badge {{ $g>=95 ? 'badge-success' : ($g>=85 ? 'badge-primary' : ($g>=70 ? 'badge-warning' : 'badge-danger')) }}">
                                    {{ number_format($g,2) }}%
                                </span>
                            @endif
                        </td>
                        <td>{{ optional($student->created_at)->format('Y-m-d') }}</td>
                        <td class="action-buttons">
                            <a href="{{ route('admin.students.show', $student->id) }}"
                               class="msr-btn msr-btn-light" title="عرض">
                                <i class="fa-solid fa-eye"></i> عرض
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">لا يوجد طلاب لعرضهم بناءً على الفلاتر المحددة.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center p-3">
            {{ $students->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
  // يعمل حتى لو ما عندك Bootstrap JS
  document.getElementById('toggleFilters')?.addEventListener('click', function(){
    const p = document.getElementById('filtersPanel');
    if (!p) return;
    p.style.display = (p.style.display === 'none') ? '' : 'none';
  });
</script>
@endpush
