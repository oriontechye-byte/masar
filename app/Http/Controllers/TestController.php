<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class TestController extends Controller
{
    /**
     * عرض صفحة الاختبار.
     * - يتأكد من وجود جلسة صالحة (قبلي/بعدي)
     * - يجلب الأسئلة ويعرضها
     */
    public function showTest()
    {
        // ✅ تحقق من صلاحية الجلسة
        $isPreTest  = Session::has('student_registration_data') && Session::get('test_type_for_test') === 'pre';
        $isPostTest = Session::has('student_id_for_test')       && Session::get('test_type_for_test') === 'post';

        if (!$isPreTest && !$isPostTest) {
            return redirect()->route('landing')->withErrors(['msg' => 'جلسة الاختبار غير صالحة، يرجى البدء من جديد.']);
        }

        // ✅ جلب الأسئلة
        $questions = DB::table('questions')->get();

        // (اختياري) تمرير نوع الاختبار للواجهة لو حبيت تغيّر النصوص
        $testType = $isPreTest ? 'pre' : 'post';

        return view('test', [
            'questions' => $questions,
            'testType'  => $testType,
        ]);
    }

    /**
     * حساب وحفظ نتائج الاختبار.
     * - يحسب المجاميع الخام لكل نوع ذكاء
     * - يحولها إلى نسب مئوية (0..100) بناءً على عدد الأسئلة × 2
     * - يخزّن/يحدث النتائج حسب نوع الاختبار (قبلي/بعدي)
     * - يوجّه:
     *     قبلي  -> صفحة النتائج المعتادة
     *     بعدي  -> تقرير التطوّر (growth.report)
     */
    public function calculateResult(Request $request)
    {
        // ✅ نقرأ نوع الاختبار من الجلسة
        $testType = Session::get('test_type_for_test');
        $answers  = $request->input('answers'); // answers[question_id] = 0|1|2

        if (!$testType) {
            return redirect()->route('landing')->withErrors(['msg' => 'انتهت صلاحية الجلسة، الرجاء البدء من جديد.']);
        }

        // ✅ جلب الأسئلة مرّة واحدة (مفهرسة بالـ id)
        $questions = DB::table('questions')->get()->keyBy('id');

        // ✅ خريطة أنواع الذكاء (ثابتة حسب IDs في جدول intelligence_types)
        $typeMap = [
            1 => 'social',
            2 => 'visual',
            3 => 'intrapersonal',
            4 => 'kinesthetic',
            5 => 'logical',
            6 => 'naturalist',
            7 => 'linguistic',
            8 => 'musical',
        ];

        // ✅ تهيئة المجاميع الخام لكل نوع
        $raw = [];
        foreach ($typeMap as $name) {
            $raw[$name] = 0;
        }

        // ✅ جمع الدرجات الخام لكل نوع (ضمان النطاق 0..2)
        if (is_array($answers)) {
            foreach ($answers as $question_id => $value) {
                if (isset($questions[$question_id])) {
                    $q       = $questions[$question_id];
                    $typeKey = $typeMap[$q->intelligence_type_id] ?? null;
                    if ($typeKey !== null) {
                        $v = (int) $value;
                        if ($v < 0) $v = 0;
                        if ($v > 2) $v = 2;
                        $raw[$typeKey] += $v;
                    }
                }
            }
        }

        // ✅ عدد الأسئلة لكل نوع (من الجدول مباشرة لضمان الدقة)
        $countsByTypeId = DB::table('questions')
            ->select('intelligence_type_id', DB::raw('COUNT(*) as c'))
            ->groupBy('intelligence_type_id')
            ->pluck('c', 'intelligence_type_id'); // [type_id => count]

        // ✅ تحويل المجاميع إلى نسب مئوية من 100% (كل سؤال أقصى 2)
        $percent = [];
        foreach ($typeMap as $typeId => $name) {
            $count   = (int) ($countsByTypeId[$typeId] ?? 0);
            $maxSum  = $count * 2; // 2 = أعلى اختيار
            $percent[$name] = $maxSum > 0
                ? (int) round(($raw[$name] / $maxSum) * 100)
                : 0;
        }

        // سنحتاجه للتوجيه بعد الحفظ
        $studentId = null;

        if ($testType === 'pre') {
            // ===========================
            // ✅ حالة الاختبار القبلي
            // ===========================

            // تحقق من وجود بيانات التسجيل في الجلسة
            if (!Session::has('student_registration_data')) {
                return redirect('/register')->withErrors(['msg' => 'انتهت صلاحية جلسة التسجيل، الرجاء تسجيل بياناتك مرة أخرى.']);
            }

            $studentData = Session::get('student_registration_data');

            // إنشاء الطالب
            $studentId = DB::table('students')->insertGetId([
                'full_name'        => $studentData['full_name'],
                'whatsapp_number'  => $studentData['whatsapp_number'], // حقل الاسم كما هو في قاعدة البيانات
                'email'            => $studentData['email'],
                'governorate'      => $studentData['governorate'],
                'gpa'              => $studentData['gpa'],
                'graduation_year'  => $studentData['graduation_year'],
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // تخزين النِّسب (وليس الدرجات الخام) في الحقول score_*
            DB::table('test_results')->insert([
                'student_id'           => $studentId,
                'score_social'         => $percent['social'],
                'score_visual'         => $percent['visual'],
                'score_intrapersonal'  => $percent['intrapersonal'],
                'score_kinesthetic'    => $percent['kinesthetic'],
                'score_logical'        => $percent['logical'],
                'score_naturalist'     => $percent['naturalist'],
                'score_linguistic'     => $percent['linguistic'],
                'score_musical'        => $percent['musical'],
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            // تنظيف جلسة التسجيل فقط
            Session::forget('student_registration_data');

        } elseif ($testType === 'post') {
            // ===========================
            // ✅ حالة الاختبار البعدي
            // ===========================

            // يجب أن يكون الطالب موجودًا مسبقًا (تم تسجيله في القبلي)
            $studentId = Session::get('student_id_for_test');
            if (!$studentId) {
                return redirect()->route('landing')->withErrors(['msg' => 'تعذر تحديد الطالب لهذه الجلسة. أعد المحاولة من صفحة البحث.']);
            }

            // تحديث النِّسب في الحقول post_score_*
            DB::table('test_results')->where('student_id', $studentId)->update([
                'post_score_social'         => $percent['social'],
                'post_score_visual'         => $percent['visual'],
                'post_score_intrapersonal'  => $percent['intrapersonal'],
                'post_score_kinesthetic'    => $percent['kinesthetic'],
                'post_score_logical'        => $percent['logical'],
                'post_score_naturalist'     => $percent['naturalist'],
                'post_score_linguistic'     => $percent['linguistic'],
                'post_score_musical'        => $percent['musical'],
                'updated_at'                => now(),
            ]);
        }

        // ✅ تنظيف مفاتيح الجلسة العامة للاختبار
        Session::forget('student_id_for_test');
        Session::forget('test_type_for_test');

        // ✅ التوجيه النهائي:
        // - قبلي  -> النتائج الاعتيادية
        // - بعدي  -> تقرير التطوّر (الجديد)
        if ($testType === 'post') {
            return redirect()
                ->route('growth.report', ['student_id' => $studentId])
                ->with('success', 'تم حفظ نتيجتك البعدية — هذا تقرير تطوّرك 🎯');
        }

        return redirect()->route('results.show', ['student_id' => $studentId]);
    }
}
