<?php

namespace Database\Factories;

use App\Models\AnswerType;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<QuestionOption> */
class QuestionOptionFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'question_id' => fn (): int => Question::factory()->create(['answer_type_id' => AnswerType::where('code', 'single_choice')->first()?->id ?? AnswerType::factory()->singleChoice()->create()->id])->id,
            'label' => fake()->randomElement(['حديقة', 'مكتب منزلي', 'غرفة ضيوف', 'صالة رياضية', 'مرآب']),
            'value' => fake()->word(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
