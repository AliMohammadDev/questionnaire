<?php

namespace Database\Factories;

use App\Models\DesignStyle;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DesignStyle> */
class DesignStyleFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['حديث', 'كلاسيكي', 'إسلامي', 'بسيط', 'معاصر']),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
