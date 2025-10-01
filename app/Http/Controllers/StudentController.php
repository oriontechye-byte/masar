<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;   // ✅ أضفنا الـ Facade
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    /**
     * Show the student registration form.
     */
    public function showRegistrationForm()
    {
        $governorates = [
            'أمانة العاصمة', 'صنعاء', 'عدن', 'تعز', 'الحديدة', 'إب', 'ذمار', 'حضرموت',
            'لحج', 'أبين', 'شبوة', 'المهرة', 'مأرب', 'الجوف', 'البيضاء', 'حجة',
            'صعدة', 'المحويت', 'عمران', 'الضالع', 'ريمة', 'سقطرى'
        ];
        
        $currentYear = date('Y');
        $years = range($currentYear, $currentYear - 10);

        return view('register', compact('governorates', 'years'));
    }

    /**
     * Handle student registration.
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20|unique:students,whatsapp_number',
            'email' => 'nullable|email|max:255',
            'governorate' => 'required|string|max:255',
            'gpa' => 'required|numeric|min:0|max:100',
            'graduation_year' => 'required|integer',
        ], [
            'full_name.required' => 'حقل الاسم الكامل مطلوب.',
            'whatsapp_number.required' => 'حقل رقم الهاتف مطلوب.',
            'whatsapp_number.unique' => 'رقم الهاتف هذا مسجل بالفعل.',
            'governorate.required' => 'حقل المحافظة مطلوب.',
            'gpa.required' => 'حقل المعدل مطلوب.',
            'gpa.numeric' => 'حقل المعدل يجب أن يكون رقماً صحيحاً أو عشرياً.',
            'graduation_year.required' => 'حقل سنة التخرج مطلوب.',
        ]);

        // نخزن البيانات مؤقتاً للجلسة ونوجه لصفحة الاختبار (اختبار قبلي)
        Session::put('student_registration_data', $validatedData);
        Session::put('test_type_for_test', 'pre');

        return redirect()->route('test.show');
    }

    /**
     * Show the form to look up a student for the post-lecture test.
     */
    public function showPostTestLookupForm()
    {
        return view('post_test_lookup');
    }

    /**
     * Handle the lookup and redirect to the post-lecture test.
     */
    public function handlePostTestLookup(Request $request)
    {
        $validatedData = $request->validate([
            'whatsapp_number' => 'required|string|exists:students,whatsapp_number',
        ], [
            'whatsapp_number.required' => 'الرجاء إدخال رقم الواتساب.',
            'whatsapp_number.exists' => 'هذا الرقم غير مسجل لدينا، تأكد من إدخاله بشكل صحيح.',
        ]);

        $student = Student::where('whatsapp_number', $validatedData['whatsapp_number'])->first();

        $testResult = $student->testResult;
        if ($testResult && $testResult->post_score_social !== null) {
            return redirect()->route('results.show', $student->id)
                ->with('info', 'لقد أكملت الاختبار البعدي مسبقاً. هذه هي نتيجتك.');
        }

        Session::put('student_id_for_test', $student->id);
        Session::put('test_type_for_test', 'post');

        return redirect()->route('test.show');
    }

    /**
     * Display student's test results.
     */
    public function showStudentResults($student_id)
    {
        $student = Student::findOrFail($student_id);
        $result = $student->testResult;

        if (!$result) {
            return redirect()->route('landing')->with('error', 'لم يتم العثور على نتائج لهذا الطالب.');
        }

        $intelligenceTypes = \App\Models\IntelligenceType::all()->keyBy('id');
        $preScores = [];
        $postScores = null;

        // خريطة الحقول بين النوع ومعرّف العمود في النتائج
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

        foreach ($intelligenceTypes as $id => $type) {
            $key = $typeMap[$id] ?? null;
            if ($key) {
                $preScores[$id] = $result->{'score_' . $key} ?? 0;
                if ($result->{'post_score_' . $key} !== null) {
                    $postScores[$id] = $result->{'post_score_' . $key};
                }
            }
        }

        // ترتيب تنازلي
        arsort($preScores);
        if ($postScores) {
            arsort($postScores);
        }

        // نحدد المصفوفة المعروضة (بعدي إن وجد، وإلا قبلي)
        $scores = $postScores ?? $preScores;

        // === 1) نحسب عدد الأسئلة لكل نوع (من جدول الأسئلة) ===
        $questions = DB::table('questions')
            ->select('id', 'intelligence_type_id')
            ->get();

        $countsByKey = array_fill_keys(array_values($typeMap), 0);
        foreach ($questions as $q) {
            $key = $typeMap[$q->intelligence_type_id] ?? null;
            if ($key) $countsByKey[$key]++;
        }

        // أعلى درجة ممكنة للسؤال (0/1/2)
        $maxChoice = 2;

        // === 2) جهّز الدرجات الخام ===
        $preRaw = [];
        $postRaw = [];
        foreach ($intelligenceTypes as $id => $type) {
            $key = $typeMap[$id] ?? null;
            if (!$key) continue;

            $preVal = (int)($result->{'score_' . $key} ?? 0);
            $postVal = $result->{'post_score_' . $key} ?? null;

            $preRaw[$id] = $preVal;
            if (!is_null($postVal)) {
                $postRaw[$id] = (int)$postVal;
            }
        }

        // === 3) حوّل الخام إلى نسب مئوية (0..100) لكل فرع ===
        $prePercents = [];
        $postPercents = null;

        foreach ($intelligenceTypes as $id => $type) {
            $key = $typeMap[$id] ?? null;
            if (!$key) continue;

            $maxForThis = $countsByKey[$key] * $maxChoice;
            $prePercents[$id] = $maxForThis > 0
                ? round(($preRaw[$id] / $maxForThis) * 100)
                : 0;

            if ($postScores) {
                $val = $postRaw[$id] ?? null;
                if (!is_null($val)) {
                    $postPercents[$id] = $maxForThis > 0
                        ? round(($val / $maxForThis) * 100)
                        : 0;
                }
            }
        }

        // نستخدم النسب في العرض
        $scores = $postPercents ?? $prePercents;

        // إعادة الترتيب تنازلي
        arsort($scores);

        return view('results', compact(
            'student',
            'preScores',
            'postScores',
            'intelligenceTypes',
            'scores',
            'prePercents',
            'postPercents'
        ));
    }
}
