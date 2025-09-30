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
     */
    public function showTest()
    {
        // --- تعديل: التحقق من وجود جلسة صالحة للاختبار ---
        $isPreTest = Session::has('student_registration_data') && Session::get('test_type_for_test') === 'pre';
        $isPostTest = Session::has('student_id_for_test') && Session::get('test_type_for_test') === 'post';

        // إذا لم تكن هناك أي جلسة صالحة، نعيد المستخدم للصفحة الرئيسية
        if (!$isPreTest && !$isPostTest) {
            return redirect()->route('landing')->withErrors(['msg' => 'جلسة الاختبار غير صالحة، يرجى البدء من جديد.']);
        }
        
        $questions = DB::table('questions')->get();
        return view('test', ['questions' => $questions]);
    }

    /**
     * حساب وحفظ نتائج الاختبار.
     */
    public function calculateResult(Request $request)
    {
        // --- تعديل: قراءة البيانات من الجلسة فقط ---
        $testType = Session::get('test_type_for_test');
        $answers = $request->input('answers');

        // إعادة التحقق من بيانات الجلسة
        if (!$testType) {
            return redirect()->route('landing')->withErrors(['msg' => 'انتهت صلاحية الجلسة، الرجاء البدء من جديد.']);
        }
        
        // حساب الدرجات (هذا الجزء يبقى كما هو)
        $questions = DB::table('questions')->get()->keyBy('id');
        $scores = [];
        $typeMap = [
            1 => 'social', 2 => 'visual', 3 => 'intrapersonal', 4 => 'kinesthetic',
            5 => 'logical', 6 => 'naturalist', 7 => 'linguistic', 8 => 'musical'
        ];
        
        foreach ($typeMap as $name) {
            $scores[$name] = 0;
        }

        if ($answers) {
            foreach ($answers as $question_id => $value) {
                if (isset($questions[$question_id])) {
                    $question = $questions[$question_id];
                    $typeName = $typeMap[$question->intelligence_type_id];
                    $scores[$typeName] += (int)$value;
                }
            }
        }

        $studentId = null; 

        // --- **هذا هو التصحيح الرئيسي للمنطق** ---
        if ($testType === 'pre') {
            // 1. نتأكد من وجود بيانات التسجيل المؤقتة
            if (!Session::has('student_registration_data')) {
                return redirect('/register')->withErrors(['msg' => 'انتهت صلاحية جلسة التسجيل، الرجاء تسجيل بياناتك مرة أخرى.']);
            }
            $studentData = Session::get('student_registration_data');

            // 2. نقوم بإنشاء سجل الطالب **الآن** ونحصل على رقمه
            $studentId = DB::table('students')->insertGetId([
                'full_name' => $studentData['full_name'],
                'whatsapp_number' => $studentData['whatsapp_number'],
                'email' => $studentData['email'],
                'governorate' => $studentData['governorate'],
                'gpa' => $studentData['gpa'],
                'graduation_year' => $studentData['graduation_year'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. نقوم بإنشاء سجل النتائج للطالب الجديد
            DB::table('test_results')->insert([
                'student_id' => $studentId,
                'score_social' => $scores['social'],
                'score_visual' => $scores['visual'],
                'score_intrapersonal' => $scores['intrapersonal'],
                'score_kinesthetic' => $scores['kinesthetic'],
                'score_logical' => $scores['logical'],
                'score_naturalist' => $scores['naturalist'],
                'score_linguistic' => $scores['linguistic'],
                'score_musical' => $scores['musical'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // 4. نحذف بيانات التسجيل المؤقتة من الجلسة
            Session::forget('student_registration_data');

        } elseif ($testType === 'post') {
            // في الاختبار البعدي، الطالب موجود بالفعل، لذلك نأخذ رقمه من الجلسة
            $studentId = Session::get('student_id_for_test');

            DB::table('test_results')->where('student_id', $studentId)->update([
                'post_score_social' => $scores['social'],
                'post_score_visual' => $scores['visual'],
                'post_score_intrapersonal' => $scores['intrapersonal'],
                'post_score_kinesthetic' => $scores['kinesthetic'],
                'post_score_logical' => $scores['logical'],
                'post_score_naturalist' => $scores['naturalist'],
                'post_score_linguistic' => $scores['linguistic'],
                'post_score_musical' => $scores['musical'],
                'updated_at' => now(),
            ]);
        }

        // مسح كل بيانات الجلسة المتعلقة بالاختبار
        Session::forget('student_id_for_test');
        Session::forget('test_type_for_test');

        return redirect()->route('results.show', ['student_id' => $studentId]);
    }
}