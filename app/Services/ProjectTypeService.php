<?php

namespace App\Services;

use App\Models\ProjectType;
use Illuminate\Database\Eloquent\Collection;

class ProjectTypeService
{
    /**
     * @return Collection<int, ProjectType>
     */
    public function getAllProjectTypes(): Collection
    {
        return ProjectType::query()
            ->with('media')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
