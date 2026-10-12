<?php

namespace App\Filament\Resources\Answers\Schemas;

use App\Filament\Support\QuestionnaireFields;
use App\Models\Question;
use App\Models\QuestionOption;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class AnswerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->description('تظهر حقول الإجابة حسب نوع السؤال. إدارة الاختيارات من هذه الصفحة تحفظها في جدول خيارات الإجابات.')->schema([
                QuestionnaireFields::relationship('project_id', 'project', 'project_code', 'المشروع'),
                QuestionnaireFields::relationship('question_id', 'question', 'title', 'السؤال')->disabled(fn (string $operation): bool => $operation === 'edit')->dehydrated()->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('project_id', $get('project_id')))->live()->afterStateUpdated(function (Set $set): void {
                    foreach (['value_text', 'value_number', 'value_boolean', 'value_date', 'value_time', 'value_json'] as $field) {
                        $set($field, null);
                    }
                    $set('options', []);
                }),
                Textarea::make('value_text')->label('الإجابة النصية')->rows(3)->columnSpanFull()->visible(fn (Get $get): bool => in_array(QuestionnaireFields::answerCode($get), ['short_text', 'paragraph', 'duration'])),
                TextInput::make('value_number')->label('الإجابة الرقمية')->numeric()->step('0.0001')->visible(fn (Get $get): bool => in_array(QuestionnaireFields::answerCode($get), ['number', 'rating', 'linear_scale'])),
                Select::make('value_boolean')->label('الإجابة')->options([1 => 'نعم', 0 => 'لا'])->visible(fn (Get $get): bool => QuestionnaireFields::answerCode($get) === 'boolean'),
                DatePicker::make('value_date')->label('التاريخ')->native(false)->visible(fn (Get $get): bool => QuestionnaireFields::answerCode($get) === 'date'),
                TimePicker::make('value_time')->label('الوقت')->seconds(false)->visible(fn (Get $get): bool => QuestionnaireFields::answerCode($get) === 'time'),
                Select::make('options')->label('الخيارات المختارة')->relationship('options', 'label', modifyQueryUsing: fn (Builder $query, Get $get): Builder => $query->where('question_id', $get('question_id') ?? 0)->where('is_active', true))->multiple()->maxItems(fn (Get $get): ?int => QuestionnaireFields::answerCode($get) === 'multiple_choice' ? null : 1)->searchable()->preload()->visible(fn (Get $get): bool => in_array(QuestionnaireFields::answerCode($get), ['single_choice', 'multiple_choice', 'dropdown']))->rules([fn (Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                    if (QuestionOption::whereIn('id', (array) $value)->where('question_id', $get('question_id'))->where('is_active', true)->count() !== count((array) $value)) {
                        $fail('اختر خيارات فعالة تابعة للسؤال المحدد.');
                    }
                }])->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
            Section::make('شبكة الإجابة')->visible(fn (Get $get): bool => in_array(QuestionnaireFields::answerCode($get), ['multiple_choice_grid', 'checkbox_grid'], true))->schema(function (Get $get): array {
                $rules = Question::find($get('question_id'))?->validation_rules ?? [];
                $columns = $rules['columns'] ?? [];

                return array_map(fn (string $row): Select => Select::make('value_json.'.$row)->label($row)->options(array_combine($columns, $columns))->multiple(QuestionnaireFields::answerCode($get) === 'checkbox_grid')->native(false), $rules['rows'] ?? []);
            })->columns(2)->columnSpanFull(),
            Section::make('بيانات إضافية')->hidden(fn (Get $get): bool => in_array(QuestionnaireFields::answerCode($get), ['multiple_choice_grid', 'checkbox_grid'], true))->schema(fn (Get $get): array => in_array(QuestionnaireFields::answerCode($get), ['multiple_choice_grid', 'checkbox_grid'], true) ? [] : [KeyValue::make('value_json')->label('بيانات مرنة إضافية')->columnSpanFull()])->collapsible()->collapsed()->columnSpanFull(),
            Section::make('الصور والمرفقات')->schema([
                SpatieMediaLibraryFileUpload::make('attachments')->label('ملفات الإجابة')->collection('attachments')->disk('public')->maxSize(10240)->multiple()->reorderable()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])->downloadable()->openable()->columnSpanFull()->visible(fn (Get $get): bool => QuestionnaireFields::answerCode($get) === 'file'),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}
