<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\IntelligenceTypeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController; // للتحكم العام بالاختبار البعدي

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'showLandingPage'])->name('landing');

/* التسجيل (الاختبار القبلي) */
Route::get('/register', [StudentController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [StudentController::class, 'register'])->name('register.submit');

/* البحث/الدخول للاختبار البعدي */
Route::get('/post-test', [StudentController::class, 'showPostTestLookupForm'])->name('post-test.lookup');
Route::post('/post-test', [StudentController::class, 'handlePostTestLookup'])
    ->middleware('throttle:5,1') // ✅ خمس محاولات لكل دقيقة
    ->name('post-test.submit');

/* صفحة الاختبار (تحدد من الجلسة قبلي/بعدي) */
Route::get('/test', [TestController::class, 'showTest'])->name('test.show');
Route::post('/submit-test', [TestController::class, 'calculateResult'])->name('test.submit');

/* ===================== الطالب (بدون معرّف في الرابط) ===================== */
Route::get('/results', [StudentController::class, 'showOwnResults'])
    ->middleware('owns.result')
    ->name('results.show');

Route::get('/results/pdf', [StudentController::class, 'exportOwnPdf'])
    ->middleware('owns.result')
    ->name('results.pdf');

Route::get('/growth-report', [StudentController::class, 'showOwnGrowthReport'])
    ->middleware('owns.result')
    ->name('growth.report');
/* ======================================================================== */

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');

/* 🛡️ تقييد محاولات تسجيل الدخول */
Route::post('/admin/login', [LoginController::class, 'login'])
    ->middleware('throttle:login')
    ->name('admin.login.attempt');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/* ✅ قفل لوحات الإدارة على المستخدمين بدور admin فقط */
Route::prefix('admin')->middleware(['auth', 'auth.admin'])->name('admin.')->group(function () {

    /* لوحة التحكم */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* إدارة الطلاب */
    Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
    Route::get('/students/export', [AdminStudentController::class, 'export'])->name('students.export');
    Route::get('/students/{student}', [AdminStudentController::class, 'show'])
        ->whereNumber('student')
        ->name('students.show');
    Route::delete('/students/{student}', [AdminStudentController::class, 'destroy'])
        ->whereNumber('student')
        ->name('students.destroy');

    // التحكم الفردي بسماح الاختبار البعدي لطالب معيّن
    Route::post('/students/{student}/toggle-post-test', [AdminStudentController::class, 'togglePostTest'])
        ->whereNumber('student')
        ->name('students.toggle_post_test');

    // السماح/الإلغاء جماعيًا
    Route::post('/students/bulk/allow-post-test', [AdminStudentController::class, 'bulkAllowPostTest'])
        ->name('students.bulk_allow_post_test');

    /* إدارة الأسئلة */
    Route::resource('questions', QuestionController::class)->except(['show']);

    /* إدارة أنواع الذكاء */
    Route::get('/types', [IntelligenceTypeController::class, 'index'])->name('types.index');
    Route::get('/types/{id}/edit', [IntelligenceTypeController::class, 'edit'])
        ->whereNumber('id')
        ->name('types.edit');
    Route::put('/types/{id}', [IntelligenceTypeController::class, 'update'])
        ->whereNumber('id')
        ->name('types.update');

    /* الملف الشخصي + الثيم */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/theme/{theme}', [PageController::class, 'switchTheme'])->name('theme.switch');

    /* التحكم العام في الاختبار البعدي */
    Route::post('/settings/toggle-post-test-global', [SettingsController::class, 'togglePostTestGlobal'])
        ->name('settings.toggle_post_test_global');
});
