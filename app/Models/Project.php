<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['user_id', 'client_id', 'department_id', 'project_category_id', 'project_type_id', 'project_code', 'project_name', 'notes', 'additional_questions', 'submitted_at', 'completed_at'])]
class Project extends Model implements HasMedia
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    use InteractsWithMedia;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'additional_questions' => 'array',
            'user_id' => 'integer',
            'client_id' => 'integer',
            'department_id' => 'integer',
            'project_category_id' => 'integer',
            'project_type_id' => 'integer',
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function projectCategory(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function projectType(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

    public function designStyles(): BelongsToMany
    {
        return $this->belongsToMany(DesignStyle::class);
    }

    /** @return array<int, int> */
    public function selectedDesignStyleIds(): array
    {
        return $this->designStyles->modelKeys();
    }

    /** @return array<string, mixed> */
    public function questionnaireData(): array
    {
        return [...$this->getAttributes(), 'design_style_ids' => $this->selectedDesignStyleIds()];
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'project_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')->useDisk('public');
    }
}
