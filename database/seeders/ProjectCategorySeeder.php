<?php

namespace Database\Seeders;

use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;

class ProjectCategorySeeder extends Seeder
{
    public static function category(string $departmentKey, string $categoryKey): ProjectCategory
    {
        $recordId = ProjectSeeder::catalogRecordId('project_category_id', ['department' => $departmentKey, 'category' => $categoryKey]);

        return $recordId
            ? ProjectCategory::findOrFail($recordId)
            : DepartmentSeeder::department($departmentKey)->projectCategories()
                ->where('name', DepartmentSeeder::catalog()[$departmentKey]['categories'][$categoryKey]['name'])->sole();
    }

    public function run(): void
    {
        foreach (DepartmentSeeder::catalog() as $key => $data) {
            $department = DepartmentSeeder::department($key);
            foreach ($data['categories'] as $categoryKey => $category) {
                $recordId = ProjectSeeder::catalogRecordId('project_category_id', ['department' => $key, 'category' => $categoryKey]);
                $department->projectCategories()->firstOrCreate($recordId ? ['id' => $recordId] : ['name' => $category['name']], ['name' => $category['name'], 'description' => $category['description'], 'is_active' => true, 'sort_order' => array_search($categoryKey, array_keys($data['categories']), true)]);
            }
        }
    }
}
