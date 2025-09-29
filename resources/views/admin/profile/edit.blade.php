@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800 text-right">تعديل الملف الشخصي</h1>

    @if (session('success'))
        <div class="alert alert-success border-left-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-left-danger" role="alert">
            <ul class="pl-4 my-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary text-right">معلومات الحساب</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group text-right">
                    <label for="name">الاسم</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}">
                </div>

                <div class="form-group text-right">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}">
                </div>

                <hr>

                <div class="form-group text-right">
                    <label for="password">كلمة المرور الجديدة</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="اتركها فارغة لعدم التغيير">
                </div>

                <div class="form-group text-right">
                    <label for="password_confirmation">تأكيد كلمة المرور الجديدة</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>

                <button type="submit" class="btn btn-primary float-right">حفظ التغييرات</button>
            </form>
        </div>
    </div>
</div>
@endsection