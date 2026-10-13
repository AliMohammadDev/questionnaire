<?php

namespace Database\Seeders;

use App\Models\AnswerType;
use App\Models\Question;
use App\Models\QuestionSection;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /** @return array<string, array<string, mixed>> */
    public static function definitions(): array
    {
        $questions = [
            'location' => [
                'title' => 'ما موقع المشروع أو نطاق تقديم الخدمة؟',
                'type' => 'short_text',
                'section' => 'المعلومات الأساسية',
                'contexts' => [
                    [],
                ],
                'options' => [],
                'validation' => null,
                'required' => true,
            ],
            'goal' => [
                'title' => 'ما الهدف الرئيسي من المشروع؟',
                'type' => 'paragraph',
                'section' => 'المعلومات الأساسية',
                'contexts' => [
                    [],
                ],
                'options' => [],
                'validation' => null,
                'required' => true,
            ],
            'budget' => [
                'title' => 'ما الميزانية التقديرية للمشروع؟',
                'type' => 'number',
                'section' => 'الميزانية والجدول الزمني',
                'contexts' => [
                    [],
                ],
                'options' => [],
                'validation' => [
                    'min' => 0,
                ],
                'required' => false,
            ],
            'start-date' => [
                'title' => 'ما التاريخ المفضل لبدء العمل؟',
                'type' => 'date',
                'section' => 'الميزانية والجدول الزمني',
                'contexts' => [
                    [],
                ],
                'options' => [],
                'validation' => null,
                'required' => false,
            ],
            'meeting-time' => [
                'title' => 'ما الوقت المناسب للاجتماع؟',
                'type' => 'time',
                'section' => 'الميزانية والجدول الزمني',
                'contexts' => [
                    [],
                ],
                'options' => [],
                'validation' => null,
                'required' => false,
            ],
            'meeting-duration' => [
                'title' => 'ما مدة الاجتماع المفضلة؟',
                'type' => 'duration',
                'section' => 'الميزانية والجدول الزمني',
                'contexts' => [
                    [],
                ],
                'options' => [],
                'validation' => [
                    'format' => 'HH:MM:SS',
                ],
                'required' => false,
            ],
            'priority' => [
                'title' => 'ما أولوية المشروع؟',
                'type' => 'dropdown',
                'section' => 'الميزانية والجدول الزمني',
                'contexts' => [
                    [],
                ],
                'options' => [
                    'normal' => 'مرونة في موعد التسليم',
                    'urgent' => 'تسليم عاجل',
                    'phased' => 'تنفيذ على مراحل',
                ],
                'validation' => null,
                'required' => false,
            ],
            'land-area' => [
                'title' => 'ما مساحة الأرض بالمتر المربع؟',
                'type' => 'number',
                'section' => 'المتطلبات المعمارية',
                'contexts' => [
                    [
                        'department' => 'architecture',
                    ],
                ],
                'options' => [],
                'validation' => [
                    'min' => 1,
                ],
                'required' => false,
            ],
            'floors' => [
                'title' => 'كم عدد الطوابق المطلوبة؟',
                'type' => 'number',
                'section' => 'المتطلبات المعمارية',
                'contexts' => [
                    [
                        'department' => 'architecture',
                    ],
                ],
                'options' => [],
                'validation' => [
                    'min' => 1,
                    'max' => 50,
                ],
                'required' => false,
            ],
            'bedrooms' => [
                'title' => 'كم عدد غرف النوم المطلوبة؟',
                'type' => 'number',
                'section' => 'المتطلبات المعمارية',
                'contexts' => [
                    [
                        'department' => 'architecture',
                        'category' => 'residential',
                    ],
                ],
                'options' => [],
                'validation' => [
                    'min' => 1,
                    'max' => 30,
                ],
                'required' => false,
            ],
            'residential-spaces' => [
                'title' => 'ما الفراغات الإضافية المطلوبة؟',
                'type' => 'multiple_choice',
                'section' => 'المتطلبات المعمارية',
                'contexts' => [
                    [
                        'department' => 'architecture',
                        'category' => 'residential',
                    ],
                ],
                'options' => [
                    'garden' => 'حديقة',
                    'office' => 'مكتب منزلي',
                    'gym' => 'صالة رياضية',
                    'guest-room' => 'غرفة ضيوف',
                    'garage' => 'مرآب',
                ],
                'validation' => null,
                'required' => false,
            ],
            'pool' => [
                'title' => 'هل ترغب بإضافة مسبح؟',
                'type' => 'boolean',
                'section' => 'المتطلبات المعمارية',
                'contexts' => [
                    [
                        'department' => 'architecture',
                        'category' => 'residential',
                        'type' => 'villa',
                    ],
                ],
                'options' => [],
                'validation' => null,
                'required' => false,
            ],
            'pool-area' => [
                'title' => 'ما مساحة المسبح المطلوبة بالمتر المربع؟',
                'type' => 'number',
                'section' => 'المتطلبات المعمارية',
                'contexts' => [
                    [
                        'department' => 'architecture',
                        'category' => 'residential',
                        'type' => 'villa',
                    ],
                ],
                'options' => [],
                'validation' => [
                    'min' => 5,
                    'max' => 500,
                ],
                'required' => false,
            ],
            'commercial-activity' => [
                'title' => 'ما النشاط التجاري للمشروع؟',
                'type' => 'single_choice',
                'section' => 'المتطلبات المعمارية',
                'contexts' => [
                    [
                        'department' => 'architecture',
                        'category' => 'commercial',
                    ],
                ],
                'options' => [
                    'food' => 'مطعم أو مقهى',
                    'retail' => 'بيع بالتجزئة',
                    'offices' => 'مكاتب وخدمات',
                    'other' => 'نشاط آخر',
                ],
                'validation' => null,
                'required' => false,
            ],
            'customer-capacity' => [
                'title' => 'كم عدد العملاء المتوقع استقبالهم في الوقت نفسه؟',
                'type' => 'number',
                'section' => 'المتطلبات المعمارية',
                'contexts' => [
                    [
                        'department' => 'architecture',
                        'category' => 'commercial',
                    ],
                ],
                'options' => [],
                'validation' => [
                    'min' => 1,
                ],
                'required' => false,
            ],
            'restaurant-kitchen' => [
                'title' => 'ما تجهيزات المطبخ ومسار الخدمة المطلوب؟',
                'type' => 'paragraph',
                'section' => 'المتطلبات المعمارية',
                'contexts' => [
                    [
                        'department' => 'architecture',
                        'category' => 'commercial',
                        'type' => 'restaurant',
                    ],
                ],
                'options' => [],
                'validation' => null,
                'required' => false,
            ],
            'industrial-activity' => [
                'title' => 'ما نوع النشاط الصناعي؟',
                'type' => 'single_choice',
                'section' => 'المتطلبات المعمارية',
                'contexts' => [
                    [
                        'department' => 'architecture',
                        'category' => 'industrial',
                    ],
                ],
                'options' => [
                    'food-production' => 'صناعات غذائية',
                    'textiles' => 'صناعة نسيج',
                    'assembly' => 'تجميع منتجات',
                    'storage' => 'تخزين وتوزيع',
                    'other' => 'نشاط آخر',
                ],
                'validation' => null,
                'required' => false,
            ],
            'industrial-machines' => [
                'title' => 'ما الآلات وخطوط الإنتاج وأبعادها؟',
                'type' => 'paragraph',
                'section' => 'المتطلبات المعمارية',
                'contexts' => [
                    [
                        'department' => 'architecture',
                        'category' => 'industrial',
                    ],
                ],
                'options' => [],
                'validation' => null,
                'required' => false,
            ],
            'loading-access' => [
                'title' => 'هل تحتاج مدخل تحميل للشاحنات؟',
                'type' => 'boolean',
                'section' => 'المتطلبات المعمارية',
                'contexts' => [
                    [
                        'department' => 'architecture',
                        'category' => 'industrial',
                    ],
                ],
                'options' => [],
                'validation' => null,
                'required' => false,
            ],
            'brand-name' => [
                'title' => 'ما اسم العلامة التجارية؟',
                'type' => 'short_text',
                'section' => 'الهوية البصرية والمطبوعات',
                'contexts' => [
                    [
                        'department' => 'graphic-design',
                    ],
                ],
                'options' => [],
                'validation' => null,
                'required' => true,
            ],
            'target-audience' => [
                'title' => 'من الجمهور المستهدف؟',
                'type' => 'paragraph',
                'section' => 'الهوية البصرية والمطبوعات',
                'contexts' => [
                    [
                        'department' => 'graphic-design',
                    ],
                    [
                        'department' => 'web-design',
                    ],
                ],
                'options' => [],
                'validation' => null,
                'required' => false,
            ],
            'brand-personality' => [
                'title' => 'ما شخصية العلامة التجارية؟',
                'type' => 'multiple_choice',
                'section' => 'الهوية البصرية والمطبوعات',
                'contexts' => [
                    [
                        'department' => 'graphic-design',
                    ],
                ],
                'options' => [
                    'professional' => 'احترافية',
                    'luxury' => 'فاخرة',
                    'friendly' => 'ودودة',
                    'innovative' => 'مبتكرة',
                    'youthful' => 'شبابية',
                ],
                'validation' => null,
                'required' => false,
            ],
            'logo-type' => [
                'title' => 'ما نوع الشعار المفضل؟',
                'type' => 'single_choice',
                'section' => 'الهوية البصرية والمطبوعات',
                'contexts' => [
                    [
                        'department' => 'graphic-design',
                        'category' => 'branding',
                    ],
                ],
                'options' => [
                    'wordmark' => 'شعار كتابي',
                    'symbol' => 'رمز بصري',
                    'combination' => 'نص ورمز',
                    'no-preference' => 'أحتاج اقتراح المصمم',
                ],
                'validation' => null,
                'required' => false,
            ],
            'print-dimensions' => [
                'title' => 'ما أبعاد المطبوع أو العبوة وخامة الطباعة؟',
                'type' => 'paragraph',
                'section' => 'الهوية البصرية والمطبوعات',
                'contexts' => [
                    [
                        'department' => 'graphic-design',
                        'category' => 'print-design',
                    ],
                ],
                'options' => [],
                'validation' => null,
                'required' => false,
            ],
            'social-channels' => [
                'title' => 'ما منصات النشر المطلوبة؟',
                'type' => 'multiple_choice',
                'section' => 'الهوية البصرية والمطبوعات',
                'contexts' => [
                    [
                        'department' => 'graphic-design',
                        'category' => 'digital-marketing',
                    ],
                ],
                'options' => [
                    'instagram' => 'إنستغرام',
                    'facebook' => 'فيسبوك',
                    'linkedin' => 'لينكدإن',
                    'tiktok' => 'تيك توك',
                ],
                'validation' => null,
                'required' => false,
            ],
            'website-languages' => [
                'title' => 'ما لغات الموقع المطلوبة؟',
                'type' => 'multiple_choice',
                'section' => 'الموقع الإلكتروني',
                'contexts' => [
                    [
                        'department' => 'web-design',
                    ],
                ],
                'options' => [
                    'ar' => 'العربية',
                    'en' => 'الإنجليزية',
                    'fr' => 'الفرنسية',
                ],
                'validation' => null,
                'required' => false,
            ],
            'website-pages' => [
                'title' => 'ما صفحات الموقع الأساسية؟',
                'type' => 'multiple_choice',
                'section' => 'الموقع الإلكتروني',
                'contexts' => [
                    [
                        'department' => 'web-design',
                    ],
                ],
                'options' => [
                    'home' => 'الرئيسية',
                    'about' => 'من نحن',
                    'services' => 'الخدمات',
                    'portfolio' => 'الأعمال',
                    'contact' => 'تواصل معنا',
                    'blog' => 'المدونة',
                ],
                'validation' => null,
                'required' => false,
            ],
            'has-domain' => [
                'title' => 'هل لديك نطاق واستضافة حالياً؟',
                'type' => 'boolean',
                'section' => 'الموقع الإلكتروني',
                'contexts' => [
                    [
                        'department' => 'web-design',
                    ],
                ],
                'options' => [],
                'validation' => null,
                'required' => false,
            ],
            'domain-name' => [
                'title' => 'ما اسم النطاق الحالي؟',
                'type' => 'short_text',
                'section' => 'الموقع الإلكتروني',
                'contexts' => [
                    [
                        'department' => 'web-design',
                    ],
                ],
                'options' => [],
                'validation' => null,
                'required' => false,
            ],
            'products-count' => [
                'title' => 'كم عدد المنتجات المتوقع إطلاقها؟',
                'type' => 'number',
                'section' => 'الموقع الإلكتروني',
                'contexts' => [
                    [
                        'department' => 'web-design',
                        'category' => 'e-commerce',
                    ],
                ],
                'options' => [],
                'validation' => [
                    'min' => 1,
                ],
                'required' => false,
            ],
            'payment-methods' => [
                'title' => 'ما وسائل الدفع المطلوبة؟',
                'type' => 'multiple_choice',
                'section' => 'الموقع الإلكتروني',
                'contexts' => [
                    [
                        'department' => 'web-design',
                        'category' => 'e-commerce',
                    ],
                ],
                'options' => [
                    'cash' => 'الدفع عند الاستلام',
                    'bank' => 'تحويل بنكي',
                    'card' => 'بطاقات مصرفية',
                ],
                'validation' => null,
                'required' => false,
            ],
            'booking-features' => [
                'title' => 'ما متطلبات الحجز؟',
                'type' => 'multiple_choice',
                'section' => 'الموقع الإلكتروني',
                'contexts' => [
                    [
                        'department' => 'web-design',
                        'category' => 'service-platforms',
                        'type' => 'booking-platform',
                    ],
                ],
                'options' => [
                    'calendar' => 'تقويم مواعيد',
                    'reminders' => 'تذكيرات',
                    'payment' => 'دفع مسبق',
                    'rescheduling' => 'تعديل المواعيد',
                ],
                'validation' => null,
                'required' => false,
            ],
            'visual-priority' => [
                'title' => 'ما أهمية الهوية البصرية بالنسبة لك؟',
                'type' => 'linear_scale',
                'section' => 'التفضيلات والتقييم',
                'contexts' => [
                    [],
                ],
                'options' => [],
                'validation' => [
                    'min' => 1,
                    'max' => 10,
                ],
                'required' => false,
            ],
            'preferred-colors' => [
                'title' => 'ما الألوان المفضلة والألوان التي تريد تجنبها؟',
                'type' => 'paragraph',
                'section' => 'التفضيلات والتقييم',
                'contexts' => [
                    [],
                ],
                'options' => [],
                'validation' => null,
                'required' => false,
            ],
            'style-rating' => [
                'title' => 'كيف تقيم النمط المقترح؟',
                'type' => 'rating',
                'section' => 'التفضيلات والتقييم',
                'contexts' => [
                    [],
                ],
                'options' => [],
                'validation' => [
                    'min' => 1,
                    'max' => 5,
                ],
                'required' => false,
            ],
            'priorities-grid' => [
                'title' => 'حدد أهمية عناصر المشروع',
                'type' => 'multiple_choice_grid',
                'section' => 'التفضيلات والتقييم',
                'contexts' => [
                    [],
                ],
                'options' => [],
                'validation' => [
                    'rows' => [
                        'الجودة',
                        'سرعة التسليم',
                        'التكلفة',
                    ],
                    'columns' => [
                        'منخفضة',
                        'متوسطة',
                        'عالية',
                    ],
                ],
                'required' => false,
            ],
            'deliverables-grid' => [
                'title' => 'حدد صيغ التسليم المطلوبة لكل مخرج',
                'type' => 'checkbox_grid',
                'section' => 'التفضيلات والتقييم',
                'contexts' => [
                    [
                        'department' => 'graphic-design',
                    ],
                ],
                'options' => [],
                'validation' => [
                    'rows' => [
                        'الشعار',
                        'المطبوعات',
                    ],
                    'columns' => [
                        'PDF',
                        'PNG',
                        'ملف قابل للتعديل',
                    ],
                ],
                'required' => false,
            ],
            'islamic-details' => [
                'title' => 'ما الزخارف أو العناصر الإسلامية المفضلة؟',
                'type' => 'paragraph',
                'section' => 'التفضيلات والتقييم',
                'contexts' => [
                    [
                        'department' => 'architecture',
                        'style' => 'islamic',
                    ],
                ],
                'options' => [],
                'validation' => null,
                'required' => false,
            ],
            'references' => [
                'title' => 'ارفع ملفات مرجعية أو مخططات تساعدنا على فهم المشروع',
                'type' => 'file',
                'section' => 'المرفقات والملاحظات',
                'contexts' => [
                    [],
                ],
                'options' => [],
                'validation' => null,
                'required' => false,
            ],
            'notes' => [
                'title' => 'هل لديك ملاحظات إضافية؟',
                'type' => 'paragraph',
                'section' => 'المرفقات والملاحظات',
                'contexts' => [
                    [],
                ],
                'options' => [],
                'validation' => null,
                'required' => false,
            ],
        ];

        $questions['land-area']['contexts'] = [
            ['department' => 'architecture', 'category' => 'residential', 'type' => 'villa'],
            ['department' => 'architecture', 'category' => 'residential', 'type' => 'residential-complex'],
            ['department' => 'architecture', 'category' => 'commercial', 'type' => 'office-building'],
            ['department' => 'architecture', 'category' => 'industrial'],
        ];
        $questions['floors']['contexts'] = $questions['land-area']['contexts'];
        $questions['deliverables-grid']['contexts'] = [['department' => 'graphic-design', 'category' => 'branding']];
        $questions['website-pages']['options'] += ['products' => 'المنتجات', 'cart' => 'سلة المشتريات', 'account' => 'حساب العميل', 'courses' => 'الدورات', 'booking' => 'حجز موعد'];
        $questions['bedrooms']['description'] = 'للمجمعات السكنية، حدد عدد غرف النوم في الوحدة النموذجية وليس مجموع غرف المجمع.';
        $questions['budget']['description'] = 'أدخل ميزانية خدمات التصميم والتطوير بالدولار الأمريكي؛ تكلفة البناء أو التصنيع غير مشمولة.';

        foreach (self::typeQuestions() as $key => [$title, $example]) {
            foreach (DepartmentSeeder::catalog() as $departmentKey => $department) {
                foreach ($department['categories'] as $categoryKey => $category) {
                    if (! array_key_exists($key, $category['types'])) {
                        continue;
                    }
                    $section = match ($departmentKey) {
                        'architecture' => 'المتطلبات المعمارية',
                        'graphic-design' => 'الهوية البصرية والمطبوعات',
                        'web-design' => 'الموقع الإلكتروني',
                    };
                    $questions['type-'.$key] = self::definition($title, $section, [['department' => $departmentKey, 'category' => $categoryKey, 'type' => $key]], $example);
                }
            }
        }
        foreach (self::styleQuestions() as $key => [$title, $example, $departments]) {
            $contexts = array_map(fn (string $department): array => ['department' => $department, 'style' => $key], $departments);
            $questions['style-'.$key] = self::definition($title, 'التفضيلات والتقييم', $contexts, $example);
        }

        $questions += self::practicalQuestions();
        foreach ($questions as $key => &$question) {
            $question['description'] ??= isset($question['example'])
                ? 'مثال للإجابة: '.$question['example']
                : self::questionHelp($key, $question['type']);
        }
        unset($question);

        return $questions;
    }

    /** @return array<string, array<string, mixed>> */
    private static function practicalQuestions(): array
    {
        $questions = [];
        $briefs = [
            'architecture' => [
                'المتطلبات المعمارية',
                'existing-plans' => ['ما المخططات والقياسات المتوفرة حالياً؟', 'مخطط الأرض بصيغة DWG ورفع مساحي حديث؛ لا توجد مخططات إنشائية.'],
                'site-constraints' => ['ما قيود الموقع التي يجب مراعاتها في التصميم؟', 'مدخل من جهة الشمال، مبنى ملاصق من الشرق، والحاجة إلى خصوصية من الشارع.'],
                'architecture-delivery' => ['ما المخرجات المعمارية التي تحتاجها من فريقنا؟', 'مخططات توزيع وواجهات ومقاطع وثلاث لقطات خارجية؛ المخططات الإنشائية خارج النطاق.'],
            ],
            'graphic-design' => [
                'الهوية البصرية والمطبوعات',
                'brand-assets' => ['ما ملفات العلامة والمحتوى الجاهز الذي ستزودنا به؟', 'الشعار بصيغة SVG، النصوص النهائية بالعربية وصور المنتجات؛ نحتاج تنسيقها فقط.'],
                'design-approval' => ['من يعتمد التصاميم وما مراحل المراجعة المطلوبة؟', 'مدير التسويق يعتمد الاتجاه الأول، ثم نراجع النصوص والألوان قبل تجهيز ملفات الطباعة.'],
                'printing-supplier' => ['هل توجد مطبعة أو جهة إنتاج لها مواصفات يجب الالتزام بها؟', 'المطبعة تزودنا بقالب القص؛ طباعة CMYK بدقة 300dpi وهامش قص 3 مم.'],
            ],
            'web-design' => [
                'الموقع الإلكتروني',
                'website-content' => ['من سيجهز نصوص الموقع والصور ومتى ستكون جاهزة؟', 'فريقنا يزودكم بالنصوص والصور خلال أسبوع، ونحتاج إدخال المحتوى لـ12 صفحة.'],
                'website-integrations' => ['ما الأنظمة الخارجية التي يجب ربطها بالموقع؟', 'ربط نموذج الطلب مع البريد وCRM، وإضافة بوابة دفع محلية دون مزامنة مخزون.'],
                'website-maintenance' => ['من سيدير الموقع بعد الإطلاق وما التدريب المطلوب؟', 'موظف واحد يضيف المنتجات؛ نحتاج جلسة تدريب ونسخاً احتياطية وتحديثات شهرية.'],
            ],
        ];
        foreach ($briefs as $department => $items) {
            $section = array_shift($items);
            foreach ($items as $key => [$title, $example]) {
                $questions[$key] = self::definition($title, $section, [['department' => $department]], $example);
            }
        }
        $visuals = [
            'architecture-visual' => ['architecture', 'المتطلبات المعمارية', 'أي طابع للفراغات الداخلية أقرب إلى تفضيلك؟',
                ['modern' => 'حديث بخطوط بسيطة', 'classic' => 'كلاسيكي بتفاصيل وزخارف', 'minimalist' => 'بسيط بألوان فاتحة', 'bohemian' => 'دافئ بخامات طبيعية']],
            'graphic-visual' => ['graphic-design', 'الهوية البصرية والمطبوعات', 'أي لوحة إلهام لونية أقرب إلى علامتك؟',
                ['graphic' => 'درجات متوازنة ومتنوعة', 'colorful' => 'ألوان حيوية متعددة', 'scandinavian' => 'درجات طبيعية دافئة']],
            'web-visual' => ['web-design', 'الموقع الإلكتروني', 'أي نوع من الصور تريد إبرازَه في الموقع؟',
                ['services' => 'صور العمل والفريق والخدمات', 'ecommerce' => 'صور المنتجات والتسوق', 'architecture' => 'صور المشاريع والأعمال المنفذة']],
        ];
        foreach ($visuals as $key => [$department, $section, $title, $options]) {
            $questions[$key] = [
                'title' => $title, 'type' => 'single_choice', 'section' => $section,
                'contexts' => [['department' => $department]], 'options' => $options,
                'option_images' => array_combine(array_keys($options), array_keys($options)),
                'description' => 'اختر الصورة الأقرب إلى تفضيلك. الصور مرجع لاتجاه التصميم وليست نتيجة نهائية.',
                'validation' => null, 'required' => false,
            ];
        }

        return $questions;
    }

    private static function questionHelp(string $key, string $type): string
    {
        return match ($key) {
            'location' => 'للعمارة اذكر المدينة والحي؛ للمواقع والتصميم اذكر الأسواق أو الدول المستهدفة.',
            'goal' => 'اشرح النتيجة التي تتوقعها، مثل تحسين توزيع المساحات أو إطلاق متجر أو تطوير هوية العلامة.',
            'start-date' => 'حدد موعد البدء المناسب مع مراعاة جاهزية الملفات والموافقات.',
            'meeting-time' => 'اذكر الوقت المحلي المناسب لك لعقد اجتماع التنسيق.',
            'meeting-duration' => 'أدخل المدة بصيغة ساعات:دقائق:ثوانٍ، مثلاً 00:30:00 لاجتماع نصف ساعة.',
            'land-area' => 'أدخل مساحة قطعة الأرض وفق سند الملكية أو الرفع المساحي، وليس مساحة البناء.',
            'floors' => 'حدد العدد المطلوب مبدئياً؛ يعتمد العدد النهائي على القيود التنظيمية ودراسة الموقع.',
            'pool-area' => 'أدخل مساحة سطح المسبح بالمتر المربع؛ مثال 8 × 5 أمتار يعني 40 م².',
            'customer-capacity' => 'اذكر العدد في ساعة الذروة، وليس مجموع الزوار خلال اليوم.',
            'brand-name' => 'اكتب الاسم المعتمد بالعربية والإنجليزية إن وجدا، مع الشعار النصي إن كان جاهزاً.',
            'target-audience' => 'صف العملاء من حيث الاهتمامات والموقع والاحتياج الذي يحله منتجك أو خدمتك.',
            'print-dimensions' => 'اذكر المقاس النهائي بوحدة واضحة، ونوع الورق أو خامة العبوة وطريقة الطباعة.',
            'domain-name' => 'اكتب اسم النطاق فقط مثل example.com؛ لا تشارك كلمات المرور.',
            'products-count' => 'عدد المنتجات عند الإطلاق، مع توضيح إن كانت لها ألوان أو مقاسات متعددة.',
            'preferred-colors' => 'اذكر الألوان المرغوبة والممنوعة وأكوادها إن كانت لديك هوية معتمدة.',
            'style-rating' => 'قيّم مدى ملاءمة النمط المختار من 1 (غير مناسب) إلى 5 (مناسب جداً).',
            'visual-priority' => 'حدد أهمية المظهر البصري من 1 إلى 5 مقارنة بأهداف المشروع الأخرى.',
            'references' => 'ارفع مخططات أو صوراً مرجعية أو ملفات محتوى بصيغة JPG أو PNG أو WEBP أو PDF، حتى 10 ميغابايت للملف.',
            'notes' => 'اذكر أي قيود أو احتياجات لم تغطها الأسئلة السابقة.',
            default => match ($type) {
                'multiple_choice' => 'اختر جميع البنود المطلوبة ضمن نطاق العمل؛ يمكنك اختيار أكثر من بند.',
                'single_choice', 'dropdown' => 'اختر البند الأقرب إلى احتياجك الحالي.',
                'boolean' => 'اختر نعم إذا كان هذا المتطلب ينطبق على مشروعك، وإلا اختر لا.',
                'number' => 'أدخل العدد أو القياس المطلوب حسب الوحدة المذكورة في السؤال.',
                'multiple_choice_grid', 'checkbox_grid' => 'حدد الاختيار المناسب لكل صف لتوضيح الأولويات والمخرجات.',
                default => 'اذكر التفاصيل العملية والأعداد والقيود المتوفرة لديك؛ يمكنك ترك السؤال الاختياري فارغاً.',
            },
        };
    }

    /** @return array<string, array{string, string}> */
    public static function typeQuestions(): array
    {
        return [
            'villa' => ['كيف تريد توزيع مناطق العائلة والضيوف والحديقة؟', 'مدخل مستقل للضيوف، معيشة عائلية مطلة على الحديقة، وغرف النوم في الطابق الأول.'],
            'apartment' => ['ما التعديلات المطلوبة على توزيع الشقة والجدران الحالية؟', 'فتح المطبخ على المعيشة مع الحفاظ على جدران القص ومواقع الحمامات الحالية.'],
            'residential-complex' => ['كم عدد الوحدات وما الخدمات المشتركة المطلوبة؟', '24 شقة بمساحتين 100 و140 م²، مع موقف لكل وحدة وحديقة مشتركة وغرفة حارس.'],
            'restaurant' => ['ما نوع الخدمة ونسبة الجلسات الداخلية والخارجية؟', 'خدمة على الطاولة؛ 40 مقعداً داخلياً و20 مقعداً في التراس مع فصل مسار التوريد.'],
            'retail-store' => ['كيف تريد توزيع العرض والمخزون ونقطة البيع؟', 'عرض المنتجات على الجدران، جزيرة وسطية، مستودع خلفي ونقطة دفع قرب المخرج.'],
            'office-building' => ['كم عدد الموظفين وما متطلبات الاجتماعات والاستقبال؟', '60 موظفاً، استقبال أرضي، ثلاث قاعات اجتماعات ومكتب إدارة منفصل.'],
            'factory' => ['ما الطاقة الإنتاجية ومسار انتقال المواد داخل المصنع؟', 'تعبئة 2000 عبوة يومياً؛ استلام المواد ثم التعقيم والتعبئة والتغليف وصولاً للتخزين.'],
            'warehouse' => ['ما أنواع التخزين وارتفاع الرفوف ومساحات المناورة؟', 'تخزين مواد جافة على رفوف بارتفاع 5 أمتار مع ممرات 3.5 متر للرافعات.'],
            'workshop' => ['ما مناطق العمل ومتطلبات العزل والتهوية؟', 'فصل القص عن التجميع والدهان، مع شفط غبار موضعي وعزل صوتي لمنطقة القص.'],
            'logo' => ['ما الاسم المكتوب بالشعار وأين سيستخدم بأصغر حجم؟', 'اسم نواة بالعربية؛ يستخدم على بطاقات العمل وصورة الحساب وختم بعرض 25 مم.'],
            'brand-identity' => ['ما تطبيقات الهوية التي تحتاجها عند الإطلاق؟', 'شعار عربي وإنجليزي، بطاقة عمل، ورق مراسلات، توقيع بريد وقوالب منشورات.'],
            'brand-guidelines' => ['ما أصول الهوية الحالية وما قواعد الاستخدام المطلوب توثيقها؟', 'لدينا شعار متجهي؛ نحتاج توثيق الألوان والخطوط والمساحة الآمنة والاستخدامات الممنوعة.'],
            'packaging' => ['ما بيانات المنتج الإلزامية ومقاسات قالب القص؟', 'عبوة قهوة 250 غراماً؛ الاسم والمكونات والوزن والباركود ومكان لتاريخ الإنتاج.'],
            'brochure' => ['ما عدد الصفحات والطيات ومحتوى البروشور؟', 'بروشور A4 مطوي ثلاثياً، يتضمن نبذة الشركة وأربع خدمات ووسائل التواصل.'],
            'catalog' => ['كم عدد المنتجات وكيف ستنظم أقسام الكتالوج؟', '36 منتجاً في ثلاثة أقسام؛ صورة ومقاس ورمز لكل منتج، ضمن 20 صفحة A4.'],
            'social-media' => ['كم عدد التصاميم الشهرية وما مقاسات النشر؟', '12 منشوراً مربعاً و8 قصص شهرياً؛ يزوّد العميل النصوص والصور قبل بداية كل أسبوع.'],
            'ad-campaign' => ['ما رسالة الحملة ومدتها ومخرجات التصميم المطلوبة؟', 'حملة افتتاح لمدة أسبوعين؛ ثلاثة مفاهيم بصرية وستة مقاسات للإعلانات الرقمية.'],
            'presentation' => ['من جمهور العرض وما عدد الشرائح والهدف منه؟', 'عرض لمستثمرين من 15 شريحة يشرح المشكلة والمنتج ونموذج الإيرادات وخطة النمو.'],
            'company-website' => ['ما الخدمات التي يجب عرضها وكيف يتواصل الزوار مع الشركة؟', 'عرض ثلاث خدمات وصفحات مشاريع سابقة مع نموذج طلب عرض سعر وبيانات الاتصال.'],
            'portfolio' => ['كم عملاً سيعرض وكيف ستصنف المشاريع؟', '18 مشروعاً مصنفاً إلى تصوير ومنتجات وهوية بصرية، مع صفحة تفاصيل لكل عمل.'],
            'landing-page' => ['ما الإجراء الرئيسي المطلوب من زائر صفحة الهبوط؟', 'تعبئة نموذج طلب استشارة؛ يشمل الصفحة تعريف الخدمة ومزاياها وأسئلة شائعة.'],
            'online-store' => ['ما مناطق الشحن وسياسة المخزون والاسترجاع؟', 'شحن إلى دمشق وحلب وحمص؛ مخزون لكل منتج ورسوم حسب المدينة واسترجاع خلال 7 أيام.'],
            'digital-products' => ['كيف يستلم المشتري الملف وما حدود التنزيل؟', 'رابط تنزيل بعد تأكيد الدفع، صالح 7 أيام وبحد أقصى ثلاث عمليات تنزيل.'],
            'marketplace' => ['كيف يسجل البائع وما آلية العمولة واعتماد المنتجات؟', 'طلب انضمام يراجعه المدير؛ عمولة 8% واعتماد كل منتج قبل النشر وتسوية شهرية.'],
            'booking-platform' => ['ما مدة الموعد وساعات العمل وسياسة الإلغاء؟', 'موعد 30 دقيقة من التاسعة إلى الخامسة؛ استراحة ساعة وإلغاء قبل الموعد بـ24 ساعة.'],
            'learning-platform' => ['كيف تنظم الدورات وتقيم تقدم المتعلم؟', 'خمس دورات؛ دروس فيديو وملفات PDF واختبار نهاية كل وحدة وشهادة عند اجتياز الدورة.'],
            'customer-portal' => ['ما المعلومات التي يراها العميل وما صلاحيات حسابه؟', 'يرى مشاريعه وفواتيره ومرفقاته فقط، ويرسل طلب دعم دون تعديل بيانات العملاء الآخرين.'],
        ];
    }

    /** @return array<string, array{string, string, list<string>}> */
    public static function styleQuestions(): array
    {
        return [
            'modern' => ['ما التفاصيل الحديثة التي تريد إبرازها في التصميم؟', 'خطوط مستقيمة وتفاصيل بسيطة مع تباين محدود وتوزيع عملي للعناصر.', ['architecture', 'graphic-design', 'web-design']],
            'classic' => ['ما درجة التفاصيل والزخارف الكلاسيكية المفضلة؟', 'تفاصيل معتدلة وتكوين متناظر دون ازدحام زخرفي.', ['architecture', 'graphic-design']],
            'islamic' => ['كيف تريد توظيف الأقواس والأنماط الهندسية الإسلامية؟', 'نمط هندسي في المدخل وأقواس بسيطة مع تجنب الزخارف على جميع الأسطح.', ['architecture']],
            'neoclassical' => ['ما العناصر الكلاسيكية التي تريد دمجها مع التوزيع المعاصر؟', 'إطارات جدارية بسيطة وألوان فاتحة وأثاث بتفاصيل محدودة.', ['architecture']],
            'minimalist' => ['ما العناصر الأساسية التي يجب إبقاؤها في التصميم المبسط؟', 'إبراز العنصر الرئيسي والاكتفاء بلونين ومساحات فارغة واضحة.', ['architecture', 'graphic-design', 'web-design']],
            'industrial' => ['ما الخامات الظاهرة التي تفضلها في النمط الصناعي؟', 'معدن أسود وخشب طبيعي مع إضاءة دافئة وتمديدات ظاهرة منظمة.', ['architecture']],
            'scandinavian' => ['ما تفضيلاتك للخشب والإضاءة ضمن النمط الاسكندنافي؟', 'خشب بلوط فاتح وألوان محايدة واستثمار الضوء الطبيعي.', ['architecture']],
            'contemporary' => ['ما التوازن المطلوب بين الخامات والأشكال المعاصرة؟', 'تكوين بسيط مع عنصر بارز واحد وخامات سهلة الصيانة.', ['architecture', 'graphic-design', 'web-design']],
            'bohemian' => ['ما الألوان والخامات الطبيعية المناسبة للطابع البوهيمي؟', 'ألوان ترابية وقماش طبيعي ونباتات مع قطع محدودة مصنوعة يدوياً.', ['architecture']],
            'corporate' => ['ما عناصر التصميم التي تعبر عن الثقة والطابع المؤسسي؟', 'شبكة منظمة وخط واضح وألوان الهوية مع صور حقيقية للفريق والخدمات.', ['graphic-design', 'web-design']],
            'playful' => ['ما حدود الألوان والرسوم في الطابع الحيوي؟', 'ألوان مبهجة ورسوم بسيطة مع الحفاظ على وضوح النصوص وقابلية القراءة.', ['graphic-design', 'web-design']],
        ];
    }

    /**
     * @param  list<array<string, string>>  $contexts
     * @return array{title: string, type: string, section: string, contexts: list<array<string, string>>, options: array, validation: null, required: bool, example: string}
     */
    private static function definition(string $title, string $section, array $contexts, string $example): array
    {
        return ['title' => $title, 'type' => 'paragraph', 'section' => $section, 'contexts' => $contexts, 'options' => [], 'validation' => null, 'required' => false, 'example' => $example];
    }

    public static function question(string $key): Question
    {
        $data = self::definitions()[$key];

        return Question::where('question_section_id', QuestionSection::where('name', $data['section'])->sole()->id)->where('title', $data['title'])->sole();
    }

    public function run(): void
    {
        foreach (self::definitions() as $order => $data) {
            $question = Question::firstOrCreate(['question_section_id' => QuestionSection::where('name', $data['section'])->sole()->id, 'title' => $data['title']], ['answer_type_id' => AnswerType::where('code', $data['type'])->sole()->id, 'description' => $data['description'] ?? 'حدد المتطلبات والأعداد والمخرجات المتوقعة لتقدير نطاق العمل بدقة.', 'is_required' => $data['required'], 'is_active' => true, 'sort_order' => array_search($order, array_keys(self::definitions()), true), 'validation_rules' => $data['validation']]);
            if (in_array($question->description, ['أدخل إجابة تساعد الفريق على تحديد نطاق العمل بدقة.', 'حدد المتطلبات والأعداد والمخرجات المتوقعة لتقدير نطاق العمل بدقة.'], true) && isset($data['description'])) {
                $question->update(['description' => $data['description']]);
            }
        }
    }
}
