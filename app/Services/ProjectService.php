<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Str;

class ProjectService
{
    /**
     * @param  array{client_id: int, department_id: int, project_category_id: int, project_type_id: int, design_style_id?: int|null, project_name?: string|null, notes?: string|null, additional_questions?: array<int, array{question: string, answer?: string|null, notes?: string|null}>}  $data
     */
    public function storeProject(array $data, User $user): Project
    {
        return $user->projects()->create([
            ...$data,
            'project_code' => 'PRJ-'.Str::upper((string) Str::ulid()),
        ]);
    }
}
