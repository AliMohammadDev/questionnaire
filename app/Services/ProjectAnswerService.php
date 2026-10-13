<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Question;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProjectAnswerService
{
  /**
   * @param  Collection<int, Question>  $questions
   * @param  array<int, mixed>  $answers
   */
  public function storeAnswers(Project $project, Collection $questions, array $answers): void
  {
    DB::transaction(function () use ($project, $questions, $answers): void {
      Project::query()->whereKey($project->id)->lockForUpdate()->firstOrFail();
      foreach ($questions as $question) {
        $value = $answers[$question->id] ?? null;
        $answer = $project->answers()->where('question_id', $question->id)->first();
        if (blank($value)) {
          $answer?->options()->detach();
          $answer?->delete();

          continue;
        }
        $type = $question->answerType->code;
        $values = array_fill_keys(['value_text', 'value_number', 'value_boolean', 'value_date', 'value_time', 'value_json'], null);
        $column = match ($type) {
          'number', 'rating', 'linear_scale' => 'value_number',
          'boolean' => 'value_boolean',
          'date' => 'value_date',
          'time' => 'value_time',
          'multiple_choice_grid', 'checkbox_grid' => 'value_json',
          default => 'value_text',
        };
        if (! in_array($type, ['single_choice', 'multiple_choice', 'dropdown', 'file'], true)) {
          $values[$column] = $value;
        }
        $answer = $project->answers()->updateOrCreate(['question_id' => $question->id], $values);
        $answer->options()->sync(in_array($type, ['single_choice', 'multiple_choice', 'dropdown'], true) ? (array) $value : []);
        if ($type === 'file') {
          $answer->clearMediaCollection('attachments');
          foreach ($value as $file) {
            $answer->addMedia($file)->toMediaCollection('attachments');
          }
        }
      }
    });
  }
}
