<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Project> */
class ProjectFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'project_type_id' => ProjectType::factory(),
            'project_category_id' => fn (array $attributes): int => ProjectType::findOrFail($attributes['project_type_id'])->project_category_id,
            'department_id' => fn (array $attributes): int => ProjectCategory::findOrFail($attributes['project_category_id'])->department_id,
            'project_code' => fake()->unique()->bothify('PRJ-########'),
            'project_name' => fake()->randomElement(['مشروع فيلا عائلية', 'تصميم متجر إلكتروني', 'هوية بصرية لشركة', 'تصميم مبنى تجاري']),
        ];
    }
}
