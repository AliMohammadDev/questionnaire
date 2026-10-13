<?php

namespace App\Services;

use App\Models\AnswerType;
use Illuminate\Database\Eloquent\Collection;

class AnswerTypeService
{
  /**
   * @return Collection<int, AnswerType>
   */
  public function getAllAnswerTypes(): Collection
  {
    return AnswerType::query()
      ->orderBy('sort_order')
      ->orderBy('id')
      ->get();
  }
}
