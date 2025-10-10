<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            // نضيف كل عمود فقط إذا لم يكن موجودًا
            if (!Schema::hasColumn('test_results', 'post_score_social')) {
                $table->integer('post_score_social')->nullable()->after('score_musical');
            }
            if (!Schema::hasColumn('test_results', 'post_score_visual')) {
                $table->integer('post_score_visual')->nullable()->after('post_score_social');
            }
            if (!Schema::hasColumn('test_results', 'post_score_intrapersonal')) {
                $table->integer('post_score_intrapersonal')->nullable()->after('post_score_visual');
            }
            if (!Schema::hasColumn('test_results', 'post_score_kinesthetic')) {
                $table->integer('post_score_kinesthetic')->nullable()->after('post_score_intrapersonal');
            }
            if (!Schema::hasColumn('test_results', 'post_score_logical')) {
                $table->integer('post_score_logical')->nullable()->after('post_score_kinesthetic');
            }
            if (!Schema::hasColumn('test_results', 'post_score_naturalist')) {
                $table->integer('post_score_naturalist')->nullable()->after('post_score_logical');
            }
            if (!Schema::hasColumn('test_results', 'post_score_linguistic')) {
                $table->integer('post_score_linguistic')->nullable()->after('post_score_naturalist');
            }
            if (!Schema::hasColumn('test_results', 'post_score_musical')) {
                $table->integer('post_score_musical')->nullable()->after('post_score_linguistic');
            }

            // FK للذكاء الأعلى بعد المحاضرة (نضيفه فقط إن ما كان موجود)
            if (!Schema::hasColumn('test_results', 'highest_post_lecture_intelligence_type_id')) {
                $table->foreignId('highest_post_lecture_intelligence_type_id')
                      ->nullable()
                      ->after('post_score_musical')
                      ->constrained('intelligence_types')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            // نحذف الأعمدة فقط إن كانت موجودة
            $cols = [
                'post_score_social',
                'post_score_visual',
                'post_score_intrapersonal',
                'post_score_kinesthetic',
                'post_score_logical',
                'post_score_naturalist',
                'post_score_linguistic',
                'post_score_musical',
                'highest_post_lecture_intelligence_type_id',
            ];

            // إسقاط الـ FK إذا كان موجود
            if (Schema::hasColumn('test_results', 'highest_post_lecture_intelligence_type_id')) {
                try {
                    $table->dropForeign('test_results_highest_post_lecture_intelligence_type_id_foreign');
                } catch (\Throwable $e) {
                    // تجاهل لو الاسم مختلف في بعض إصدارات MySQL
                    try { $table->dropForeign(['highest_post_lecture_intelligence_type_id']); } catch (\Throwable $e2) {}
                }
            }

            foreach ($cols as $c) {
                if (Schema::hasColumn('test_results', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
