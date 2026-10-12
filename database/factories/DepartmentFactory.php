<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Department> */
class DepartmentFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['الهندسة المعمارية', 'التصميم الداخلي', 'تصميم المواقع الإلكترونية']),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
