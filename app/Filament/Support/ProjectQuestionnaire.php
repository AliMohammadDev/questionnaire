<?php

namespace App\Filament\Support;

use App\Models\Project;
use App\Models\Question;
use App\Models\QuestionCondition;
use App\Services\QuestionnaireService;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProjectQuestionnaire
{
    /**
     * @param  array<string, mixed>  $projectData
     * @return Collection<int, Question>
     */
    public static function questions(array $projectData): Collection
    {
        return app(QuestionnaireService::class)->getQuestions($projectData);
    }

    /**
     * @param  Collection<int, Question>  $questions
     * @return array<Component>
     */
    public static function components(Collection $questions): array
    {
        if ($questions->isEmpty()) {
            return [Text::make('لا توجد أسئلة فعالة مرتبطة باختيارات هذا المشروع. يمكنك الرجوع لتغيير الاختيارات أو حفظ المشروع.')];
        }

        $indexedQuestions = $questions->keyBy('id');

        return $questions->sortBy(fn (Question $question): int => $question->questionSection->sort_order)
            ->groupBy('question_section_id')->map(function (Collection $sectionQuestions) use ($indexedQuestions): Section {
                $section = $sectionQuestions->first()->questionSection;

                return Section::make($section->name)->description($section->description)
                    ->schema($sectionQuestions->map(fn (Question $question): Component => self::component($question, $indexedQuestions))->all())
                    ->visible(fn (Get $get): bool => $sectionQuestions->contains(fn (Question $question): bool => self::isVisible($question, $indexedQuestions, (array) $get('data.questionnaire', true))))
                    ->columns(2)->columnSpanFull();
            })->values()->all();
    }

    /** @param Collection<int, Question> $questions */
    private static function component(Question $question, Collection $questions): Component
    {
        $name = 'questionnaire.'.$question->id;
        $code = $question->answerType->code;
        $rules = $question->validation_rules ?? [];
        $visible = fn (Get $get): bool => self::isVisible($question, $questions, (array) $get('data.questionnaire', true));

        if (in_array($code, ['multiple_choice_grid', 'checkbox_grid'], true)) {
            $columns = $rules['columns'] ?? [];

            return Section::make($question->title)->description($question->description)->visible($visible)
                ->schema(array_map(fn (string $row): Select => Select::make($name.'.'.$row)->label($row)
                    ->options(array_combine($columns, $columns))->multiple($code === 'checkbox_grid')
                    ->in($columns)->required($question->is_required)->native(false)->live(), $rules['rows'] ?? []))
                ->columnSpanFull();
        }

        $options = $question->options->pluck('label', 'id')->all();
        $field = match ($code) {
            'paragraph' => Textarea::make($name)->rows(3),
            'number', 'rating', 'linear_scale' => TextInput::make($name)->numeric()->step('0.0001')
                ->minValue($rules['min'] ?? null)->maxValue($rules['max'] ?? null),
            'boolean' => Select::make($name)->options([1 => 'نعم', 0 => 'لا'])->rules([Rule::in([0, 1])])->native(false),
            'single_choice', 'dropdown', 'multiple_choice' => Select::make($name)->options($options)
                ->multiple($code === 'multiple_choice')->searchable()->native(false)
                ->in(array_keys($options)),
            'date' => DatePicker::make($name)->native(false),
            'time' => TimePicker::make($name)->seconds(false),
            'file' => FileUpload::make($name)->disk('public')->storeFiles(false)->multiple()->maxSize(10240)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                ->rules([fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                    if (! $value instanceof TemporaryUploadedFile) {
                        $fail('يرجى رفع ملف صالح.');
                    }
                }]),
            default => TextInput::make($name)->maxLength(65535),
        };

        return $field->label($question->title)->helperText($question->description)
            ->placeholder($question->placeholder)->required($question->is_required)
            ->visible($visible)->live()->columnSpanFull();
    }

    /**
     * @param  Collection<int, Question>  $questions
     * @param  array<int, mixed>  $answers
     * @param  array<int>  $visited
     */
    public static function isVisible(Question $question, Collection $questions, array $answers, array $visited = []): bool
    {
        if (in_array($question->id, $visited, true)) {
            return false;
        }
        $visited[] = $question->id;
        $result = null;
        foreach ($question->conditions as $condition) {
            $source = $questions->get($condition->depends_on_question_id);
            $value = $answers[$condition->depends_on_question_id] ?? null;
            $matches = $source && self::isVisible($source, $questions, $answers, $visited)
              && filled($value) && self::matches($condition, $value);
            $result = $result === null ? $matches : ($condition->logic_type === 'OR' ? $result || $matches : $result && $matches);
        }

        return $result ?? true;
    }

    private static function matches(QuestionCondition $condition, mixed $value): bool
    {
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

    /**
     * @param  Collection<int, Question>  $questions
     * @param  array<int, mixed>  $answers
     */
    public static function saveAnswers(Project $project, Collection $questions, array $answers): void
    {
        $questions = $questions->keyBy('id');
        foreach ($questions as $question) {
            $value = $answers[$question->id] ?? null;
            if (! self::isVisible($question, $questions, $answers) || blank($value)) {
                continue;
            }
            $code = $question->answerType->code;
            $column = match ($code) {
                'number', 'rating', 'linear_scale' => 'value_number',
                'boolean' => 'value_boolean',
                'date' => 'value_date',
                'time' => 'value_time',
                'multiple_choice_grid', 'checkbox_grid' => 'value_json',
                default => 'value_text',
            };
            $answer = $project->answers()->create(['question_id' => $question->id] +
              (in_array($code, ['single_choice', 'multiple_choice', 'dropdown', 'file'], true) ? [] : [$column => $value]));

            if (in_array($code, ['single_choice', 'multiple_choice', 'dropdown'], true)) {
                $answer->options()->sync((array) $value);
            }
            if ($code === 'file') {
                foreach ($value as $file) {
                    $answer->addMedia($file->getRealPath())->usingName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                        ->usingFileName($file->getClientOriginalName())->toMediaCollection('attachments');
                }
            }
        }
    }
}
