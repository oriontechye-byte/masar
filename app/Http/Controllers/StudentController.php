<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Session;
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

        // --- **هذا هو التعديل الرئيسي** ---
        // 1. لا ننشئ الطالب هنا، بل نخزن بياناته مؤقتاً في الجلسة
        Session::put('student_registration_data', $validatedData);
        Session::put('test_type_for_test', 'pre'); // نحدد أن هذا هو الاختبار القبلي

        // 2. نوجه الطالب لصفحة الاختبار برابط نظيف
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
            return redirect()->route('results.show', $student->id)->with('info', 'لقد أكملت الاختبار البعدي مسبقاً. هذه هي نتيجتك.');
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

        foreach ($intelligenceTypes as $id => $type) {
            $key = Str::of($type->name)->before(' ')->snake();
            $preScores[$id] = $result->{'score_' . $key} ?? 0;
            if ($result->{'post_score_' . $key} !== null) {
                $postScores[$id] = $result->{'post_score_' . $key};
            }
        }
        arsort($preScores);
        if($postScores) {
            arsort($postScores);
        }
        
        return view('results', compact('student', 'preScores', 'postScores', 'intelligenceTypes'));
    }
}