<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionCondition;
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
            ->with(['questionSection', 'answerType', 'conditions.dependsOnQuestion:id,title', 'options.media', 'options' => fn (HasMany $query): HasMany => $query->where('is_active', true)->orderBy('sort_order')->orderBy('id')])
            ->orderBy('sort_order')->orderBy('id')->get();
    }

    /**
     * @param  Collection<int, Question>  $questions
     * @param  array<int, mixed>  $answers
     * @param  array<int>  $visited
     */
    public function isVisible(Question $question, Collection $questions, array $answers, array $visited = []): bool
    {
        if (in_array($question->id, $visited, true)) {
            return false;
        }
        $visited[] = $question->id;
        $result = null;
        foreach ($question->conditions as $condition) {
            $source = $questions->get($condition->depends_on_question_id);
            $value = $answers[$condition->depends_on_question_id] ?? null;
            $matches = $source && $this->isVisible($source, $questions, $answers, $visited)
              && filled($value) && $this->matches($condition, $value);
            $result = $result === null ? $matches : ($condition->logic_type === 'OR' ? $result || $matches : $result && $matches);
        }

        return $result ?? true;
    }

    private function matches(QuestionCondition $condition, mixed $value): bool
    {
        foreach ((array) $value as $item) {
            if (! is_scalar($item) && $item !== null) {
                return false;
            }
        }

        $expected = $condition->question_option_id ?? $condition->value;
        $selected = in_array((string) $expected, array_map('strval', (array) $value), true);

        return match ($condition->operator) {
            'equals', 'selected' => $selected,
            'not_equals', 'not_selected' => ! $selected,
            'greater_than' => is_numeric($value) && is_numeric($expected) && $value > $expected,
            'less_than' => is_numeric($value) && is_numeric($expected) && $value < $expected,
            'contains' => is_array($value) ? $selected : str_contains((string) $value, (string) $expected),
            default => false,
        };
    }
}
