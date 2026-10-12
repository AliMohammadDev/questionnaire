<?php

namespace Database\Factories;

use App\Models\AnswerType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AnswerType> */
class AnswerTypeFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => 'نص قصير',
            'code' => 'short_text',
            'has_options' => false,
            'allows_multiple' => false,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function singleChoice(): static
    {
        return $this->state(fn (array $attributes): array => ['name' => 'اختيار واحد',
            'code' => 'single_choice',
            'has_options' => true,
            'allows_multiple' => false]);
    }

    public function multipleChoice(): static
    {
        return $this->state(fn (array $attributes): array => ['name' => 'اختيارات متعددة',
            'code' => 'multiple_choice',
            'has_options' => true,
            'allows_multiple' => true]);
    }
}
