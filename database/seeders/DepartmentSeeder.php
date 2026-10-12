<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /** @return array<string, array<string, mixed>> */
    public static function catalog(): array
    {
        return [
            'architecture' => [
                'name' => 'الهندسة المعمارية',
                'description' => 'تصميم المباني والمخططات وتوزيع الفراغات والواجهات.',
                'categories' => [
                    'residential' => [
                        'name' => 'سكني',
                        'description' => 'مساكن خاصة ومبانٍ ومجمعات سكنية.',
                        'types' => [
                            'villa' => 'فيلا',
                            'apartment' => 'شقة سكنية',
                            'residential-complex' => 'مجمع سكني',
                        ],
                    ],
                    'commercial' => [
                        'name' => 'تجاري',
                        'description' => 'مساحات استقبال العملاء والتجارة والخدمات.',
                        'types' => [
                            'restaurant' => 'مطعم أو مقهى',
                            'retail-store' => 'محل تجاري',
                            'office-building' => 'مبنى مكاتب',
                        ],
                    ],
                    'industrial' => [
                        'name' => 'صناعي',
                        'description' => 'منشآت الإنتاج والتخزين والخدمات الصناعية.',
                        'types' => [
                            'factory' => 'مصنع',
                            'warehouse' => 'مستودع',
                            'workshop' => 'ورشة إنتاج',
                        ],
                    ],
                ],
            ],
            'graphic-design' => [
                'name' => 'الغرافيك ديزاين',
                'description' => 'تصميم الهويات البصرية والمطبوعات والمحتوى التسويقي.',
                'categories' => [
                    'branding' => [
                        'name' => 'هوية بصرية',
                        'description' => 'تأسيس هوية العلامة التجارية وتطبيقاتها.',
                        'types' => [
                            'logo' => 'شعار',
                            'brand-identity' => 'هوية بصرية متكاملة',
                            'brand-guidelines' => 'دليل استخدام الهوية',
                        ],
                    ],
                    'print-design' => [
                        'name' => 'مطبوعات وتغليف',
                        'description' => 'مواد مطبوعة ومنتجات تغليف جاهزة للإنتاج.',
                        'types' => [
                            'packaging' => 'تغليف منتجات',
                            'brochure' => 'بروشور تعريفي',
                            'catalog' => 'كتالوج منتجات',
                        ],
                    ],
                    'digital-marketing' => [
                        'name' => 'تصاميم تسويقية رقمية',
                        'description' => 'تصميم محتوى الحملات والمنصات الاجتماعية.',
                        'types' => [
                            'social-media' => 'تصاميم سوشال ميديا',
                            'ad-campaign' => 'حملة إعلانية',
                            'presentation' => 'عرض تقديمي',
                        ],
                    ],
                ],
            ],
            'web-design' => [
                'name' => 'تصميم مواقع إلكترونية',
                'description' => 'تصميم وتطوير مواقع متجاوبة تخدم أهداف الأعمال.',
                'categories' => [
                    'corporate-websites' => [
                        'name' => 'مواقع تعريفية',
                        'description' => 'تعريف بالشركات والأعمال والخدمات.',
                        'types' => [
                            'company-website' => 'موقع شركة',
                            'portfolio' => 'معرض أعمال',
                            'landing-page' => 'صفحة هبوط',
                        ],
                    ],
                    'e-commerce' => [
                        'name' => 'متاجر إلكترونية',
                        'description' => 'عرض المنتجات واستقبال الطلبات والدفع.',
                        'types' => [
                            'online-store' => 'متجر منتجات',
                            'digital-products' => 'متجر منتجات رقمية',
                            'marketplace' => 'سوق متعدد البائعين',
                        ],
                    ],
                    'service-platforms' => [
                        'name' => 'منصات خدمات',
                        'description' => 'حجوزات وتعليم وإدارة خدمات عبر الإنترنت.',
                        'types' => [
                            'booking-platform' => 'منصة حجوزات',
                            'learning-platform' => 'منصة تعليمية',
                            'customer-portal' => 'بوابة عملاء',
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function department(string $key): Department
    {
        $recordId = ProjectSeeder::catalogRecordId('department_id', ['department' => $key]);

        return $recordId
            ? Department::findOrFail($recordId)
            : Department::where('name', self::catalog()[$key]['name'])->sole();
    }

    public function run(): void
    {
        foreach (self::catalog() as $key => $data) {
            $recordId = ProjectSeeder::catalogRecordId('department_id', ['department' => $key]);
            Department::firstOrCreate($recordId ? ['id' => $recordId] : ['name' => $data['name']], ['name' => $data['name'], 'description' => $data['description'], 'is_active' => true, 'sort_order' => array_search($key, array_keys(self::catalog()), true)]);
        }
    }
}
