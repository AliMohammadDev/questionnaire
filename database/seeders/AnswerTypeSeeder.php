<?php

namespace Database\Seeders;

use App\Models\AnswerType;
use Illuminate\Database\Seeder;

class AnswerTypeSeeder extends Seeder
{
    /** @return array<string, string> */
    public static function types(): array
    {
        return [
            'short_text' => 'نص قصير',
            'paragraph' => 'فقرة نصية',
            'single_choice' => 'اختيار واحد',
            'multiple_choice' => 'مربعات اختيار متعددة',
            'dropdown' => 'قائمة منسدلة',
            'file' => 'رفع ملف',
            'linear_scale' => 'مقياس خطي',
            'rating' => 'تقييم بالنجوم',
            'multiple_choice_grid' => 'شبكة اختيار واحد لكل صف',
            'checkbox_grid' => 'شبكة اختيارات متعددة لكل صف',
            'date' => 'تاريخ',
            'time' => 'وقت',
            'duration' => 'مدة زمنية',
            'number' => 'رقم',
            'boolean' => 'نعم أو لا',
        ];
    }

    public function run(): void
    {
        foreach (self::types() as $code => $name) {
            AnswerType::firstOrCreate(['code' => $code], ['name' => $name, 'has_options' => in_array($code, ['single_choice', 'multiple_choice', 'dropdown'], true), 'allows_multiple' => in_array($code, ['multiple_choice', 'checkbox_grid'], true), 'is_active' => true, 'sort_order' => array_search($code, array_keys(self::types()), true)]);
        }
    }
}
