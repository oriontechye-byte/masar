<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TestController extends Controller
{
    /**
     * عرض صفحة الاختبار.
     */
    public function showTest()
    {
        // تحقق من صلاحية الجلسة ونوع الاختبار
        $typeFromSession = Session::get('test_type_for_test');
        $isPreTest  = $typeFromSession === 'pre'  && Session::has('student_registration_data');
        $isPostTest = $typeFromSession === 'post' && Session::has('student_id_for_test');

        if (!$isPreTest && !$isPostTest) {
            return redirect()->route('landing')
                ->withErrors(['msg' => 'جلسة الاختبار غير صالحة، يرجى البدء من جديد.']);
        }

        // لو بختبر بعدي، لازم يكون عنده سجل قبلي
        if ($isPostTest) {
            $studentId = (int) Session::get('student_id_for_test');
            $hasPre = DB::table('test_results')->where('student_id', $studentId)->exists();
            if (!$hasPre) {
                return redirect()->route('post-test.lookup')
                    ->withErrors(['whatsapp_number' => 'لا يمكنك دخول الاختبار البعدي قبل إكمال الاختبار القبلي.']);
            }
        }

        // جلب الأسئلة
        $questions = DB::table('questions')->get();

        return view('test', [
            'questions' => $questions,
            'testType'  => $isPreTest ? 'pre' : 'post',
        ]);
    }

    /**
     * حساب وحفظ نتائج الاختبار.
     */
    public function calculateResult(Request $request)
    {
        $testType = Session::get('test_type_for_test');
        if (!$testType) {
            return redirect()->route('landing')
                ->withErrors(['msg' => 'انتهت صلاحية الجلسة، الرجاء البدء من جديد.']);
        }

        // إجابات النموذج: answers[question_id] = 0|1|2
        $answers = (array) $request->input('answers', []);

        // جلب الأسئلة ومفاتيح الأنواع
        $questions = DB::table('questions')->get()->keyBy('id');

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

        // مجاميع خام
        $raw = array_fill_keys(array_values($typeMap), 0);

        foreach ($answers as $qid => $val) {
            // تحقق من الأرقام وصحة السؤال
            if (!is_numeric($qid) || !isset($questions[$qid])) continue;

            $q = $questions[$qid];
            $typeKey = $typeMap[$q->intelligence_type_id] ?? null;
            if ($typeKey === null) continue;

            // قيّم ضمن (0..2)
            $v = (int) $val;
            if ($v < 0) $v = 0;
            if ($v > 2) $v = 2;

            $raw[$typeKey] += $v;
        }

        // عدد الأسئلة لكل نوع
        $countsByTypeId = DB::table('questions')
            ->select('intelligence_type_id', DB::raw('COUNT(*) as c'))
            ->groupBy('intelligence_type_id')
            ->pluck('c', 'intelligence_type_id'); // [type_id => count]

        // تحويل إلى نسب مئوية (0..100)
        $percent = [];
        foreach ($typeMap as $typeId => $name) {
            $count  = (int) ($countsByTypeId[$typeId] ?? 0);
            $maxSum = $count * 2;
            $p = $maxSum > 0 ? (int) round(($raw[$name] / $maxSum) * 100) : 0;
            if ($p < 0)   $p = 0;
            if ($p > 100) $p = 100;
            $percent[$name] = $p;
        }

        $studentId = null;

        if ($testType === 'pre') {
            // ==== الاختبار القبلي ====
            if (!Session::has('student_registration_data')) {
                return redirect('/register')
                    ->withErrors(['msg' => 'انتهت صلاحية جلسة التسجيل، الرجاء تسجيل بياناتك مرة أخرى.']);
            }

            $data = Session::get('student_registration_data');

            DB::beginTransaction();
            try {
                $studentId = DB::table('students')->insertGetId([
                    'full_name'       => $data['full_name'],
                    'whatsapp_number' => $data['whatsapp_number'],
                    'email'           => $data['email'] ?? null,
                    'governorate'     => $data['governorate'],
                    'gpa'             => $data['gpa'],
                    'graduation_year' => $data['graduation_year'],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                DB::table('test_results')->insert([
                    'student_id'          => $studentId,
                    'score_social'        => $percent['social'],
                    'score_visual'        => $percent['visual'],
                    'score_intrapersonal' => $percent['intrapersonal'],
                    'score_kinesthetic'   => $percent['kinesthetic'],
                    'score_logical'       => $percent['logical'],
                    'score_naturalist'    => $percent['naturalist'],
                    'score_linguistic'    => $percent['linguistic'],
                    'score_musical'       => $percent['musical'],
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return back()->withErrors(['msg' => 'تعذر حفظ النتيجة: '.$e->getMessage()])->withInput();
            }

            // ✅ اسمح بعرض نتائجه فورًا (بدون رقم بالرابط)
            Session::put('viewer_student_id', $studentId);

            // نظّف الجلسة
            Session::forget(['student_registration_data', 'test_type_for_test']);

            return redirect()->route('results.show')
                ->with('success', 'تم حفظ نتيجتك.');

        } else {
            // ==== الاختبار البعدي ====
            $studentId = (int) Session::get('student_id_for_test');
            if (!$studentId) {
                return redirect()->route('landing')
                    ->withErrors(['msg' => 'تعذر تحديد الطالب لهذه الجلسة. ابدأ من صفحة البحث.']);
            }

            // تأكيد وجود سجل قبلي
            $hasPre = DB::table('test_results')->where('student_id', $studentId)->exists();
            if (!$hasPre) {
                return redirect()->route('post-test.lookup')
                    ->withErrors(['whatsapp_number' => 'لا يمكنك دخول الاختبار البعدي قبل إكمال الاختبار القبلي.']);
            }

            DB::beginTransaction();
            try {
                // تأكد من وجود السجل
                DB::table('test_results')->updateOrInsert(
                    ['student_id' => $studentId],
                    ['updated_at' => now(), 'created_at' => now()]
                );

                // حدّث حقول البعدي
                DB::table('test_results')
                    ->where('student_id', $studentId)
                    ->update([
                        'post_score_social'        => $percent['social'],
                        'post_score_visual'        => $percent['visual'],
                        'post_score_intrapersonal' => $percent['intrapersonal'],
                        'post_score_kinesthetic'   => $percent['kinesthetic'],
                        'post_score_logical'       => $percent['logical'],
                        'post_score_naturalist'    => $percent['naturalist'],
                        'post_score_linguistic'    => $percent['linguistic'],
                        'post_score_musical'       => $percent['musical'],
                        'updated_at'               => now(),
                    ]);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return back()->withErrors(['msg' => 'تعذر حفظ النتيجة البعدية: '.$e->getMessage()])->withInput();
            }

            // ✅ اسمح بعرض تقريره/نتيجته فورًا
            Session::put('viewer_student_id', $studentId);

            // تحقّق من وجود أي قيمة بعدية
            $hasAnyPost = DB::table('test_results')
                ->where('student_id', $studentId)
                ->where(function ($q) {
                    $q->whereNotNull('post_score_social')
                      ->orWhereNotNull('post_score_visual')
                      ->orWhereNotNull('post_score_intrapersonal')
                      ->orWhereNotNull('post_score_kinesthetic')
                      ->orWhereNotNull('post_score_logical')
                      ->orWhereNotNull('post_score_naturalist')
                      ->orWhereNotNull('post_score_linguistic')
                      ->orWhereNotNull('post_score_musical');
                })->exists();

            // نظّف مفاتيح الجلسة الخاصة بالاختبار
            Session::forget(['student_id_for_test', 'test_type_for_test']);

            // بعد البعدي: تقرير التطوّر إن وُجدت قيم، وإلا النتائج العادية
            return $hasAnyPost
                ? redirect()->route('growth.report')
                    ->with('success', 'تم حفظ نتيجتك البعدية — هذا تقرير تطوّرك 🎯')
                : redirect()->route('results.show')
                    ->with('info', 'تم الحفظ، لكن لم تُسجَّل قيم بعدية؛ نعرض نتيجتك الحالية.');
        }
    }
}
