<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Show the student registration form.
     */
    public function showRegistrationForm()
    {
        // Data for dropdowns
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
        // Renaming fields for validation to match the database columns
        $request->merge([
            'name' => $request->input('full_name'),
            'grade' => $request->input('gpa'),
        ]);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20|unique:students,whatsapp_number',
            'email' => 'nullable|email|max:255',
            'governorate' => 'required|string|max:255',
            'grade' => 'required|numeric|min:0|max:100',
            'graduation_year' => 'required|integer',
        ], [
            'name.required' => 'حقل الاسم الكامل مطلوب.',
            'whatsapp_number.required' => 'حقل رقم الهاتف مطلوب.',
            'whatsapp_number.unique' => 'رقم الهاتف هذا مسجل بالفعل.',
            'governorate.required' => 'حقل المحافظة مطلوب.',
            'grade.required' => 'حقل المعدل مطلوب.',
            'grade.numeric' => 'حقل المعدل يجب أن يكون رقماً صحيحاً أو عشرياً.',
            'graduation_year.required' => 'حقل سنة التخرج مطلوب.',
        ]);

        // We already merged 'name' and 'grade', so they are available in validatedData
        $student = Student::create($validatedData);

        // Store student ID in session to start the test
        Session::put('student_id', $student->id);
        Session::put('test_type', 'pre_test'); // Mark as pre-test

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

        // Check if student has already completed the post-test
        if ($student->testResult && $student->testResult->post_lecture_scores) {
            return redirect()->route('results.show', $student->id)->with('info', 'لقد أكملت الاختبار البعدي مسبقاً. هذه هي نتيجتك.');
        }

        // Store student ID and test type in session
        Session::put('student_id', $student->id);
        Session::put('test_type', 'post_test'); // Mark as post-test

        return redirect()->route('test.show');
    }

    /**
     * Display student's test results.
     */
    public function showStudentResults($student_id)
    {
        $student = Student::with('testResult.intelligenceType')->findOrFail($student_id);

        if (!$student->testResult) {
            return redirect()->route('landing')->with('error', 'لم يتم العثور على نتائج لهذا الطالب.');
        }
        
        return view('results', compact('student'));
    }
}