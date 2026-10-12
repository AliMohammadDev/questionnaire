<?php

namespace Database\Seeders;

use App\Models\DesignStyle;
use Illuminate\Database\Seeder;

class DesignStyleSeeder extends Seeder
{
    /** @return array<string, array{string, string}> */
    public static function catalog(): array
    {
        return [
            'modern' => [
                'مودرن',
                'خطوط بسيطة وتكوينات عملية ومساحات مفتوحة.',
            ],
            'classic' => [
                'كلاسيك',
                'تناظر وزخارف وتفاصيل مستوحاة من الطرز التقليدية.',
            ],
            'islamic' => [
                'إسلامي',
                'أقواس وزخارف هندسية وهوية مستوحاة من العمارة الإسلامية.',
            ],
            'neoclassical' => [
                'نيوكلاسيك',
                'تفاصيل كلاسيكية بتكوين أبسط يناسب الحياة المعاصرة.',
            ],
            'minimalist' => [
                'مينيمال',
                'عناصر قليلة وتباين واضح ومساحات هادئة.',
            ],
            'industrial' => [
                'صناعي',
                'خامات ظاهرة ومعادن وألوان محايدة.',
            ],
            'scandinavian' => [
                'اسكندنافي',
                'ألوان فاتحة وخشب طبيعي وإضاءة مريحة.',
            ],
            'contemporary' => [
                'معاصر',
                'تكوينات مرنة تجمع الخامات والاتجاهات المعاصرة.',
            ],
            'bohemian' => [
                'بوهيمي',
                'ألوان دافئة وخامات طبيعية وتفاصيل متنوعة.',
            ],
            'corporate' => [
                'مؤسسي',
                'تصميم رسمي واضح يدعم الثقة وهوية الشركة.',
            ],
            'playful' => [
                'إبداعي ملون',
                'ألوان حيوية وأشكال مرنة تناسب العلامات الشبابية.',
            ],
        ];
    }

    public static function style(string $key): DesignStyle
    {
        $recordId = ProjectSeeder::catalogRecordId('design_style_id', ['style' => $key]);

        return $recordId
            ? DesignStyle::findOrFail($recordId)
            : DesignStyle::where('name', self::catalog()[$key][0])->sole();
    }

    public function run(): void
    {
        $styles = self::catalog();
        foreach ($styles as $key => [$name, $description]) {
            $recordId = ProjectSeeder::catalogRecordId('design_style_id', ['style' => $key]);
            $style = DesignStyle::firstOrCreate($recordId ? ['id' => $recordId] : ['name' => $name], ['name' => $name, 'description' => $description, 'is_active' => true, 'sort_order' => array_search($key, array_keys($styles), true)]);
            if (in_array($style->name, ['مودرن — Modern', 'كلاسيك — Classic', 'إسلامي — Islamic', 'نيوكلاسيك — Neoclassical', 'مينيمال — Minimalist', 'صناعي — Industrial', 'اسكندنافي — Scandinavian', 'معاصر — Contemporary', 'بوهيمي — Bohemian', 'مؤسسي — Corporate', 'إبداعي ملون — Playful'], true)) {
                $style->update(['name' => $name]);
            }
        }
    }
}
