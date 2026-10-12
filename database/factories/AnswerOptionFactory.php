<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\AnswerOption;
use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AnswerOption> */
class AnswerOptionFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'question_option_id' => QuestionOption::factory(),
            'answer_id' => fn (array $attributes): int => Answer::factory()->create(['question_id' => QuestionOption::findOrFail($attributes['question_option_id'])->question_id,
                'value_text' => null])->id,
        ];
    }
}
