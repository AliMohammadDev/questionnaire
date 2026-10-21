<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectService
{
    /**
     * @param  array{client_id: int, department_id: int, project_category_id: int, project_type_id: int, design_style_ids?: array<int>, project_name?: string|null, notes?: string|null, additional_questions?: array<int, array{question: string, answer?: string|null, notes?: string|null}>}  $data
     */
    public function storeProject(array $data, User $user): Project
    {
        return DB::transaction(function () use ($data, $user): Project {
            $styleIds = $data['design_style_ids'] ?? [];
            unset($data['design_style_ids']);
            $project = $user->projects()->create([
                ...$data,
                'project_code' => 'PRJ-'.Str::upper((string) Str::ulid()),
            ]);
            $project->designStyles()->sync($styleIds);

            return $project;
        });
    }
}
