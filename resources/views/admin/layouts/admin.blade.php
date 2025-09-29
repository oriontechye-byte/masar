<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة التحكم') - مشروع مسار</title>
    <style>
        body { font-family: 'Cairo', sans-serif; margin: 0; background-color: #f8f9fa; display: flex; }
        .sidebar { width: 250px; background-color: #343a40; color: white; height: 100vh; position: fixed; right: 0; top: 0; padding-top: 20px; box-shadow: -5px 0 15px rgba(0,0,0,0.1); }
        .sidebar h2 { text-align: center; color: #fff; margin-bottom: 30px; }
        .sidebar-nav a { display: block; color: #adb5bd; padding: 15px 20px; text-decoration: none; transition: background-color 0.3s, color 0.3s; border-right: 3px solid transparent; }
        .sidebar-nav a:hover { background-color: #495057; color: white; }
        .sidebar-nav a.active { background-color: #007bff; color: white; border-right-color: #f8f9fa; font-weight: bold; }
        .main-content { margin-right: 250px; padding: 30px; width: calc(100% - 250px); }
        .content-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #dee2e6; padding-bottom: 15px; margin-bottom: 20px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .logout-form button { background: #dc3545; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .stat-card { background-color: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 25px; }
        /* Fix for pagination buttons size */
        .pagination { display: flex; justify-content: center; padding-left: 0; list-style: none; border-radius: .25rem; margin-top: 20px; }
        .pagination li a, .pagination li span { padding: .5rem .75rem; font-size: 1rem; }
        .pagination li.active span { z-index: 1; color: #fff; background-color: #007bff; border-color: #007bff; }
        .pagination li.disabled span { color: #6c757d; pointer-events: none; cursor: auto; background-color: #fff; border-color: #dee2e6; }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <h2>مشروع مسار</h2>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">لوحة التحكم</a>
            <a href="{{ route('admin.students.index') }}" class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}">إدارة الطلاب</a>
            <a href="{{ route('admin.questions.index') }}" class="{{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">إدارة الأسئلة</a>
            <a href="{{ route('admin.types.index') }}" class="{{ request()->routeIs('admin.types.*') ? 'active' : '' }}">إدارة أنواع الذكاء</a>
            
            {{-- ***** هذه هي الإضافة الجديدة ***** --}}
            <a href="{{ route('admin.profile.edit') }}" class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">إعدادات الحساب</a>
            {{-- ***** نهاية الإضافة ***** --}}
        </nav>
    </div>

    <main class="main-content">
        <header class="content-header">
            <h1>@yield('title')</h1>
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit">تسجيل الخروج</button>
            </form>
        </header>

        <div class="content-body">
            @yield('content')
        </div>
    </main>
</body>
</html>