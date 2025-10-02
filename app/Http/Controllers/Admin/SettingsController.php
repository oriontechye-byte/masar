<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\SiteSetting;

class SettingsController extends Controller
{
    /**
     * فتح/قفل الاختبار البعدي بشكل عام (لكل المستخدمين).
     * يستقبل حقل enabled = 1 أو 0 من النموذج.
     */
    public function togglePostTestGlobal(Request $request)
    {
        $enabled = $request->boolean('enabled', false);

        // تأكد أن جدول الإعدادات موجود
        if (!Schema::hasTable('site_settings')) {
            return back()->with('error', 'جدول site_settings غير موجود. شغّل المايجريشن أولاً.');
        }

        // خزّن القيمة
        SiteSetting::set('post_test_global_enabled', $enabled ? '1' : '0');

        return back()->with(
            'success',
            $enabled ? 'تم فتح الاختبار البعدي للجميع.' : 'تم قفل الاختبار البعدي للجميع.'
        );
    }
}
