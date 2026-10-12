<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use Illuminate\Database\Seeder;

class ProjectTypeSeeder extends Seeder
{
    public static function type(string $departmentKey, string $categoryKey, string $typeKey): ProjectType
    {
        $recordId = ProjectSeeder::catalogRecordId('project_type_id', ['department' => $departmentKey, 'category' => $categoryKey, 'type' => $typeKey]);

        return $recordId
            ? ProjectType::findOrFail($recordId)
            : ProjectCategorySeeder::category($departmentKey, $categoryKey)->projectTypes()
                ->where('name', DepartmentSeeder::catalog()[$departmentKey]['categories'][$categoryKey]['types'][$typeKey])->sole();
    }

    public function run(): void
    {
        foreach (DepartmentSeeder::catalog() as $key => $data) {
            foreach ($data['categories'] as $categoryKey => $category) {
                $record = ProjectCategorySeeder::category($key, $categoryKey);
                foreach ($category['types'] as $typeKey => $name) {
                    $recordId = ProjectSeeder::catalogRecordId('project_type_id', ['department' => $key, 'category' => $categoryKey, 'type' => $typeKey]);
                    $record->projectTypes()->firstOrCreate($recordId ? ['id' => $recordId] : ['name' => $name], ['name' => $name, 'description' => 'دراسة متطلبات '.$name.' وتحديد نطاق العمل والمخرجات.', 'is_active' => true, 'sort_order' => array_search($typeKey, array_keys($category['types']), true)]);
                }
            }
        }
    }
}
