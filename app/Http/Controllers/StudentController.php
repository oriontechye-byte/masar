<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;
use App\Models\SiteSetting;

class StudentController extends Controller
{
    /**
     * هل الاختبار البعدي مفتوح عالميًا؟
     */
    protected function isPostTestGloballyEnabled(): bool
    {
        if (!Schema::hasTable('site_settings')) {
            return false;
        }
        return SiteSetting::get('post_test_global_enabled', '0') === '1';
    }

    /**
     * مساعد: الحصول على معرف الطالب من الجلسة للعرض العام، أو 403.
     */
    protected function viewerIdOrAbort(): int
    {
        $id = (int) (Session::get('viewer_student_id') ?? 0);
        abort_unless($id > 0, 403, 'غير مصرح لك بعرض هذه النتائج.');
        return $id;
    }

    /**
     * نموذج التسجيل (اختبار قبلي)
     */
    public function showRegistrationForm()
    {
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
     * حفظ التسجيل وإرسال الطالب للاختبار القبلي
     */
    public function register(Request $request)
    {
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

        Session::put('student_registration_data', $validatedData);
        Session::put('test_type_for_test', 'pre');

        return redirect()->route('test.show');
    }

    /**
     * نموذج البحث للاختبار البعدي
     */
    public function showPostTestLookupForm()
    {
        if (!$this->isPostTestGloballyEnabled()) {
            return view('post_test_locked'); // صفحة القفل العام
        }

        return view('post_test_lookup');     // نموذج إدخال الرقم
    }

    /**
     * التحقق من رقم الهاتف وإرسال الطالب للاختبار البعدي
     */
    public function handlePostTestLookup(Request $request)
    {
        // حماية من الدخول المباشر عند القفل العام
        if (!$this->isPostTestGloballyEnabled()) {
            return view('post_test_locked');
        }

        $validatedData = $request->validate([
            'whatsapp_number' => 'required|string|exists:students,whatsapp_number',
        ], [
            'whatsapp_number.required' => 'الرجاء إدخال رقم الهاتف.',
            'whatsapp_number.exists'   => 'هذا الرقم غير مسجل لدينا، تأكد من إدخاله بشكل صحيح.',
        ]);

        $student = Student::where('whatsapp_number', $validatedData['whatsapp_number'])->first();

        /** ✅ خزّن معرف الطالب للعرض (يحل مشكلة 403 للنتائج/التقرير) */
        Session::put('viewer_student_id', $student->id);

        // ✅ لازم يكون عنده اختبار قبلي (سجل نتائج موجود)
        $result = $student->testResult;
        if (!$result) {
            return back()->withErrors([
                'whatsapp_number' => 'لا يمكنك دخول الاختبار البعدي قبل إكمال الاختبار القبلي.'
            ])->withInput();
        }

        // ✅ إذا الطالب أنهى البعدي سابقًا → أرسله لتقرير التطور مباشرة (بدون ID في الرابط)
        $hasPost = collect([
            $result->post_score_social,
            $result->post_score_visual,
            $result->post_score_intrapersonal,
            $result->post_score_kinesthetic,
            $result->post_score_logical,
            $result->post_score_naturalist,
            $result->post_score_linguistic,
            $result->post_score_musical,
        ])->filter(fn($v) => !is_null($v))->isNotEmpty();

        if ($hasPost) {
            return redirect()->route('growth.report')
                ->with('info', 'لقد أكملت الاختبار البعدي سابقًا — هذا تقرير تطوّرك.');
        }

        // ✅ تجهيز الجلسة للاختبار البعدي
        Session::put('student_id_for_test', $student->id);
        Session::put('test_type_for_test', 'post');

        return redirect()->route('test.show');
    }

    /**
     * عرض النتائج الاعتيادية (تقرأ القيم المئوية المخزّنة مباشرة)
     */
    public function showStudentResults($student_id)
    {
        $student = Student::findOrFail($student_id);
        $result  = $student->testResult;

        if (!$result) {
            return redirect()->route('landing')->with('error', 'لم يتم العثور على نتائج لهذا الطالب.');
        }

        $intelligenceTypes = \App\Models\IntelligenceType::all()->keyBy('id');

        // خريطة الحقول حسب IDs
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
     * تقرير التطوّر (بعد البعدي)
     */
    public function showGrowthReport($student_id)
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

        $prePercents  = [];
        $postPercents = [];
        $hasAnyPost   = false;

        foreach ($intelligenceTypes as $id => $type) {
            $key = $typeMap[$id] ?? null;
            if (!$key) continue;

            // ✅ تصحيح السهو: score_ وليس "score "
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

        if (!$hasAnyPost) {
            // ✅ المسار الجديد بدون ID
            return redirect()->route('results.show')
                ->with('info', 'لا يمكن عرض تقرير التطوّر قبل إكمال الاختبار البعدي.');
        }

        $growth = [];
        foreach ($intelligenceTypes as $id => $type) {
            $pre  = $prePercents[$id]  ?? 0;
            $post = $postPercents[$id] ?? null;
            $growth[$id] = is_null($post) ? null : ($post - $pre);
        }

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

        // أعلى 3 ذكاءات حسب البعدي
        $postForSort = [];
        foreach ($postPercents as $id => $val) {
            if (!is_null($val)) $postForSort[$id] = $val;
        }
        arsort($postForSort);
        $top3Ids = array_slice(array_keys($postForSort), 0, 3, true);

        $top3 = [];
        foreach ($top3Ids as $id) {
            $type = $intelligenceTypes[$id];

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

        // أعلى 3 مسارات/مهارات
        $skillScores = [];
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

        // بيانات الرسم البياني (اختياريًا)
        $chartLabels = [];
        $chartPre    = [];
        $chartPost   = [];
        foreach ($intelligenceTypes->sortBy('id') as $id => $type) {
            $chartLabels[] = $type->name ?? ('ID '.$id);
            $chartPre[]    = (int)($prePercents[$id] ?? 0);
            $chartPost[]   = is_null($postPercents[$id]) ? 0 : (int)$postPercents[$id];
        }

        return view('growth_report', [
            'student'           => $student,
            'intelligenceTypes' => $intelligenceTypes,
            'prePercents'       => $prePercents,
            'postPercents'      => $postPercents,
            'growth'            => $growth,
            'tableRows'         => $tableRows,
            'top3'              => $top3,
            'topThree'          => $topThree,
            'topSkills'         => $topSkills,
            'chartLabels'       => $chartLabels,
            'chartPre'          => $chartPre,
            'chartPost'         => $chartPost,
        ]);
    }

    /**
     * تصدير PDF (تستخدم القيم المئوية المخزّنة 0..100)
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

    /* ===================== دوال بدون معرف في الرابط ===================== */

    public function showOwnResults()
    {
        $id = $this->viewerIdOrAbort();
        return $this->showStudentResults($id);
    }

    public function showOwnGrowthReport()
    {
        $id = $this->viewerIdOrAbort();
        return $this->showGrowthReport($id);
    }

    public function exportOwnPdf()
    {
        $id = $this->viewerIdOrAbort();
        return $this->exportPdf($id);
    }
}
