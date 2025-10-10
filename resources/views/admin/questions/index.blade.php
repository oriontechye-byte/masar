@extends('admin.layouts.admin')

@section('title', 'إدارة أسئلة الاختبار')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<style>
    :root{
        --brand:#e67e22; --brand2:#f39c12;
        --ink:#2c3e50; --muted:#7f8c8d;
        --bg:#f7f9fc; --card:#fff; --line:#eef1f4;
        --shadow:0 12px 36px rgba(0,0,0,.08);
    }
    body.dark-mode{
        --bg:#0f1115; --card:#151821; --ink:#e9edf2; --muted:#b6bdc6; --line:#1f2430;
        --shadow:0 18px 42px rgba(0,0,0,.45);
    }
    body{font-family:'Cairo',sans-serif; background:var(--bg); color:var(--ink)}

    /* ====== Card ====== */
    .msr-card{background:var(--card); border:1px solid var(--line); border-radius:16px; box-shadow:var(--shadow)}
    .msr-head{padding:14px 16px; border-bottom:1px solid var(--line); display:flex; align-items:center; justify-content:space-between; gap:12px}
    .msr-title{margin:0; font-weight:800; display:flex; align-items:center; gap:.6rem}
    .msr-body{padding:16px}

    /* ====== Buttons ====== */
    .msr-btn{border-radius:999px; font-weight:800; padding:.55rem 1rem; border:2px solid transparent;
        display:inline-flex; align-items:center; gap:.5rem; transition:all .2s ease; cursor:pointer; text-decoration:none}
    .msr-btn i{font-size:1rem}
    .msr-btn-primary{background:linear-gradient(90deg,var(--brand2),var(--brand)); color:#fff}
    .msr-btn-primary:hover{filter:brightness(.95); transform:translateY(-2px)}
    .msr-btn-light{background:#fff; color:var(--ink); border-color:#e6e8ec}
    body.dark-mode .msr-btn-light{background:#1a1f2b; border-color:#273041; color:#e9edf2}
    .msr-btn-muted{background:#f3f4f6; color:#374151; border-color:#e5e7eb}
    body.dark-mode .msr-btn-muted{background:#1c2130; color:#dbe2ea; border-color:#283246}
    .msr-btn-danger{background:#dc3545; color:#fff}
    .msr-btn-warning{background:#ffc107; color:#111}

    /* ====== Tools / Filters ====== */
    .tools{display:flex; gap:10px; flex-wrap:wrap}
    .tools .input-group{display:flex; align-items:stretch; flex-direction:row-reverse}
    .tools .input-group > .form-control, .tools .input-group > .custom-select{border-color:#e6e8ec; border-radius:0 12px 12px 0; height:44px}
    .tools .input-group > .custom-select{padding-inline:12px}
    .tools .input-group-text{background:#f7f8fa; border-color:#e6e8ec; border-radius:12px 0 0 12px}
    .tools .form-control:focus, .tools .custom-select:focus{box-shadow:0 0 0 .15rem rgba(230,126,34,.15); border-color:#e67e22}

    /* ====== Table ====== */
    .table-wrap{overflow:auto}
    table.table{margin:0}
    .table thead th{background:#fafafa; border-bottom:1px solid var(--line); font-weight:800}
    body.dark-mode .table thead th{background:#1b2030}
    .table td, .table th{vertical-align:middle}
    tr:hover{background:rgba(0,0,0,.02)}
    body.dark-mode tr:hover{background:#121620}
    .badge-chip{border-radius:999px; padding:.35rem .6rem; font-weight:800; background:#eef2ff; color:#4153a3; white-space:nowrap}
    body.dark-mode .badge-chip{background:#232a3a; color:#9fb0ff}

    /* ====== Pagination (Laravel default markup) ====== */
    .pagination{display:flex; justify-content:center; margin:16px 0; list-style:none; gap:6px}
    .pagination li a, .pagination li span{padding:.55rem .85rem; border:1px solid var(--line); border-radius:10px;
        background:var(--card); color:var(--ink); text-decoration:none; display:inline-block; min-width:38px; text-align:center}
    .pagination li a:hover{box-shadow:0 6px 18px rgba(0,0,0,.08)}
    .pagination li.active span{background:linear-gradient(135deg,var(--brand2),var(--brand)); color:#fff; border-color:transparent}
    .pagination li.disabled span{opacity:.6}

    /* ====== Alerts ====== */
    .alert{border-radius:12px; padding:12px 14px; border:1px solid; margin-bottom:16px}
    .alert-success{background:#eafaf0; border-color:#bfe8cb; color:#22693b}
    body.dark-mode .alert-success{background:#14301f; border-color:#305d42; color:#b9f2cf}
</style>
@endpush

@section('content')
    {{-- رأس الصفحة --}}
    <div class="msr-card mb-3" dir="rtl">
        <div class="msr-head">
            <h4 class="msr-title"><i class="fa-solid fa-list-check text-warning"></i> إدارة أسئلة الاختبار</h4>
            <div class="tools">
                <a href="{{ route('admin.questions.create') }}" class="msr-btn msr-btn-primary">
                    <i class="fa-solid fa-plus"></i> إضافة سؤال
                </a>
                <a href="{{ route('admin.questions.index') }}" class="msr-btn msr-btn-light" title="تحديث">
                    <i class="fa-solid fa-rotate"></i> تحديث
                </a>
            </div>
        </div>

        {{-- فلاتر سريعة --}}
        <div class="msr-body">
            <form action="{{ route('admin.questions.index') }}" method="GET" class="tools" dir="rtl">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="ابحث في نص السؤال" value="{{ request('q') }}">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                </div>

                <div class="input-group">
                    <select name="type_id" class="custom-select">
                        <option value="">كل الأنواع</option>
                        @foreach($types ?? [] as $t)
                            <option value="{{ $t->id }}" {{ request('type_id')==$t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <span class="input-group-text"><i class="fa-solid fa-brain"></i></span>
                </div>

                <div class="input-group">
                    <select name="per_page" class="custom-select">
                        @foreach([10,20,30,50,100] as $pp)
                            <option value="{{ $pp }}" {{ (int)request('per_page', $questions->perPage()) === $pp ? 'selected' : '' }}>{{ $pp }} / صفحة</option>
                        @endforeach
                    </select>
                    <span class="input-group-text"><i class="fa-solid fa-list-ol"></i></span>
                </div>

                <button class="msr-btn msr-btn-primary" type="submit"><i class="fa-solid fa-sliders"></i> تطبيق</button>
                <a href="{{ route('admin.questions.index') }}" class="msr-btn msr-btn-muted"><i class="fa-solid fa-eraser"></i> مسح</a>
            </form>
        </div>
    </div>

    {{-- فلاش --}}
    @if(session('success'))
        <div class="alert alert-success" dir="rtl">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- الجدول --}}
    <div class="msr-card" dir="rtl">
        <div class="msr-body table-wrap" style="padding:0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead>
                        <tr class="text-right">
                            <th style="width:60px">#</th>
                            <th>نص السؤال</th>
                            <th style="width:220px">نوع الذكاء</th>
                            <th style="width:200px" class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $q)
                            <tr>
                                <td>{{ $q->id }}</td>
                                <td>{{ $q->text }}</td>
                                <td>
                                    <span class="badge-chip"><i class="fa-solid fa-brain"></i> {{ $q->type_name ?? ($q->type->name ?? '—') }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.questions.edit', $q->id) }}" class="msr-btn msr-btn-warning" title="تعديل">
                                        <i class="fa-solid fa-pen-to-square"></i> تعديل
                                    </a>
                                    <form action="{{ route('admin.questions.destroy', $q->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا السؤال؟');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="msr-btn msr-btn-danger">
                                            <i class="fa-solid fa-trash-can"></i> حذف
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">لا توجد أسئلة حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $questions->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
