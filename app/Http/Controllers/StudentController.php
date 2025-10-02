<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    /**
     * عرض نموذج تسجيل الطالب (اختبار قبلي)
     */
    public function showRegistrationForm()
    {
        // قائمة المحافظات + سنوات التخرج (آخر 10 سنوات)
        $governorates = [
            'أمانة العاصمة', 'صنعاء', 'عدن', 'تعز', 'الحديدة', 'إب', 'ذمار', 'حضرموت',
            'لحج', 'أبين', 'شبوة', 'المهرة', 'مأرب', 'الجوف', 'البيضاء', 'حجة',
            'صعدة', 'المحويت', 'عمران', 'الضالع', 'ريمة', 'سقطرى'
        ];

        $currentYear = date('Y');
        $years       = range($currentYear, $currentYear - 10);

        return view('register', compact('governorates', 'years'));
    }

    /**
     * حفظ بيانات التسجيل للجلسة ثم التوجيه لصفحة الاختبار (قبلي)
     */
    public function register(Request $request)
    {
        // التحقق من البيانات الأساسية للتسجيل
        $validatedData = $request->validate([
            'full_name'        => 'required|string|max:255',
            'whatsapp_number'  => 'required|string|max:20|unique:students,whatsapp_number',
            'email'            => 'nullable|email|max:255',
            'governorate'      => 'required|string|max:255',
            'gpa'              => 'required|numeric|min:0|max:100',
            'graduation_year'  => 'required|integer',
        ], [
            'full_name.required'       => 'حقل الاسم الكامل مطلوب.',
            'whatsapp_number.required' => 'حقل رقم الهاتف مطلوب.',
            'whatsapp_number.unique'   => 'رقم الهاتف هذا مسجل بالفعل.',
            'governorate.required'     => 'حقل المحافظة مطلوب.',
            'gpa.required'             => 'حقل المعدل مطلوب.',
            'gpa.numeric'              => 'حقل المعدل يجب أن يكون رقماً صحيحاً أو عشرياً.',
            'graduation_year.required' => 'حقل سنة التخرج مطلوب.',
        ]);

        // نحفظ بيانات الطالب مؤقتاً ونعرف أن نوع الاختبار "قبلي"
        Session::put('student_registration_data', $validatedData);
        Session::put('test_type_for_test', 'pre');

        return redirect()->route('test.show');
    }

    /**
     * نموذج البحث للدخول للاختبار البعدي
     */
    public function showPostTestLookupForm()
    {
        return view('post_test_lookup');
    }

    /**
     * التحقق من رقم الطالب وتوجيهه للاختبار البعدي
     */
    public function handlePostTestLookup(Request $request)
    {
        // نتحقق بالرقم (الرسائل تقول "رقم الهاتف")
        $validatedData = $request->validate([
            'whatsapp_number' => 'required|string|exists:students,whatsapp_number',
        ], [
            'whatsapp_number.required' => 'الرجاء إدخال رقم الهاتف.',
            'whatsapp_number.exists'   => 'هذا الرقم غير مسجل لدينا، تأكد من إدخاله بشكل صحيح.',
        ]);

        $student = Student::where('whatsapp_number', $validatedData['whatsapp_number'])->first();

        // لو الطالب أكمل البعدي سابقاً نرجّعه للنتيجة
        $testResult = $student->testResult;
        if ($testResult && $testResult->post_score_social !== null) {
            return redirect()->route('results.show', $student->id)
                ->with('info', 'لقد أكملت الاختبار البعدي مسبقاً. هذه هي نتيجتك.');
        }

        // إعداد الجلسة للاختبار البعدي
        Session::put('student_id_for_test', $student->id);
        Session::put('test_type_for_test', 'post');

        return redirect()->route('test.show');
    }

    /**
     * صفحة النتائج الاعتيادية
     * ملاحظة مهمّة: الحقول score_* و post_score_* مخزنة أصلاً كنِسب (0..100)
     * لذلك نقرأها مباشرة مع clamp لضمان النطاق، بدون تحويل إضافي.
     */
    public function showStudentResults($student_id)
    {
        $student = Student::findOrFail($student_id);
        $result  = $student->testResult;

        if (!$result) {
            return redirect()->route('landing')->with('error', 'لم يتم العثور على نتائج لهذا الطالب.');
        }

        $intelligenceTypes = \App\Models\IntelligenceType::all()->keyBy('id');

        // خريطة الحقول
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

        $clamp = fn($v) => max(0, min(100, (int)$v));

        $preScores  = [];
        $postScores = null;

        foreach ($intelligenceTypes as $id => $type) {
            $key = $typeMap[$id] ?? null;
            if (!$key) continue;

            $pre  = $result->{'score_' . $key} ?? 0;
            $post = $result->{'post_score_' . $key} ?? null;

            $preScores[$id] = $clamp($pre);
            if (!is_null($post)) {
                $postScores[$id] = $clamp($post);
            }
        }

        // نعرض البعدي إن وُجد وإلا القبلي
        $scores = $postScores ?? $preScores;
        arsort($scores);

        // من باب التوافق مع الواجهة الحالية
        $prePercents  = $preScores;
        $postPercents = $postScores;

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

    /**
     * تقرير التطوّر (بعد الاختبار البعدي)
     * - كل شيء يُقرأ كنسب مخزّنة (0..100) مع clamp، بلا إعادة تحويل ثانية.
     * - يبني $tableRows للجدول، $topThree لأعلى 3 ذكاءات،
     *   و $topSkills (أعلى 3 مهارات/مسارات) قبل الذكاءات.
     */
    public function showGrowthReport($student_id)
    {
        // (1) الطالب + نتيجته
        $student = Student::findOrFail($student_id);
        $result  = $student->testResult;

        if (!$result) {
            return redirect()->route('landing')->with('error', 'لم يتم العثور على نتائج لهذا الطالب.');
        }

        // (2) أنواع الذكاء مفهرسة
        $intelligenceTypes = \App\Models\IntelligenceType::all()->keyBy('id');

        // (3) خريطة الحقول
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

        $clamp = fn($v) => max(0, min(100, (int)$v));

        // (4) قراءة (قبلي/بعدي) من المخزن مباشرة
        $prePercents  = [];
        $postPercents = [];
        $hasAnyPost   = false;

        foreach ($intelligenceTypes as $id => $type) {
            $key = $typeMap[$id] ?? null;
            if (!$key) continue;

            $pre  = $result->{'score_' . $key} ?? 0;
            $post = $result->{'post_score_' . $key} ?? null;

            $prePercents[$id] = $clamp($pre);
            if (!is_null($post)) {
                $postPercents[$id] = $clamp($post);
                $hasAnyPost = true;
            } else {
                $postPercents[$id] = null;
            }
        }

        // (5) إن لم يوجد أي بعدي -> نرجّع الطالب لنتيجته
        if (!$hasAnyPost) {
            return redirect()->route('results.show', $student->id)
                ->with('info', 'لا يمكن عرض تقرير التطوّر قبل إكمال الاختبار البعدي.');
        }

        // (6) مقدار التغيّر لكل نوع
        $growth = [];
        foreach ($intelligenceTypes as $id => $type) {
            $pre  = $prePercents[$id]  ?? 0;
            $post = $postPercents[$id] ?? null;
            $growth[$id] = is_null($post) ? null : ($post - $pre);
        }

        // (7) صفوف الجدول
        $tableRows = [];
        foreach ($intelligenceTypes as $id => $type) {
            $tableRows[] = [
                'id'   => $id,
                'name' => $type->name ?? ('ID ' . $id),
                'pre'  => (int) ($prePercents[$id] ?? 0),
                'post' => is_null($postPercents[$id]) ? null : (int) $postPercents[$id],
                'diff' => is_null($postPercents[$id]) ? null : ((int)$postPercents[$id] - (int)($prePercents[$id] ?? 0)),
            ];
        }

        // (8) أعلى 3 ذكاءات (حسب بعدي)
        $postForSort = [];
        foreach ($postPercents as $id => $val) {
            if (!is_null($val)) $postForSort[$id] = $val;
        }
        arsort($postForSort);
        $top3Ids = array_slice(array_keys($postForSort), 0, 3, true);

        $top3 = [];
        foreach ($top3Ids as $id) {
            $type = $intelligenceTypes[$id];

            // careers: نفصلها بفواصل عربية/إنجليزية
            $careersText = (string) ($type->careers ?? '');
            $careers = collect(preg_split('/[,،]+/u', $careersText))
                ->map(fn($s) => trim($s))
                ->filter()
                ->values()
                ->all();

            $pre  = (int) ($prePercents[$id] ?? 0);
            $post = (int) ($postPercents[$id] ?? 0);

            $top3[] = [
                'id'            => $id,
                'name'          => $type->name ?? ('ID ' . $id),
                'description'   => $type->description ?? '',
                'post_percent'  => $post,
                'pre_percent'   => $pre,
                'diff_percent'  => $post - $pre,
                'careers'       => $careers,
            ];
        }
        $topThree = $top3;

        // (9) أعلى 3 مهارات/مسارات مناسبة — نقيّم كل مسار بنسبة الذكاء البعدي المرتبط
        $skillScores = []; // name => ['score'=>.., 'source_id'=>..]
        foreach ($intelligenceTypes as $id => $type) {
            $post = $postPercents[$id] ?? null;
            if (is_null($post)) continue;

            $careersText = (string)($type->careers ?? '');
            $careers = collect(preg_split('/[,،]+/u', $careersText))
                ->map(fn($s) => trim($s))
                ->filter()
                ->values()
                ->all();

            foreach ($careers as $c) {
                if (!isset($skillScores[$c]) || $post > $skillScores[$c]['score']) {
                    $skillScores[$c] = ['score' => (int)$post, 'source_id' => $id];
                }
            }
        }
        // ترتيب وأخذ أفضل 3
        uasort($skillScores, fn($a,$b)=> $b['score'] <=> $a['score']);
        $topSkills = [];
        $picked = 0;
        foreach ($skillScores as $name => $meta) {
            $topSkills[] = [
                'name'    => $name,
                'percent' => $meta['score'],
                'typeId'  => $meta['source_id'],
                'type'    => $intelligenceTypes[$meta['source_id']]->name ?? '—',
            ];
            if (++$picked === 3) break;
        }

        // (10) بيانات الرسم البياني (لو رجّعته لاحقًا)
        $chartLabels = [];
        $chartPre    = [];
        $chartPost   = [];
        foreach ($intelligenceTypes->sortBy('id') as $id => $type) {
            $chartLabels[] = $type->name ?? ('ID '.$id);
            $chartPre[]    = (int)($prePercents[$id] ?? 0);
            $chartPost[]   = is_null($postPercents[$id]) ? 0 : (int)$postPercents[$id];
        }

        // (11) تمرير البيانات للواجهة
        return view('growth_report', [
            'student'           => $student,
            'intelligenceTypes' => $intelligenceTypes,
            'prePercents'       => $prePercents,
            'postPercents'      => $postPercents,
            'growth'            => $growth,
            'tableRows'         => $tableRows,   // للجدول
            'top3'              => $top3,
            'topThree'          => $topThree,    // بطاقات أعلى 3 ذكاءات
            'topSkills'         => $topSkills,   // ✅ بطاقات أعلى 3 مهارات/مسارات
            'chartLabels'       => $chartLabels,
            'chartPre'          => $chartPre,
            'chartPost'         => $chartPost,
        ]);
    }

    /**
     * تصدير PDF — نستخدم القيم المئوية المخزّنة مباشرة (0..100)
     */
    public function exportPdf($student_id)
    {
        $student = Student::findOrFail($student_id);
        $result  = $student->testResult;

        if (!$result) {
            return redirect()->route('landing')->with('error', 'لم يتم العثور على نتائج لهذا الطالب.');
        }

        $intelligenceTypes = \App\Models\IntelligenceType::all()->keyBy('id');

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

        $clamp = fn($v) => max(0, min(100, (int)$v));

        // نقرأ المئويّات مباشرة
        $prePercents  = [];
        $postPercents = null;

        foreach ($intelligenceTypes as $id => $type) {
            $key = $typeMap[$id] ?? null;
            if (!$key) continue;

            $pre  = $result->{'score_' . $key} ?? 0;
            $post = $result->{'post_score_' . $key} ?? null;

            $prePercents[$id] = $clamp($pre);
            if (!is_null($post)) {
                $postPercents[$id] ??= [];
                $postPercents[$id] = $clamp($post);
            }
        }

        $currentPercents = $postPercents ?? $prePercents;
        arsort($currentPercents);

        $overallAvg = count($currentPercents) ? round(array_sum($currentPercents) / count($currentPercents)) : 0;

        $isPost    = !is_null($postPercents);
        $testLabel = $isPost ? 'الاختبار البَعدي' : 'الاختبار القبلي';

        $pdf = Pdf::loadView('results_pdf', [
            'student'           => $student,
            'intelligenceTypes' => $intelligenceTypes,
            'percents'          => $currentPercents,
            'overallAvg'        => $overallAvg,
            'testLabel'         => $testLabel,
            'generatedAt'       => now()->format('Y-m-d H:i'),
        ])->setPaper('a4', 'portrait');

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'dejavusans',
        ]);

        $fileName = 'Masar-Results-' . ($student->full_name ?? 'Student') . '.pdf';
        return $pdf->download($fileName);
    }
}
