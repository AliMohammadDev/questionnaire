<?php

namespace App\Services;

use App\Models\DesignStyle;
use Illuminate\Database\Eloquent\Collection;

class DesignStyleService
{
    /**
     * @return Collection<int, DesignStyle>
     */
    public function getAllDesignStyles(): Collection
    {
        return DesignStyle::query()
            ->with('media')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
