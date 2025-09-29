<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\IntelligenceType; // <-- هذا هو السطر المهم الذي يحل المشكلة

class TestResult extends Model
{
    use HasFactory;

    /**
     * Get the student that owns the test result.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the highest post-lecture intelligence type.
     */
    public function highestPostLectureIntelligenceType()
    {
        // هذا السطر يفترض أن لديك عموداً في قاعدة البيانات اسمه
        // highest_post_lecture_intelligence_type_id
        // ليخزن فيه رقم أعلى ذكاء للطالب
        return $this->belongsTo(IntelligenceType::class, 'highest_post_lecture_intelligence_type_id');
    }
}