<?php

namespace App\Http\Resources;

use App\Models\DesignStyle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DesignStyle */
class DesignStyleResource extends JsonResource
{
    /**
     * @return array{id: int, name: string, description: string|null, image: string|null, is_active: bool, sort_order: int}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->getFirstMedia('cover')?->getUrl(),
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];
    }
}
