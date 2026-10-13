<?php

namespace App\Http\Resources;

use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ProjectType */
class ProjectTypeResource extends JsonResource
{
    /**
     * @return array{id: int, project_category_id: int, name: string, description: string|null, image: string|null, is_active: bool, sort_order: int}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_category_id' => $this->project_category_id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->getFirstMedia('image')?->getUrl(),
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];
    }
}
