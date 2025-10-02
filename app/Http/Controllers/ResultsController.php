<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // استدعاء DomPDF
use App\Models\Student; // عدل حسب اسم الموديل عندك
use App\Models\IntelligenceType; // عدل حسب اسم الموديل عندك


class ResultsController extends Controller
{
public function download($studentId)
{
$student = Student::findOrFail($studentId);


// TODO: اجلب نتائج الطالب (قبلي/بعدي)
$scores = $this->fetchScoresFor($student);


// اجلب أنواع الذكاءات
$intelligenceTypes = IntelligenceType::all()->keyBy('id');


$rawMax = 100; // عدل لو في عندك مقياس مختلف


$percents = [];
foreach ((array) $scores as $typeId => $value) {
$p = $rawMax > 0 ? round(((float)$value / $rawMax) * 100) : 0;
$p = max(0, min(100, $p));
$percents[$typeId] = $p;
}


$overallAvg = count($percents) ? round(array_sum($percents)/count($percents)) : 0;
$testLabel = $this->hasPost($student) ? 'الاختبار البَعدي' : 'الاختبار القبلي';


$data = [
'student' => $student,
'intelligenceTypes' => $intelligenceTypes,
'percents' => $percents,
'overallAvg' => $overallAvg,
'testLabel' => $testLabel,
'generatedAt' => now()->format('Y-m-d H:i'),
];


// عرض القالب pdf
$pdf = Pdf::loadView('results_pdf', $data)->setPaper('a4', 'portrait');
return $pdf->download('نتيجتي.pdf');
}


private function fetchScoresFor($student): array
{
// عدل حسب بنية البيانات عندك
return (array) ($student->post_scores ?: $student->pre_scores ?: []);
}


private function hasPost($student): bool
{
return !empty($student->post_scores);
}
}