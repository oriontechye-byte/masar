<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student; // <-- تأكد من استخدام المودل
use App\Models\IntelligenceType; // <-- نحتاجه في دالة show
use Illuminate\Http\Request;
use App\Exports\StudentsExport;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    /**
     * عرض صفحة الطلاب مع إمكانية الفلترة
     */
    public function index(Request $request)
    {
        // استخدام المودل لبناء الاستعلام
        $query = Student::query();

        // تطبيق الفلاتر
        if ($request->filled('governorate')) {
            $query->where('governorate', $request->governorate);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $students = $query->latest()->paginate(20);
        
        $governorates = Student::select('governorate')->distinct()->pluck('governorate');

        return view('admin.students.index', compact('students', 'governorates'));
    }

    /**
     * ----- ***** هذه هي الدالة المعدلة التي تحل المشكلة ***** -----
     * عرض تفاصيل طالب واحد
     */
    public function show(Student $student)
    {
        // نقوم بتحميل بيانات الطالب ونتائجه المرتبطة به
        $student->load('testResult');

        // نحضر قائمة بأنواع الذكاء لنستخدمها في عرض النتائج
        $intelligenceTypes = IntelligenceType::all()->keyBy('id');

        // نرسل كل البيانات المطلوبة إلى صفحة العرض
        return view('admin.students.show', [
            'student' => $student,
            'result' => $student->testResult, // <-- هذا هو السطر الذي يحل المشكلة
            'intelligenceTypes' => $intelligenceTypes,
        ]);
    }

    /**
     * حذف طالب
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'تم حذف الطالب بنجاح.');
    }

    /**
     * دالة التصدير إلى إكسل
     */
    public function export(Request $request)
    {
        $filters = $request->only(['governorate', 'start_date', 'end_date']);
        $testType = $request->input('test_type', 'pre');
        $fileName = ($testType === 'post') ? 'نتائج_الطلاب_البعدي.xlsx' : 'نتائج_الطلاب_القبلي.xlsx';

        return Excel::download(new StudentsExport($filters, $testType), $fileName);
    }
}