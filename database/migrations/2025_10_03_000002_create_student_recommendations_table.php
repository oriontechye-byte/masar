<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            // أفضل ثلاثة تخصّصات مقترحة (نص حر)
            $table->string('top1_major')->nullable();
            $table->string('top2_major')->nullable();
            $table->string('top3_major')->nullable();

            // اختياري: مصدر/ملاحظات/تاريخ التوليد
            $table->timestamp('generated_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_recommendations');
    }
};
