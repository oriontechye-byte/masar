<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| هنا تحط كل المسارات الخاصة بالـ API.
| هذه المسارات تلقائيًا تستخدم مجموعة ميدلوير "api"
| المعرّفة في Laravel، يعني بدون جلسات وبتحديد معدل الطلبات.
|
*/

Route::middleware('api')->group(function () {

    // مسار اختبار سريع عشان تتأكد إن الملف شغال
    Route::get('/ping', function () {
        return response()->json(['status' => 'ok']);
    });

    // مثال لمسار مستقبلي: بيانات طالب
    /*
    Route::get('/students/{id}', [\App\Http\Controllers\Api\StudentController::class, 'show']);
    Route::post('/students', [\App\Http\Controllers\Api\StudentController::class, 'store']);
    */
});
