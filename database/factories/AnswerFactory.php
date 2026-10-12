<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Project;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Answer> */
class AnswerFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'question_id' => Question::factory(),
            'value_text' => fake()->randomElement(['أرغب بتصميم عملي ومريح يناسب احتياجات الأسرة.', 'أفضل تصميماً بسيطاً بألوان هادئة.', 'أحتاج إلى تنفيذ المشروع على مراحل.', 'أرغب بهوية واضحة تعكس نشاط الشركة.']),
        ];
    }
}
