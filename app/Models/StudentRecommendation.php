<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentRecommendation extends Model
{
    protected $fillable = [
        'student_id', 'top1_major', 'top2_major', 'top3_major', 'generated_at',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
