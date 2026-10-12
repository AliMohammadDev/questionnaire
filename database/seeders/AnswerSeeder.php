<?php

namespace Database\Seeders;

use App\Filament\Support\ProjectQuestionnaire;
use App\Models\Answer;
use App\Models\Project;
use App\Models\Question;
use Illuminate\Database\Seeder;

class AnswerSeeder extends Seeder
{
    /**
     * @param  array{name: string, department: string, category: string, type: string, style: string, client: string, submitted_at: ?string, completed_at: ?string, budget: int, location: string, goal: string}  $scenario
     * @return array<string, mixed>
     */
    public static function demoAnswers(array $scenario): array
    {
        $type = $scenario['type'];
        $finished = filled($scenario['completed_at']);
        $answers = [
            'location' => $scenario['location'],
            'goal' => $scenario['goal'],
            'budget' => $scenario['budget'],
            'start-date' => $finished ? '2026-09-10' : '2026-11-01',
            'meeting-time' => '10:30:00',
            'meeting-duration' => '00:45:00',
            'priority' => in_array($type, ['ad-campaign', 'landing-page'], true) ? ['urgent'] : ['phased'],
            'visual-priority' => $scenario['department'] === 'graphic-design' ? 9 : 7,
            'style-rating' => $finished ? 5 : 4,
            'preferred-colors' => match ($scenario['style']) {
                'playful' => 'أصفر وأزرق مع خلفيات فاتحة؛ تجنب تداخل الألوان مع النصوص.',
                'industrial' => 'أسود مطفي وخشب طبيعي وألوان ترابية.',
                'corporate' => 'أزرق داكن ورمادي وأبيض بما يتوافق مع الهوية الحالية.',
                default => 'ألوان محايدة مع لون بارز واحد؛ تجنب التباين المزعج.',
            },
            'priorities-grid' => ['الجودة' => 'عالية', 'سرعة التسليم' => 'متوسطة', 'التكلفة' => 'متوسطة'],
            'notes' => 'بيانات وهمية للتدريب. يعتمد العميل نطاق العمل وعدد جولات التعديل قبل بدء التنفيذ.',
            'references' => true,
            'type-'.$type => QuestionSeeder::typeQuestions()[$type][1],
            'style-'.$scenario['style'] => QuestionSeeder::styleQuestions()[$scenario['style']][1],
        ];
        if ($scenario['department'] === 'architecture') {
            $answers += [
                'land-area' => match ($type) {
                    'villa' => 650, 'residential-complex' => 4200, 'office-building' => 900,
                    'factory' => 1800, 'warehouse' => 1500, 'workshop' => 500, default => 120,
                },
                'floors' => match ($type) {
                    'villa' => 2, 'residential-complex' => 4, 'office-building' => 3, default => 1,
                },
                'bedrooms' => $type === 'villa' ? 4 : 3,
                'residential-spaces' => $type === 'apartment' ? ['office', 'guest-room'] : ['garden', 'office', 'garage'],
                'pool' => 0,
                'commercial-activity' => [match ($type) {
                    'restaurant' => 'food', 'retail-store' => 'retail', default => 'offices'
                }],
                'customer-capacity' => match ($type) {
                    'restaurant' => 60, 'retail-store' => 12, default => 20
                },
                'restaurant-kitchen' => 'مطبخ تحضير بمساحة 45 م²، شفط مستقل، مخزن جاف ومسار توريد لا يمر بمنطقة الجلسات.',
                'industrial-activity' => [match ($type) {
                    'factory' => 'food-production', 'warehouse' => 'storage', default => 'other'
                }],
                'industrial-machines' => match ($type) {
                    'factory' => 'خط تعبئة بطول 12 متراً وعرض 3 أمتار مع مسار منفصل للمواد الخام.',
                    'warehouse' => 'رافعتان كهربائيتان؛ لا توجد خطوط إنتاج. ارتفاع الرفوف 5 أمتار وممرات المناورة 3.5 متر.',
                    default => 'منشار طاولة 3 × 2 متر وآلة قص وركن تجميع؛ فصل غرفة الدهان وشفط الغبار.',
                },
                'loading-access' => $type === 'workshop' ? 0 : 1,
                'islamic-details' => 'أقواس بسيطة في المداخل ونمط هندسي في المساحات المشتركة مع إبقاء الواجهات هادئة.',
            ];
        }
        if ($scenario['department'] === 'graphic-design') {
            $answers += [
                'brand-name' => match ($scenario['client']) {
                    'layla' => 'استوديو ليلى', 'alnoor' => 'النور', 'sanabel' => 'سنابل',
                    'wood' => 'خشب وزاوية', 'khayt' => 'خيط', 'tannour' => 'تنور وحطب', default => 'نواة',
                },
                'target-audience' => match ($scenario['client']) {
                    'khayt' => 'نساء من 20 إلى 40 عاماً يبحثن عن أزياء يومية عملية.',
                    'sanabel' => 'مستهلكو القهوة ومحلات البقالة والموزعون المحليون.',
                    'tannour' => 'عائلات وموظفون يبحثون عن وجبات محلية وجلسات مريحة.',
                    'layla' => 'شركات وعلامات صغيرة تحتاج تصوير منتجات وحملات.',
                    default => 'أصحاب مشاريع صغيرة ومتوسطة ومديرو مشتريات الشركات.',
                },
                'brand-personality' => $scenario['style'] === 'playful' ? ['friendly', 'youthful'] : ['professional', 'innovative'],
                'logo-type' => ['combination'],
                'print-dimensions' => match ($type) {
                    'packaging' => 'كيس قهوة 250 غراماً بمقاس 12 × 20 سم؛ طباعة على خامة معتمدة من المورد وقالب قص يقدمه المصنع.',
                    'brochure' => 'A4 مطوي ثلاثياً، ورق مطفي 170 غراماً، مع هوامش قص 3 مم.',
                    default => 'كتالوج A4 من 20 صفحة؛ غلاف 250 غراماً وصفحات 150 غراماً مع هوامش قص 3 مم.',
                },
                'social-channels' => $type === 'presentation' ? ['linkedin'] : ['instagram', 'facebook'],
                'deliverables-grid' => ['الشعار' => ['PNG', 'ملف قابل للتعديل'], 'المطبوعات' => ['PDF']],
            ];
        }
        if ($scenario['department'] === 'web-design') {
            $hasDomain = ! in_array($type, ['landing-page', 'digital-products', 'marketplace'], true);
            $answers += [
                'target-audience' => $type === 'learning-platform' ? 'أصحاب مشاريع وطلاب يريدون تعلم مهارات عملية باللغة العربية.' : 'عملاء الخدمة داخل سوريا مع واجهة عربية واضحة تعمل على الهاتف.',
                'website-languages' => $type === 'company-website' ? ['ar', 'en'] : ['ar'],
                'website-pages' => match ($type) {
                    'portfolio' => ['home', 'portfolio', 'about', 'contact'],
                    'landing-page' => ['home', 'contact'],
                    'online-store', 'digital-products', 'marketplace' => ['home', 'products', 'cart', 'account', 'contact'],
                    'booking-platform' => ['home', 'booking', 'contact'],
                    'learning-platform' => ['home', 'courses', 'account', 'contact'],
                    'customer-portal' => ['home', 'account', 'contact'],
                    default => ['home', 'about', 'services', 'contact'],
                },
                'has-domain' => $hasDomain ? 1 : 0,
                'domain-name' => $scenario['client'].'.example.test',
                'products-count' => match ($type) {
                    'online-store' => 80, 'digital-products' => 24, default => 150
                },
                'payment-methods' => $type === 'online-store' ? ['cash', 'bank'] : ['bank'],
                'booking-features' => ['calendar', 'reminders', 'rescheduling'],
            ];
        }

        return $answers;
    }

    public function run(): void
    {
        $definitions = QuestionSeeder::definitions();
        $questionsByKey = [];
        foreach (array_keys($definitions) as $key) {
            $questionsByKey[$key] = QuestionSeeder::question($key);
        }
        foreach (ProjectSeeder::scenarios() as $code => $scenario) {
            $project = Project::where('project_code', $code)->sole();
            $questions = ProjectQuestionnaire::questions($project->getAttributes())->keyBy('id');
            $valuesByKey = self::demoAnswers($scenario);
            $answers = [];
            foreach ($valuesByKey as $key => $value) {
                $answers[$questionsByKey[$key]->id] = $value;
            }
            $visibleQuestionIds = [];
            foreach ($valuesByKey as $key => $value) {
                $question = $questionsByKey[$key];
                if (! $questions->has($question->id) || ! ProjectQuestionnaire::isVisible($question, $questions, $answers)) {
                    continue;
                }
                $column = match ($definitions[$key]['type']) {
                    'short_text', 'paragraph', 'duration' => 'value_text',
                    'number', 'rating', 'linear_scale' => 'value_number',
                    'boolean' => 'value_boolean', 'date' => 'value_date', 'time' => 'value_time',
                    'multiple_choice_grid', 'checkbox_grid' => 'value_json',
                    default => null,
                };
                $visibleQuestionIds[] = $question->id;
                $project->answers()->updateOrCreate(['question_id' => $question->id], array_replace([
                    'value_text' => null, 'value_number' => null, 'value_boolean' => null,
                    'value_date' => null, 'value_time' => null, 'value_json' => null,
                ], $column ? [$column => $value] : []));
            }
            $project->answers()->whereIn('question_id', array_map(fn (Question $question): int => $question->id, $questionsByKey))
                ->whereNotIn('question_id', $visibleQuestionIds)->get()->each(function (Answer $answer): void {
                    $answer->options()->detach();
                    $answer->delete();
                });
        }
    }
}
