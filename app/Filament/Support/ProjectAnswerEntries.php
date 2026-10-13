<?php

namespace App\Filament\Support;

use App\Models\Answer;
use App\Models\Project;
use App\Models\Question;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Gate;

class ProjectAnswerEntries
{
  /** @return array<Section|TextEntry> */
  public static function sections(Project $project): array
  {
    if (! Gate::allows('viewAny', Answer::class) || ! Gate::allows('view', new Answer)) {
      return [];
    }

    $answers = $project->answers()->with(['question.questionSection', 'options', 'media'])->orderBy('id')->get()->keyBy('question_id');
    $questions = ProjectQuestionnaire::questions($project->getAttributes())
      ->merge($answers->pluck('question'))->unique('id')
      ->sortBy(fn(Question $question): array => [$question->questionSection->sort_order, $question->questionSection->id, $question->sort_order, $question->id]);

    if ($questions->isEmpty()) {
      return [TextEntry::make('empty_questionnaire')->hiddenLabel()->state('لا توجد أسئلة أو إجابات لهذا المشروع بعد.')];
    }

    $sections = [];
    $questionNumber = 0;
    foreach ($questions->groupBy('question_section_id') as $sectionQuestions) {
      $entries = [];
      foreach ($sectionQuestions as $question) {
        $answer = $answers->get($question->id);
        $questionNumber++;
        $questionEntries = [TextEntry::make('question_' . $question->id)->label($questionNumber . '. ' . $question->title)
          ->state(fn(): string|array|null => $answer ? self::value($answer) : null)
          ->placeholder('لم تتم الإجابة')->listWithLineBreaks()
          ->extraEntryWrapperAttributes(['class' => 'project-question-answer'])];
        if ($answer && $answer->media->isNotEmpty()) {
          $questionEntries[] = Section::make('مرفقات الإجابة: ' . $question->title)
            ->schema([QuestionnaireEntries::attachments()->state(fn() => $answer->media)])->columnSpanFull();
        }
        $entries[] = Group::make($questionEntries);
      }
      $section = $sectionQuestions->first()->questionSection;
      $sections[] = Section::make($section->name)->description($section->description)
        ->schema($entries)->columns(['default' => 1, 'lg' => 2])
        ->extraAttributes(['class' => 'project-answer-section'])->columnSpanFull();
    }

    return $sections;
  }

  /** @return string|array<string>|null */
  private static function value(Answer $answer): string|array|null
  {
    if ($answer->options->isNotEmpty()) {
      return $answer->options->pluck('label')->all();
    }
    if ($answer->value_boolean !== null) {
      return $answer->value_boolean ? 'نعم' : 'لا';
    }
    if ($answer->value_number !== null) {
      return (string) $answer->value_number;
    }
    if ($answer->value_date !== null) {
      return $answer->value_date->format('Y/m/d');
    }
    if ($answer->value_time !== null) {
      return $answer->value_time;
    }
    if ($answer->value_json !== null) {
      return collect($answer->value_json)->map(fn(mixed $value, string|int $key): string => $key . ': ' . self::formatStructuredValue($value))->values()->all();
    }

    return $answer->value_text;
  }

  private static function formatStructuredValue(mixed $value): string
  {
    if (is_array($value)) {
      return implode('، ', array_map(self::formatStructuredValue(...), $value));
    }

    return is_bool($value) ? ($value ? 'نعم' : 'لا') : (string) $value;
  }
}
