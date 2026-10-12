<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuestionContext;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<QuestionContext> */
class QuestionContextFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
        ];
    }
}
