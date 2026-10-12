<?php

namespace Database\Factories;

use App\Models\ProjectCategory;
use App\Models\ProjectType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProjectType> */
class ProjectTypeFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'project_category_id' => ProjectCategory::factory(),
            'name' => fake()->randomElement(['فيلا عائلية', 'مبنى مكاتب', 'مصنع إنتاج', 'متجر إلكتروني']),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
