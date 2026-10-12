<?php

namespace App\Filament\Resources\AnswerOptions\Schemas;

use App\Filament\Support\QuestionnaireFields;
use App\Models\Answer;
use Closure;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;

class AnswerOptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->description('يمكن إدارة هذه الاختيارات مباشرة من صفحة الإجابة أيضاً.')->schema([
                QuestionnaireFields::relationship('answer_id', 'answer', 'id', 'الإجابة')->getOptionLabelFromRecordUsing(fn (Answer $record): string => $record->project->project_code.' — '.$record->question->title)->live()->afterStateUpdated(fn (Set $set) => $set('question_option_id', null)),
                QuestionnaireFields::relationship('question_option_id', 'questionOption', 'label', 'الخيار المختار')->relationship('questionOption', 'label', modifyQueryUsing: fn (Builder $query, Get $get): Builder => $query->where('question_id', Answer::find($get('answer_id'))?->question_id ?? 0)->where('is_active', true))->exists(modifyRuleUsing: fn (Exists $rule, Get $get): Exists => $rule->where('question_id', Answer::find($get('answer_id'))?->question_id)->where('is_active', true))->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('answer_id', $get('answer_id')))->rules([fn (Get $get, ?Model $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                    $answer = Answer::with('question.answerType')->find($get('answer_id'));
                    if (! $answer?->question?->answerType?->has_options) {
                        $fail('السؤال لا يدعم الخيارات.');
                    } elseif (! $answer->question->answerType->allows_multiple && $answer->answerOptions()->when($record, fn (Builder $query): Builder => $query->whereKeyNot($record->getKey()))->exists()) {
                        $fail('هذا السؤال يسمح بخيار واحد فقط.');
                    }
                }]),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}
