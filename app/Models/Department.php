<?php

namespace App\Models;

use Database\Factories\DepartmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['name', 'description', 'is_active', 'sort_order'])]
class Department extends Model implements HasMedia
{
    /** @use HasFactory<DepartmentFactory> */
    use HasFactory;

    use InteractsWithMedia;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function projectCategories(): HasMany
    {
        return $this->hasMany(ProjectCategory::class, 'department_id');
    }

    public function questionContexts(): HasMany
    {
        return $this->hasMany(QuestionContext::class, 'department_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'department_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')->useDisk('public');
    }
}
