<?php

namespace Database\Seeders;

use App\Models\QuestionSection;
use Illuminate\Database\Seeder;

class QuestionSectionSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'المعلومات الأساسية',
            'الميزانية والجدول الزمني',
            'المتطلبات المعمارية',
            'الهوية البصرية والمطبوعات',
            'الموقع الإلكتروني',
            'التفضيلات والتقييم',
            'المرفقات والملاحظات',
        ] as $order => $name) {
            QuestionSection::firstOrCreate(['name' => $name], ['description' => 'أدخل المعلومات المتعلقة بـ'.$name.'.', 'is_active' => true, 'sort_order' => $order]);
        }
    }
}
