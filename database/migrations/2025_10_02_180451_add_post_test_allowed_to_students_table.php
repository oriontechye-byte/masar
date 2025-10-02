<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // عمود من النوع boolean مع القيمة الافتراضية false
            $table->boolean('post_test_allowed')->default(false)->after('governorate');
            // ^ غيّر موقع after() إذا ما عندك عمود governorate أو تحب مكان مختلف
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('post_test_allowed');
        });
    }
};
