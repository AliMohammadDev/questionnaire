<?php

namespace App\Http\Resources;

use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ProjectCategory */
class ProjectCategoryResource extends JsonResource
{
    /**
     * @return array{id: int, department_id: int, name: string, description: string|null, image: string|null, is_active: bool, sort_order: int}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'department_id' => $this->department_id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->getFirstMedia('gallery')?->getUrl(),
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];
    }
}
