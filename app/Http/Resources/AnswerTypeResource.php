<?php

namespace App\Http\Resources;

use App\Models\AnswerType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AnswerType */
class AnswerTypeResource extends JsonResource
{
    /** @return array{id: int, name: string, code: string, has_options: bool, allows_multiple: bool, is_active: bool, sort_order: int} */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'has_options' => $this->has_options,
            'allows_multiple' => $this->allows_multiple,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];
    }
}
