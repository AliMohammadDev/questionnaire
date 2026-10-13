<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionnaireService
{
    /**
     * @param  array<string, mixed>  $projectData
     * @return Collection<int, Question>
     */
    public function getQuestions(array $projectData): Collection
    {
        return Question::query()->where('is_active', true)
            ->whereHas('questionSection', fn (Builder $query): Builder => $query->where('is_active', true))
            ->whereHas('answerType', fn (Builder $query): Builder => $query->where('is_active', true))
            ->whereHas('contexts', function (Builder $query) use ($projectData): void {
                foreach (['department_id', 'project_category_id', 'project_type_id', 'design_style_id'] as $column) {
                    $query->where(function (Builder $query) use ($projectData, $column): void {
                        $query->whereNull($column);
                        if (filled($projectData[$column] ?? null)) {
                            $query->orWhere($column, $projectData[$column]);
                        }
                    });
                }
            })
            ->with(['questionSection', 'answerType', 'conditions.dependsOnQuestion:id,title', 'options' => fn (HasMany $query): HasMany => $query->where('is_active', true)->orderBy('sort_order')->orderBy('id')])
            ->orderBy('sort_order')->orderBy('id')->get();
    }
}
