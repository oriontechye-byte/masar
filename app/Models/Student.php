<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TestResult; // This is the line that fixes the error

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name',
        'whatsapp_number',
        'email',
        'governorate',
        'gpa',
        'graduation_year',
    ];

    /**
     * Get the test result associated with the student.
     */
    public function testResult()
    {
        return $this->hasOne(TestResult::class);
    }
}