<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class QuestionContextSeeder extends Seeder
{
    public function run(): void
    {
        foreach (QuestionSeeder::definitions() as $key => $data) {
            $question = QuestionSeeder::question($key);
            if (in_array($key, ['land-area', 'floors', 'deliverables-grid'], true)) {
                $departmentKey = $key === 'deliverables-grid' ? 'graphic-design' : 'architecture';
                $question->contexts()->where('department_id', DepartmentSeeder::department($departmentKey)->id)
                    ->whereNull('project_category_id')->whereNull('project_type_id')->whereNull('design_style_id')->delete();
            }
            foreach ($data['contexts'] as $context) {
                $department = isset($context['department']) ? DepartmentSeeder::department($context['department']) : null;
                $category = isset($context['category']) ? ProjectCategorySeeder::category($context['department'], $context['category']) : null;
                $type = isset($context['type']) ? ProjectTypeSeeder::type($context['department'], $context['category'], $context['type']) : null;
                $question->contexts()->firstOrCreate(['department_id' => $department?->id, 'project_category_id' => $category?->id, 'project_type_id' => $type?->id, 'design_style_id' => isset($context['style']) ? DesignStyleSeeder::style($context['style'])->id : null]);
            }
        }
    }
}
