<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    // اسم الجدول (اختياري لو الاسم غير افتراضي)
    protected $table = 'students';

    /* توحيد أسماء الحقول للواجهات:
       name          ↔ full_name
       whatsapp      ↔ whatsapp_number
       grade         ↔ gpa
    */
    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? $this->attributes['full_name'] ?? null;
    }

    public function getWhatsappAttribute()
    {
        return $this->attributes['whatsapp'] ?? $this->attributes['whatsapp_number'] ?? null;
    }

    public function getGradeAttribute()
    {
        $v = $this->attributes['grade'] ?? $this->attributes['gpa'] ?? null;
        return is_null($v) ? null : (float) $v;
    }

    /**
     * كل نتائج الاختبارات (للاحتياط/التتبّع)
     */
    public function testResults(): HasMany
    {
        return $this->hasMany(\App\Models\TestResult::class, 'student_id');
    }

    /**
     * نتيجة الاختبارات الأحدث (قبلي/بعدي) — هذه التي نعرضها في صفحة التفاصيل.
     * latestOfMany() تجعل العلاقة دائمة تُعيد أحدث صف بحسب created_at.
     */
    public function testResult(): HasOne
    {
        return $this->hasOne(\App\Models\TestResult::class, 'student_id')->latestOfMany();
    }

    /**
     * التوصيات (أكثر 3 تخصصات مقترحة مخزنة)
     */
    public function recommendation(): HasOne
    {
        return $this->hasOne(\App\Models\StudentRecommendation::class, 'student_id');
    }
}
