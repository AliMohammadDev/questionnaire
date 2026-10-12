<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuestionCondition;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<QuestionCondition> */
class QuestionConditionFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'depends_on_question_id' => Question::factory(),
            'operator' => 'equals',
            'value' => 'نعم',
            'logic_type' => 'AND',
        ];
    }
}
