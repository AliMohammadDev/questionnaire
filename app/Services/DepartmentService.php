<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;

class DepartmentService
{
    /**
     * @return Collection<int, Department>
     */
    public function getAllDepartments(): Collection
    {
        return Department::query()
            ->with('media')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
