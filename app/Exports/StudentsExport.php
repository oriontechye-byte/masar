<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\Question;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;
    protected $testType;
    protected $questions;

    public function __construct(array $filters, string $testType = 'pre')
    {
        $this->filters = $filters;
        $this->testType = $testType;
        $this->questions = Question::orderBy('id')->get();
    }

    public function query()
    {
        $query = Student::with(['testResult', 'testResult.highestPostLectureIntelligenceType'])
                        ->whereHas('testResult');

        if (!empty($this->filters['governorate'])) {
            $query->where('governorate', $this->filters['governorate']);
        }
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        $headings = [
            'ID', 'الاسم', 'رقم الواتساب', 'المحافظة', 'المعدل', 'تاريخ التسجيل',
        ];

        foreach ($this->questions as $question) {
            $headings[] = 'س' . $question->id;
        }

        if ($this->testType === 'post') {
            $headings[] = 'أعلى ذكاء بعد المحاضرة';
            $headings[] = 'التخصصات المقترحة';
            $headings[] = 'المهن المقترحة';
        }

        return $headings;
    }

    public function map($student): array
    {
        $row = [
            $student->id, $student->name, $student->whatsapp_number, $student->governorate,
            $student->gpa, $student->created_at->format('Y-m-d'),
        ];

        // --- **هذا هو التعديل الرئيسي** ---
        // نتأكد أولاً من وجود نتيجة اختبار للطالب قبل المتابعة
        $scores = [];
        if ($student->testResult) {
            $scores_json = ($this->testType === 'post')
                ? $student->testResult->post_lecture_scores
                : $student->testResult->pre_lecture_scores;

            $scores = json_decode($scores_json, true) ?? [];
        }
        // --- نهاية التعديل ---

        foreach ($this->questions as $question) {
            // إذا لم نجد إجابة للسؤال (بسبب عدم وجود نتيجة مثلاً)، سنضع 0
            $row[] = $scores[$question->id] ?? 0;
        }

        if ($this->testType === 'post' && $student->testResult) {
            $highestIntelligence = $student->testResult->highestPostLectureIntelligenceType;
            if ($highestIntelligence) {
                $row[] = $highestIntelligence->name;
                $row[] = $highestIntelligence->careers;
                $row[] = $highestIntelligence->description;
            } else {
                $row[] = 'N/A';
                $row[] = 'N/A';
                $row[] = 'N/A';
            }
        }

        return $row;
    }
}