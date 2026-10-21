<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Project */
class ProjectResource extends JsonResource
{
    /** @return array{id: int, user_id: int|null, project_code: string, client_id: int, department_id: int, project_category_id: int, project_type_id: int, design_style_ids: array<int>, project_name: string|null, notes: string|null, additional_questions: array<int, array{question: string, answer?: string|null, notes?: string|null}>} */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'project_code' => $this->project_code,
            'client_id' => $this->client_id,
            'department_id' => $this->department_id,
            'project_category_id' => $this->project_category_id,
            'project_type_id' => $this->project_type_id,
            'design_style_ids' => $this->selectedDesignStyleIds(),
            'project_name' => $this->project_name,
            'notes' => $this->notes,
            'additional_questions' => $this->additional_questions ?? [],
        ];
    }
}
