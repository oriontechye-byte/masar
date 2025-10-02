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
// NOTE: ما عد نحتاج ResultsController الخاص بتصدير PDF
// use App\Http\Controllers\ResultsController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'showLandingPage'])->name('landing');

/* تسجيل قبلي */
Route::get('/register', [StudentController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [StudentController::class, 'register'])->name('register.submit');

/* البحث للاختبار البعدي */
Route::get('/post-test', [StudentController::class, 'showPostTestLookupForm'])->name('post-test.lookup');
Route::post('/post-test', [StudentController::class, 'handlePostTestLookup'])->name('post-test.submit');

/* صفحة الاختبار (تعتمد على نوعه من الجلسة: قبلي/بعدي) */
Route::get('/test', [TestController::class, 'showTest'])->name('test.show');
Route::post('/submit-test', [TestController::class, 'calculateResult'])->name('test.submit');

/* النتائج الاعتيادية للطالب */
Route::get('/results/{student_id}', [StudentController::class, 'showStudentResults'])
    ->whereNumber('student_id')
    ->name('results.show');

/* تقرير التطوّر (بعد إكمال البعدي) */
Route::get('/growth-report/{student_id}', [StudentController::class, 'showGrowthReport'])
    ->whereNumber('student_id')
    ->name('growth.report');

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {

    /* لوحة التحكم */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* إدارة الطلاب */
    Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
    Route::get('/students/{student}', [AdminStudentController::class, 'show'])
        ->whereNumber('student')
        ->name('students.show');
    Route::get('/students/export', [AdminStudentController::class, 'export'])->name('students.export');

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

    // NOTE: حذفت مسار /results/{student}/pdf الخاص بالتصدير لأنه اتلغى
});
