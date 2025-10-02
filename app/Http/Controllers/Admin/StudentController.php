<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\IntelligenceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Exports\StudentsExport;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    /**
     * عرض صفحة الطلاب مع إمكانية الفلترة
     */
    public function index(Request $request)
    {
        $query = Student::query();

        if ($request->filled('governorate')) {
            $query->where('governorate', $request->governorate);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        // فلتر حالة السماح بالبعدي (إذا كان العمود موجود)
        if ($request->filled('post_test_allowed') && Schema::hasColumn('students', 'post_test_allowed')) {
            $val = $request->input('post_test_allowed'); // "0" أو "1"
            if ($val === '0' || $val === '1') {
                $query->where('post_test_allowed', (int)$val);
            }
        }

        $students = $query->latest()->paginate(20)->appends($request->query());
        $governorates = Student::select('governorate')->distinct()->pluck('governorate');

        return view('admin.students.index', compact('students', 'governorates'));
    }

    /**
     * عرض تفاصيل طالب واحد
     */
    public function show(Student $student)
    {
        $student->load('testResult');
        $intelligenceTypes = IntelligenceType::all()->keyBy('id');

        return view('admin.students.show', [
            'student'            => $student,
            'result'             => $student->testResult,
            'intelligenceTypes'  => $intelligenceTypes,
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
     * تصدير إلى إكسل
     */
    public function export(Request $request)
    {
        $filters = $request->only(['governorate', 'start_date', 'end_date', 'post_test_allowed']);
        $testType = $request->input('test_type', 'pre');
        $fileName = ($testType === 'post') ? 'نتائج_الطلاب_البعدي.xlsx' : 'نتائج_الطلاب_القبلي.xlsx';

        return Excel::download(new StudentsExport($filters, $testType), $fileName);
    }

    /**
     * تبديل (سماح/إيقاف) الاختبار البعدي لطالب
     */
    public function togglePostTest(Student $student)
    {
        if (!Schema::hasColumn('students', 'post_test_allowed')) {
            return back()->with('error', 'عمود post_test_allowed غير موجود. شغّل الهجرة (migration) أولاً.');
        }

        $student->post_test_allowed = ! (bool) $student->post_test_allowed;
        $student->save();

        return back()->with(
            'success',
            $student->post_test_allowed
                ? 'تم السماح للطالب بالدخول للاختبار البعدي.'
                : 'تم إيقاف السماح للطالب بالدخول للاختبار البعدي.'
        );
    }

    /**
     * سماح/إلغاء جماعي حسب فلاتر معينة
     */
    public function bulkAllowPostTest(Request $request)
    {
        if (!Schema::hasColumn('students', 'post_test_allowed')) {
            return back()->with('error', 'عمود post_test_allowed غير موجود. شغّل الهجرة (migration) أولاً.');
        }

        $allowed = $request->boolean('allowed', true);
        $q = Student::query();

        if ($request->filled('governorate')) {
            $q->where('governorate', $request->governorate);
        }
        // اختياري: تمرير IDs معينة لتحديثها فقط
        if ($request->filled('ids')) {
            $ids = array_filter((array) $request->input('ids'));
            if (!empty($ids)) {
                $q->whereIn('id', $ids);
            }
        }

        $updated = $q->update(['post_test_allowed' => $allowed]);

        return back()->with(
            'success',
            $allowed
                ? "تم السماح بالاختبار البعدي لـ {$updated} طالب."
                : "تم إيقاف السماح بالاختبار البعدي لـ {$updated} طالب."
        );
    }
}
