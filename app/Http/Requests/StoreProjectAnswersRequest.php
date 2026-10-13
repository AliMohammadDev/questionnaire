<?php

namespace App\Http\Requests;

use App\Models\Question;
use App\Services\QuestionnaireService;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectAnswersRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  /** @return Collection<int, Question> */
  public function questions(): Collection
  {
    return once(fn(): Collection => app(QuestionnaireService::class)
      ->getQuestions($this->route('project')->getAttributes())->keyBy('id'));
  }

  /** @return array<string, array<int, mixed>> */
  public function rules(): array
  {
    $questions = $this->questions();
    $answers = $this->all()['answers'] ?? [];
    $answers = is_array($answers) ? $answers : [];
    $rules = ['answers' => ['present', 'array', function (string $attribute, mixed $value, Closure $fail) use ($questions): void {
      if (is_array($value) && array_diff(array_keys($value), $questions->modelKeys()) !== []) {
        $fail('توجد إجابات لأسئلة لا تنتمي إلى استبيان المشروع.');
      }
    }]];
    $service = app(QuestionnaireService::class);

    foreach ($questions as $question) {
      $path = 'answers.' . $question->id;
      if (! $service->isVisible($question, $questions, $answers)) {
        $rules[$path] = ['exclude'];

        continue;
      }
      $rules[$path] = [$question->is_required ? 'required' : 'nullable'];
      $type = $question->answerType->code;
      $validation = $question->validation_rules ?? [];
      $optionIds = $question->options->modelKeys();
      $rules[$path] = array_merge($rules[$path], match ($type) {
        'number', 'rating', 'linear_scale' => ['numeric', 'decimal:0,4', 'between:-99999999999.9999,99999999999.9999'],
        'boolean' => ['boolean'],
        'date' => ['date_format:Y-m-d'],
        'time' => ['date_format:H:i'],
        'single_choice', 'dropdown' => ['integer', Rule::in($optionIds)],
        'multiple_choice', 'file' => ['array'],
        'multiple_choice_grid', 'checkbox_grid' => ['array', Rule::array($validation['rows'] ?? [])],
        default => ['string', 'max:65535'],
      });
      if (in_array($type, ['number', 'rating', 'linear_scale'], true)) {
        foreach (['min', 'max'] as $limit) {
          if (isset($validation[$limit])) {
            $rules[$path][] = $limit . ':' . $validation[$limit];
          }
        }
      }
      if ($type === 'multiple_choice') {
        $rules[$path . '.*'] = ['integer', 'distinct', Rule::in($optionIds)];
      }
      if ($type === 'file') {
        $rules[$path . '.*'] = ['file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'];
      }
      if (in_array($type, ['multiple_choice_grid', 'checkbox_grid'], true)) {
        foreach ($validation['rows'] ?? [] as $row) {
          $rowPath = $path . '.' . str_replace('.', '\\.', $row);
          $rules[$rowPath] = [$question->is_required ? 'required' : 'nullable'];
          if ($type === 'checkbox_grid') {
            $rules[$rowPath][] = 'array';
            $rules[$rowPath . '.*'] = ['string', 'distinct', Rule::in($validation['columns'] ?? [])];
          } else {
            $rules[$rowPath][] = 'string';
            $rules[$rowPath][] = Rule::in($validation['columns'] ?? []);
          }
        }
      }
    }

    return $rules;
  }
}
