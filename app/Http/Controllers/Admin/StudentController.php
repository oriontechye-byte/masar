<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\IntelligenceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'q'                 => ['nullable','string','max:100'],
            'governorate'       => ['nullable','string','max:50'],
            'start_date'        => ['nullable','date'],
            'end_date'          => ['nullable','date','after_or_equal:start_date'],
            'post_test_allowed' => ['nullable', Rule::in(['0','1'])],
            'sort'              => ['nullable', Rule::in(['latest','name_az','grade_desc','grade_asc'])],
            'per_page'          => ['nullable','integer','min:5','max:100'],
        ]);

        $perPage = (int) $request->input('per_page', 20);

        $query = Student::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $v = '%'.trim($request->q).'%';
                $q->where(function ($qq) use ($v) {
                    $qq->where('full_name', 'like', $v)
                       ->orWhere('whatsapp_number', 'like', $v)
                       ->orWhere('email', 'like', $v);
                });
            })
            ->when($request->filled('governorate'), fn($q) => $q->where('governorate', $request->governorate))
            ->when($request->filled('start_date'), fn($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'),   fn($q) => $q->whereDate('created_at', '<=', $request->end_date))
            ->when(
                $request->filled('post_test_allowed') && Schema::hasColumn('students','post_test_allowed'),
                fn($q) => $q->where('post_test_allowed', (int) $request->post_test_allowed)
            );

        switch ($request->input('sort', 'latest')) {
            case 'name_az':   $query->orderBy('full_name'); break;
            case 'grade_desc':$query->orderByDesc('gpa');    break;
            case 'grade_asc': $query->orderBy('gpa');        break;
            default:          $query->orderByDesc('created_at');
        }

        $students = $query->paginate($perPage)->appends($request->query());

        $governorates = Student::query()
            ->select('governorate')->distinct()->pluck('governorate')->filter()->values();

        return view('admin.students.index', compact('students','governorates'));
    }

    public function show(Student $student)
    {
        $student->load('testResult');
        $intelligenceTypes = IntelligenceType::all()->keyBy('id');

        return view('admin.students.show', [
            'student'           => $student,
            'result'            => $student->testResult,
            'intelligenceTypes' => $intelligenceTypes,
        ]);
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'تم حذف الطالب بنجاح.');
    }

    /**
     * تصدير CSV: بيانات الطالب + نتائج (قبلي/بعدي) + التخصصات المقترحة.
     * - لو جدول student_recommendations موجود وفيه قيم: نستخدمها.
     * - لو ناقصة/غير موجود: نولّد التخصصات من أعلى ذكاء (بعديًا فقط) ونأخذ أول 3 تخصصات له
     *   بنفس المسمّيات الظاهرة في واجهة "التقدّم".
     */
    public function export(Request $request)
    {
        $testType = $request->input('test_type', 'pre'); // pre | post
        $isPost   = $testType === 'post';

        $query = Student::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $v = '%'.trim($request->q).'%';
                $q->where(function ($qq) use ($v) {
                    $qq->where('full_name', 'like', $v)
                       ->orWhere('whatsapp_number', 'like', $v)
                       ->orWhere('email', 'like', $v);
                });
            })
            ->when($request->filled('governorate'), fn($q) => $q->where('governorate', $request->governorate))
            ->when($request->filled('start_date'), fn($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'),   fn($q) => $q->whereDate('created_at', '<=', $request->end_date));

        // نتائج الاختبار
        $query->leftJoin('test_results as tr', 'tr.student_id', '=', 'students.id');

        // توصيات (اختياري)
        $hasRecsTable = Schema::hasTable('student_recommendations');
        if ($hasRecsTable) {
            $query->leftJoin('student_recommendations as sr', 'sr.student_id', '=', 'students.id');
        }

        // أعمدة الطالب
        $selects = [
            DB::raw('students.full_name       AS name'),
            DB::raw('students.whatsapp_number AS whatsapp'),
            DB::raw('students.email           AS email'),
            DB::raw('students.governorate     AS governorate'),
            DB::raw('students.gpa             AS gpa'),
            DB::raw('students.graduation_year AS graduation_year'),
            DB::raw('students.created_at      AS created_at'),
        ];

        // أعمدة الدرجات (قبلي/بعدي) مع فحص وجود أعمدة البعدي
        $trHasPost = [
            'social'        => Schema::hasColumn('test_results', 'post_score_social'),
            'visual'        => Schema::hasColumn('test_results', 'post_score_visual'),
            'intrapersonal' => Schema::hasColumn('test_results', 'post_score_intrapersonal'),
            'kinesthetic'   => Schema::hasColumn('test_results', 'post_score_kinesthetic'),
            'logical'       => Schema::hasColumn('test_results', 'post_score_logical'),
            'naturalist'    => Schema::hasColumn('test_results', 'post_score_naturalist'),
            'linguistic'    => Schema::hasColumn('test_results', 'post_score_linguistic'),
            'musical'       => Schema::hasColumn('test_results', 'post_score_musical'),
        ];

        if ($isPost) {
            $selects[] = $trHasPost['social']        ? DB::raw('tr.post_score_social        AS s_social')        : DB::raw('NULL AS s_social');
            $selects[] = $trHasPost['visual']        ? DB::raw('tr.post_score_visual        AS s_visual')        : DB::raw('NULL AS s_visual');
            $selects[] = $trHasPost['intrapersonal'] ? DB::raw('tr.post_score_intrapersonal AS s_intrapersonal') : DB::raw('NULL AS s_intrapersonal');
            $selects[] = $trHasPost['kinesthetic']   ? DB::raw('tr.post_score_kinesthetic   AS s_kinesthetic')   : DB::raw('NULL AS s_kinesthetic');
            $selects[] = $trHasPost['logical']       ? DB::raw('tr.post_score_logical       AS s_logical')       : DB::raw('NULL AS s_logical');
            $selects[] = $trHasPost['naturalist']    ? DB::raw('tr.post_score_naturalist    AS s_naturalist')    : DB::raw('NULL AS s_naturalist');
            $selects[] = $trHasPost['linguistic']    ? DB::raw('tr.post_score_linguistic    AS s_linguistic')    : DB::raw('NULL AS s_linguistic');
            $selects[] = $trHasPost['musical']       ? DB::raw('tr.post_score_musical       AS s_musical')       : DB::raw('NULL AS s_musical');
        } else {
            $selects[] = DB::raw('tr.score_social        AS s_social');
            $selects[] = DB::raw('tr.score_visual        AS s_visual');
            $selects[] = DB::raw('tr.score_intrapersonal AS s_intrapersonal');
            $selects[] = DB::raw('tr.score_kinesthetic   AS s_kinesthetic');
            $selects[] = DB::raw('tr.score_logical       AS s_logical');
            $selects[] = DB::raw('tr.score_naturalist    AS s_naturalist');
            $selects[] = DB::raw('tr.score_linguistic    AS s_linguistic');
            $selects[] = DB::raw('tr.score_musical       AS s_musical');
        }

        // التخصصات من جدول التوصيات (إن وُجد)
        if ($hasRecsTable) {
            $selects[] = DB::raw('sr.top1_major AS top1_major_db');
            $selects[] = DB::raw('sr.top2_major AS top2_major_db');
            $selects[] = DB::raw('sr.top3_major AS top3_major_db');
        } else {
            $selects[] = DB::raw('NULL AS top1_major_db');
            $selects[] = DB::raw('NULL AS top2_major_db');
            $selects[] = DB::raw('NULL AS top3_major_db');
        }

        $rows = $query->orderByDesc('students.created_at')->get($selects);

        // مابنج مطابق لواجهة "التقدّم"
        $majorsMap = [
            'visual'        => ['الهندسة المعمارية','فنان','مصمم جرافيك','طيار'],
            'intrapersonal' => ['فيلسوف','كاتب','عالم نفس','باحث'],
            'kinesthetic'   => ['رياضي','جراح','حرفي','راقص'],
            'social'        => ['تنمية بشرية','خدمة اجتماعية','إدارة موارد بشرية','تدريب'],
            'logical'       => ['علوم الحاسوب','رياضيات','هندسة','إحصاء'],
            'naturalist'    => ['أحياء','زراعة','بيئة','طب بيطري'],
            'linguistic'    => ['آداب/لغة','ترجمة','صحافة','تعليم'],
            'musical'       => ['موسيقى','صوتيات','إخراج صوتي','تعليم موسيقى'],
        ];

        // CSV في الذاكرة + BOM
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, "\xEF\xBB\xBF");

        $header = [
            'الاسم الكامل','رقم الواتساب','البريد الإلكتروني','المحافظة',
            'المعدل','سنة التخرج','تاريخ التسجيل',
        ];
        $prefix = $isPost ? 'بعدي - ' : 'قبلي - ';
        $header = array_merge($header, [
            $prefix.'الذكاء الاجتماعي',
            $prefix.'الذكاء البصري-المكاني',
            $prefix.'الذكاء الشخصي-الذاتي',
            $prefix.'الذكاء الجسدي-الحركي',
            $prefix.'الذكاء المنطقي-الرياضي',
            $prefix.'الذكاء الطبيعي',
            $prefix.'الذكاء اللغوي',
            $prefix.'الذكاء الموسيقي',
            'التخصص المقترح 1',
            'التخصص المقترح 2',
            'التخصص المقترح 3',
        ]);
        fputcsv($handle, $header);

        // ترتيب مفضّل عند التعادل
        $order = ['visual','intrapersonal','kinesthetic','social','logical','naturalist','linguistic','musical'];

        foreach ($rows as $r) {
            // نحافظ على رقم الواتساب كنص في Excel
            $phone = $r->whatsapp ? '="'.$r->whatsapp.'"' : '';

            // التخصصات من الجدول (إن وُجدت)
            $m1 = $r->top1_major_db ?? null;
            $m2 = $r->top2_major_db ?? null;
            $m3 = $r->top3_major_db ?? null;

            // لو Post والتوصيات ناقصة: نولِّدها من أعلى ذكاء بعدي فقط
            if ($isPost && (!$m1 || !$m2 || !$m3)) {
                $scores = [
                    'visual'        => (float) ($r->s_visual ?? 0),
                    'intrapersonal' => (float) ($r->s_intrapersonal ?? 0),
                    'kinesthetic'   => (float) ($r->s_kinesthetic ?? 0),
                    'social'        => (float) ($r->s_social ?? 0),
                    'logical'       => (float) ($r->s_logical ?? 0),
                    'naturalist'    => (float) ($r->s_naturalist ?? 0),
                    'linguistic'    => (float) ($r->s_linguistic ?? 0),
                    'musical'       => (float) ($r->s_musical ?? 0),
                ];

                // أعلى ذكاء مع كسر التعادل وفق $order
                $topKey = null;
                $max = -INF;
                foreach ($order as $k) {
                    $v = $scores[$k];
                    if ($v > $max) {
                        $max = $v;
                        $topKey = $k;
                    }
                }

                if ($topKey && isset($majorsMap[$topKey])) {
                    $auto = array_slice($majorsMap[$topKey], 0, 3);
                    $m1 = $m1 ?: ($auto[0] ?? '');
                    $m2 = $m2 ?: ($auto[1] ?? '');
                    $m3 = $m3 ?: ($auto[2] ?? '');
                }
            }

            fputcsv($handle, [
                $r->name ?? '',
                $phone,
                $r->email ?? '',
                $r->governorate ?? '',
                is_null($r->gpa) ? '' : number_format((float)$r->gpa, 2) . '%',
                $r->graduation_year ?? '',
                optional($r->created_at)->format('Y-m-d'),

                $r->s_social ?? '',
                $r->s_visual ?? '',
                $r->s_intrapersonal ?? '',
                $r->s_kinesthetic ?? '',
                $r->s_logical ?? '',
                $r->s_naturalist ?? '',
                $r->s_linguistic ?? '',
                $r->s_musical ?? '',

                $m1 ?: '',
                $m2 ?: '',
                $m3 ?: '',
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        $suffix   = $isPost ? 'post' : 'pre';
        $filename = "students_{$suffix}_export_" . now()->format('Y-m-d_His') . '.csv';

        return response($content, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    public function togglePostTest(Student $student)
    {
        if (!Schema::hasColumn('students', 'post_test_allowed')) {
            return back()->with('error', 'عمود post_test_allowed غير موجود. شغّل الهجرة أولاً.');
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

    public function bulkAllowPostTest(Request $request)
    {
        if (!Schema::hasColumn('students', 'post_test_allowed')) {
            return back()->with('error', 'عمود post_test_allowed غير موجود. شغّل الهجرة أولاً.');
        }

        $allowed = $request->boolean('allowed', true);
        $q = Student::query();

        if ($request->filled('governorate')) {
            $q->where('governorate', $request->governorate);
        }
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
