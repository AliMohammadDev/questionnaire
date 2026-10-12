<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\ProjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProjectCategory> */
class ProjectCategoryFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => fake()->randomElement(['سكني', 'تجاري', 'صناعي']),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
