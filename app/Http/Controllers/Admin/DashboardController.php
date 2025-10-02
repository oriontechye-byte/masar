<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ===== فلاتر عامة =====
        $dateFrom     = $request->input('from');
        $dateTo       = $request->input('to');
        $governorate  = $request->input('governorate');

        if (!$dateFrom || !$dateTo) {
            $dateTo   = now()->endOfDay()->toDateTimeString();
            $dateFrom = now()->subDays(30)->startOfDay()->toDateTimeString();
        } else {
            $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
            $dateTo   = date('Y-m-d 23:59:59', strtotime($dateTo));
        }

        // ===== إجمالي الطلاب (مع فلتر المحافظة) =====
        $studentsQuery = DB::table('students');
        if (!empty($governorate)) {
            $studentsQuery->where('governorate', $governorate);
        }
        $studentsCount = (clone $studentsQuery)->count();

        // ===== أساس النتائج + فلاتر =====
        $baseResults = DB::table('test_results as tr')
            ->join('students as s', 's.id', '=', 'tr.student_id')
            ->whereBetween('tr.created_at', [$dateFrom, $dateTo]);

        if (!empty($governorate)) {
            $baseResults->where('s.governorate', $governorate);
        }

        // **عدد السجلات في test_results ضمن الفترة**
        $resultsCount = (clone $baseResults)->count();

        // ===== الأعمدة الثمانية للدرجات =====
        $schema = DB::getSchemaBuilder();
        $scoreCols = [
            'score_social','score_visual','score_intrapersonal','score_kinesthetic',
            'score_logical','score_naturalist','score_linguistic','score_musical',
        ];
        // تأكد أنها موجودة فعلاً (في حال تغيّر اسم أي عمود)
        $scoreCols = array_values(array_filter($scoreCols, fn($c) => $schema->hasColumn('test_results', $c)));

        // احسب "متوسط الإجمالي" = متوسط (مجموع الأعمدة / عددها) عبر كل السجلات
        $avgTotal = 0;
        if (!empty($scoreCols)) {
            $sumExpr = implode(' + ', array_map(fn($c) => "tr.$c", $scoreCols));
            $n = count($scoreCols);

            $avgTotal = (clone $baseResults)
                ->selectRaw("AVG( ($sumExpr) / ? ) as avg_total", [$n])
                ->value('avg_total') ?? 0;
        }

        // ===== أحدث النشاطات (بدون type) =====
        $recentActivities = (clone $baseResults)
            ->select('tr.student_id', 'tr.created_at')
            ->orderBy('tr.created_at', 'desc')
            ->limit(10)
            ->get();

        // ===== قائمة المحافظات للاختيار =====
        $governorates = DB::table('students')->select('governorate')->distinct()->pluck('governorate')->filter()->values();

        // ===== حالة المفتاح العام للاختبار البعدي (آمن حتى لو الجدول غير موجود) =====
        $postTestGlobalEnabled = false;
        if (Schema::hasTable('site_settings')) {
            $val = DB::table('site_settings')->where('key', 'post_test_global_enabled')->value('value');
            $postTestGlobalEnabled = ($val === '1' || $val === 1 || $val === true);
        }
        // ملاحظة: لو ما عندك جدول site_settings بعد، يبقى false افتراضيًا

        // ===== تجميع البيانات للواجهة =====
        $stats = [
            'students_count'    => $studentsCount,
            'results_count'     => $resultsCount,
            'avg_total'         => round($avgTotal, 2),

            // مفاتيح غير مدعومة الآن (لا يوجد تمييز قبلي/بعدي في السكيمة الحالية)
            'pre_count'        => 0,
            'post_count'       => 0,
            'both_count'       => 0,
            'completion_rate'  => 0,
            'avg_delta'        => 0,
            'avg_pre'          => 0,
            'avg_post'         => 0,

            // الفلاتر
            'from'             => $dateFrom,
            'to'               => $dateTo,
            'governorate'      => $governorate,
            'governorates'     => $governorates,

            // النشاطات
            'recent_activities'=> $recentActivities,

            // المفتاح العام للبعدي
            'post_test_global_enabled' => $postTestGlobalEnabled,
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
