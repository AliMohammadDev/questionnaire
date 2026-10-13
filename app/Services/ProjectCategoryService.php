<?php

namespace App\Services;

use App\Models\ProjectCategory;
use Illuminate\Database\Eloquent\Collection;

class ProjectCategoryService
{
  /**
   * @return Collection<int, ProjectCategory>
   */
  public function getAllProjectCategories(): Collection
  {
    return ProjectCategory::query()
      ->with('media')
      ->orderBy('sort_order')
      ->orderBy('id')
      ->get();
  }
}
