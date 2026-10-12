<?php

namespace Database\Factories;

use App\Models\AnswerType;
use App\Models\Question;
use App\Models\QuestionSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Question> */
class QuestionFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'question_section_id' => QuestionSection::factory(),
            'answer_type_id' => fn (): int => AnswerType::where('code', 'short_text')->first()?->id ?? AnswerType::factory()->create()->id,
            'title' => fake()->randomElement(['ما الهدف الرئيسي من المشروع؟', 'ما الميزانية المتوقعة؟', 'ما المتطلبات الأساسية للتصميم؟', 'ما الموعد المفضل للتسليم؟']),
            'is_required' => false,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
