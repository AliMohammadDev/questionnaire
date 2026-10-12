<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * @return array<string, array{name: string, department: string, category: string, type: string, style: string, client: string, submitted_at: ?string, completed_at: ?string, budget: int, location: string, goal: string}>
     */
    public static function scenarios(): array
    {
        $rows = [
            ['DEMO-ARCH-RES', 'فيلا عائلة العلي', 'architecture', 'residential', 'villa', 'neoclassical', 'ahmad', '2026-09-01 09:00:00', null, 6500, 'ريف دمشق — ضاحية قدسيا', 'تصميم فيلا لعائلة من خمسة أفراد مع خصوصية للضيوف وحديقة قابلة للاستخدام طوال السنة.'],
            ['DEMO-ARCH-APT', 'إعادة توزيع شقة ليلى', 'architecture', 'residential', 'apartment', 'scandinavian', 'layla', null, null, 1800, 'حمص — حي الإنشاءات', 'إعادة توزيع شقة بمساحة 120 م² لتحسين الإضاءة والتخزين دون تعديل العناصر الإنشائية.'],
            ['DEMO-ARCH-COMPLEX', 'مجمع السهل السكني', 'architecture', 'residential', 'residential-complex', 'islamic', 'alnoor', '2026-09-01 09:00:00', null, 18000, 'حلب — ضاحية الشهباء', 'إعداد تصور معماري لمجمع من 24 شقة مع خدمات مشتركة ومواقف سيارات.'],
            ['DEMO-ARCH-COM', 'مطعم تنور وحطب', 'architecture', 'commercial', 'restaurant', 'industrial', 'tannour', '2026-09-01 09:00:00', null, 3200, 'دمشق — المزة', 'تصميم مطعم يتسع لـ60 ضيفاً مع فصل مسارات العملاء والخدمة والتوريد.'],
            ['DEMO-ARCH-RETAIL', 'متجر خيط للأزياء', 'architecture', 'commercial', 'retail-store', 'bohemian', 'khayt', '2026-09-01 09:00:00', null, 1700, 'حلب — العزيزية', 'تصميم متجر أزياء بمساحة 85 م² مع عرض واضح وغرفتي قياس ومستودع خلفي.'],
            ['DEMO-ARCH-OFFICE', 'مقر شركة النور', 'architecture', 'commercial', 'office-building', 'contemporary', 'alnoor', '2026-09-01 09:00:00', '2026-10-05 12:00:00', 12000, 'حلب — مركز المدينة', 'تصميم مقر إداري من ثلاثة طوابق لـ60 موظفاً مع استقبال وقاعات اجتماعات.'],
            ['DEMO-ARCH-IND', 'مصنع سنابل للتعبئة', 'architecture', 'industrial', 'factory', 'modern', 'sanabel', null, null, 8000, 'حلب — المدينة الصناعية', 'تنظيم مصنع لتعبئة المنتجات الغذائية مع فصل المواد الخام عن المنتج النهائي.'],
            ['DEMO-ARCH-WAREHOUSE', 'مستودع شركة المدار', 'architecture', 'industrial', 'warehouse', 'industrial', 'almadar', '2026-09-01 09:00:00', null, 4500, 'ريف دمشق — عدرا', 'تخطيط مستودع جاف بمساحة 1200 م² مع رفوف ومناطق تحميل ومسارات رافعات.'],
            ['DEMO-ARCH-WORKSHOP', 'ورشة خشب وزاوية', 'architecture', 'industrial', 'workshop', 'industrial', 'wood', '2026-09-01 09:00:00', '2026-10-05 12:00:00', 2200, 'حمص — المنطقة الصناعية', 'توزيع ورشة نجارة بمساحة 350 م² مع فصل القص والتجميع والدهان.'],
            ['DEMO-GRAPH-LOGO', 'شعار نواة', 'graphic-design', 'branding', 'logo', 'minimalist', 'nawat', null, null, 350, 'دمشق — خدمة تصميم عن بعد', 'تصميم شعار عربي واضح لمكتب استشارات ناشئ يعمل مع المشاريع الصغيرة.'],
            ['DEMO-GRAPH-BRAND', 'هوية استوديو ليلى', 'graphic-design', 'branding', 'brand-identity', 'minimalist', 'layla', '2026-09-01 09:00:00', '2026-10-05 12:00:00', 1200, 'حمص — خدمة تصميم عن بعد', 'تأسيس هوية لاستوديو تصوير تشمل الشعار وقوالب المنشورات وتطبيقات المراسلات.'],
            ['DEMO-GRAPH-GUIDE', 'دليل هوية شركة النور', 'graphic-design', 'branding', 'brand-guidelines', 'corporate', 'alnoor', '2026-09-01 09:00:00', null, 700, 'حلب — خدمة تصميم عن بعد', 'توثيق الهوية الحالية لتوحيد تطبيقها بين فريق التسويق والمطبعة.'],
            ['DEMO-GRAPH-PRINT', 'تغليف قهوة سنابل', 'graphic-design', 'print-design', 'packaging', 'classic', 'sanabel', '2026-09-01 09:00:00', null, 600, 'حلب — تنسيق مع مطبعة محلية', 'تصميم عبوة قهوة 250 غراماً جاهزة للطباعة مع بيانات المنتج والباركود.'],
            ['DEMO-GRAPH-BROCHURE', 'بروشور خدمات شركة النور', 'graphic-design', 'print-design', 'brochure', 'corporate', 'alnoor', '2026-09-01 09:00:00', '2026-10-05 12:00:00', 280, 'حلب — توزيع في المعارض', 'إعداد بروشور تعريفي مطوي يشرح أربع خدمات ويعرض وسائل التواصل.'],
            ['DEMO-GRAPH-CATALOG', 'كتالوج أثاث خشب وزاوية', 'graphic-design', 'print-design', 'catalog', 'contemporary', 'wood', '2026-09-01 09:00:00', null, 850, 'حمص — عرض للعملاء والموزعين', 'تصميم كتالوج من 20 صفحة لـ36 قطعة أثاث مع الرموز والأبعاد.'],
            ['DEMO-GRAPH-SOCIAL', 'محتوى متجر خيط الشهري', 'graphic-design', 'digital-marketing', 'social-media', 'playful', 'khayt', null, null, 450, 'حلب — إنستغرام وفيسبوك', 'إعداد محتوى بصري لشهر واحد يتضمن 12 منشوراً و8 قصص لمنتجات المتجر.'],
            ['DEMO-GRAPH-ADS', 'حملة افتتاح مطعم تنور', 'graphic-design', 'digital-marketing', 'ad-campaign', 'playful', 'tannour', '2026-09-01 09:00:00', null, 550, 'دمشق — حملة رقمية محلية', 'تصميم حملة افتتاح لمدة أسبوعين؛ الميزانية لخدمات التصميم دون شراء المساحات الإعلانية.'],
            ['DEMO-GRAPH-PRESENTATION', 'عرض استثماري لنواة', 'graphic-design', 'digital-marketing', 'presentation', 'corporate', 'nawat', '2026-09-01 09:00:00', '2026-10-05 12:00:00', 500, 'دمشق — اجتماع مستثمرين', 'تصميم عرض من 15 شريحة يوضح المنتج ونموذج الإيرادات وخطة النمو.'],
            ['DEMO-WEB-CORP', 'موقع شركة النور', 'web-design', 'corporate-websites', 'company-website', 'corporate', 'alnoor', '2026-09-01 09:00:00', null, 1800, 'حلب — جمهور عربي وإنجليزي', 'إطلاق موقع تعريفي يعرض الخدمات والمشاريع ويستقبل طلبات عروض الأسعار.'],
            ['DEMO-WEB-PORTFOLIO', 'معرض أعمال استوديو ليلى', 'web-design', 'corporate-websites', 'portfolio', 'minimalist', 'layla', null, null, 1100, 'حمص — عرض أعمال على الإنترنت', 'عرض 18 مشروع تصوير مع تصنيفات وصفحات تفاصيل ونموذج تواصل.'],
            ['DEMO-WEB-LANDING', 'صفحة استشارات نواة', 'web-design', 'corporate-websites', 'landing-page', 'contemporary', 'nawat', '2026-09-01 09:00:00', null, 650, 'دمشق — حملة استشارات', 'تحويل زوار الحملة إلى طلبات استشارة عبر نموذج مختصر واضح.'],
            ['DEMO-WEB-SHOP', 'متجر خيط الإلكتروني', 'web-design', 'e-commerce', 'online-store', 'modern', 'khayt', '2026-09-01 09:00:00', null, 2800, 'دمشق وحلب وحمص — شحن محلي', 'بيع 80 منتج أزياء مع إدارة المقاسات والمخزون والطلبات والشحن المحلي.'],
            ['DEMO-WEB-DIGITAL', 'متجر قوالب نواة', 'web-design', 'e-commerce', 'digital-products', 'minimalist', 'nawat', null, null, 2100, 'الإنترنت — منتجات رقمية بالعربية', 'بيع 24 قالباً رقمياً مع تسليم الملفات بعد تأكيد الدفع وإدارة حدود التنزيل.'],
            ['DEMO-WEB-MARKET', 'سوق الحرف اليدوية', 'web-design', 'e-commerce', 'marketplace', 'playful', 'almadar', '2026-09-01 09:00:00', null, 6500, 'سوريا — بائعون من عدة مدن', 'إطلاق سوق تجريبي لـ15 بائعاً مع مراجعة المنتجات وعمولة وتسوية دورية.'],
            ['DEMO-WEB-BOOK', 'منصة مواعيد نواة', 'web-design', 'service-platforms', 'booking-platform', 'minimalist', 'nawat', '2026-09-01 09:00:00', '2026-10-05 12:00:00', 2400, 'دمشق — حجز استشارات عن بعد', 'تنظيم مواعيد استشارات مدتها 30 دقيقة مع تذكيرات وإمكانية إعادة الجدولة.'],
            ['DEMO-WEB-LEARN', 'أكاديمية نواة', 'web-design', 'service-platforms', 'learning-platform', 'corporate', 'nawat', '2026-09-01 09:00:00', null, 4200, 'الإنترنت — دورات عربية', 'إطلاق خمس دورات مسجلة مع اختبارات وتتبع تقدم وشهادات إكمال.'],
            ['DEMO-WEB-PORTAL', 'بوابة عملاء شركة النور', 'web-design', 'service-platforms', 'customer-portal', 'corporate', 'alnoor', '2026-09-01 09:00:00', '2026-10-05 12:00:00', 3300, 'حلب — عملاء الشركة', 'تمكين العميل من متابعة مشاريعه وفواتيره ومرفقاته وإرسال طلبات دعم.'],
        ];
        $scenarios = [];
        foreach ($rows as [$code, $name, $department, $category, $type, $style, $client, $submittedAt, $completedAt, $budget, $location, $goal]) {
            $scenarios[$code] = compact('name', 'department', 'category', 'type', 'style', 'client', 'budget', 'location', 'goal') + ['submitted_at' => $submittedAt, 'completed_at' => $completedAt];
        }

        return $scenarios;
    }

    /** @param array<string, string> $criteria */
    public static function catalogRecordId(string $foreignKey, array $criteria): ?int
    {
        foreach (self::scenarios() as $code => $scenario) {
            if (array_intersect_assoc($scenario, $criteria) !== $criteria) {
                continue;
            }
            $recordId = Project::where('project_code', $code)->value($foreignKey);
            if ($recordId !== null) {
                return (int) $recordId;
            }
        }

        return null;
    }

    public function run(): void
    {
        foreach (self::scenarios() as $code => $data) {
            $department = DepartmentSeeder::department($data['department']);
            $category = ProjectCategorySeeder::category($data['department'], $data['category']);
            $type = ProjectTypeSeeder::type($data['department'], $data['category'], $data['type']);
            Project::updateOrCreate(['project_code' => $code], [
                'project_name' => $data['name'].' — تجريبي',
                'client_id' => Client::where('email', $data['client'].'.demo@example.test')->sole()->id,
                'department_id' => $department->id,
                'project_category_id' => $category->id,
                'project_type_id' => $type->id,
                'design_style_id' => DesignStyleSeeder::style($data['style'])->id,
                'notes' => 'سيناريو وهمي للتدريب والعرض. الميزانية لخدمات التصميم والتطوير بالدولار الأمريكي. '.$data['goal'],
                'submitted_at' => $data['submitted_at'],
                'completed_at' => $data['completed_at'],
            ]);
        }
    }
}
