<?php

namespace Database\Factories;

use App\Models\QuestionSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<QuestionSection> */
class QuestionSectionFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['المعلومات الأساسية', 'متطلبات المشروع', 'الميزانية والجدول الزمني']),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
