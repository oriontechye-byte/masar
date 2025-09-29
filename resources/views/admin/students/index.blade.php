@extends('admin.layouts.admin')

@section('title', 'إدارة الطلاب')

@section('content')
<style>
    .filter-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 25px; }
    .filter-card h5 { margin-bottom: 20px; font-weight: bold; color: #333; }
    .table-responsive { background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 20px; }
    .table th, .table td { vertical-align: middle; }
    .btn-icon-split { display: inline-flex; align-items: center; justify-content: center; }
    .btn-icon-split .icon { padding: .375rem .5rem; }
    .btn-icon-split .text { padding: .375rem .75rem; }
    .action-buttons a, .action-buttons button { margin-left: 5px; }
    .export-buttons button { margin-right: 10px; }
</style>

<div class="filter-card">
    <h5><i class="fas fa-filter"></i> فلترة وتصدير البيانات</h5>
    
    <form action="{{ route('admin.students.index') }}" method="GET" class="mb-4">
        <div class="row align-items-end">
            <div class="col-md-3">
                <label for="governorate">المحافظة</label>
                <select name="governorate" id="governorate" class="form-control">
                    <option value="">كل المحافظات</option>
                    @foreach($governorates as $gov)
                        <option value="{{ $gov }}" {{ request('governorate') == $gov ? 'selected' : '' }}>{{ $gov }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="start_date">من تاريخ</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label for="end_date">إلى تاريخ</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-icon-split">
                    <span class="icon"><i class="fas fa-search"></i></span>
                    <span class="text">بحث</span>
                </button>
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-icon-split">
                    <span class="icon"><i class="fas fa-sync-alt"></i></span>
                    <span class="text">إلغاء</span>
                </a>
            </div>
        </div>
    </form>
    
    <hr>

    <form action="{{ route('admin.students.export') }}" method="GET" class="export-buttons">
        <input type="hidden" name="governorate" value="{{ request('governorate') }}">
        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
        <input type="hidden" name="end_date" value="{{ request('end_date') }}">

        <button type="submit" name="test_type" value="pre" class="btn btn-success btn-icon-split">
            <span class="icon"><i class="fas fa-file-excel"></i></span>
            <span class="text">تصدير نتائج الاختبار القبلي</span>
        </button>

        <button type="submit" name="test_type" value="post" class="btn btn-info btn-icon-split">
            <span class="icon"><i class="fas fa-file-excel"></i></span>
            <span class="text">تصدير نتائج الاختبار البعدي (مع المهن)</span>
        </button>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover" style="width:100%">
        <thead class="thead-light">
            <tr>
                <th>الاسم</th>
                <th>رقم الواتساب</th>
                <th>المحافظة</th>
                <th>المعدل</th>
                <th>تاريخ التسجيل</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
            <tr>
                <td>{{ $student->name }}</td>
                <td>{{ $student->whatsapp_number }}</td>
                <td>{{ $student->governorate }}</td>
                <td>{{ $student->gpa }}%</td>
                <td>{{ $student->created_at->format('Y-m-d') }}</td>
                <td class="action-buttons">
                    <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> عرض
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">لا يوجد طلاب لعرضهم بناءً على الفلاتر المحددة.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="d-flex justify-content-center">
        {{ $students->withQueryString()->links() }}
    </div>
</div>
@endsection