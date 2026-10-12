<?php

namespace App\Filament\Resources\Questions\Schemas;

use App\Filament\Resources\QuestionSections\QuestionSectionResource;
use App\Filament\Resources\QuestionSections\Schemas\QuestionSectionForm;
use App\Filament\Support\QuestionnaireFields;
use App\Models\AnswerType;
use App\Models\QuestionSection;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Gate;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->schema([
                QuestionnaireFields::relationship('question_section_id', 'questionSection', 'name', 'قسم الأسئلة')
                    ->createOptionForm(fn (Schema $schema): Schema => QuestionSectionForm::configure($schema))
                    ->createOptionUsing(function (array $data): int {
                        Gate::authorize('create', QuestionSection::class);

                        return QuestionSection::create($data)->id;
                    })
                    ->createOptionAction(fn (Action $action): Action => $action
                        ->label('إضافة قسم أسئلة جديد')
                        ->modalHeading('إضافة قسم أسئلة جديد')
                        ->modalSubmitActionLabel('إضافة القسم واختياره')
                        ->visible(fn (): bool => QuestionSectionResource::canCreate())),
                QuestionnaireFields::relationship('answer_type_id', 'answerType', 'name', 'نوع الإجابة')->live(),
                Textarea::make('title')->label('نص السؤال')->rows(3)->columnSpanFull()->required(),
                Textarea::make('description')->label('شرح السؤال')->rows(3)->columnSpanFull(),
                TextInput::make('placeholder')->label('النص الإرشادي')->maxLength(500),
                Toggle::make('is_required')->label('الإجابة إلزامية')->default(false),
                TagsInput::make('validation_rules.rows')->label('صفوف شبكة الإجابة')->required()->visible(fn (Get $get): bool => in_array(AnswerType::find($get('answer_type_id'))?->code, ['multiple_choice_grid', 'checkbox_grid'], true))->columnSpanFull(),
                TagsInput::make('validation_rules.columns')->label('خيارات أعمدة الشبكة')->required()->visible(fn (Get $get): bool => in_array(AnswerType::find($get('answer_type_id'))?->code, ['multiple_choice_grid', 'checkbox_grid'], true))->columnSpanFull(),

            ])->columns(2)->columnSpanFull(),
            Section::make('قواعد التحقق')->schema(fn (Get $get): array => in_array(AnswerType::find($get('answer_type_id'))?->code, ['multiple_choice_grid', 'checkbox_grid'], true) ? [] : [KeyValue::make('validation_rules')->label('قواعد إضافية')->keyLabel('القاعدة')->valueLabel('القيمة')->columnSpanFull()])->columnSpanFull(),
            Section::make('العرض والترتيب')->schema([
                Toggle::make('is_active')->label('فعال')->default(true),
                TextInput::make('sort_order')->label('ترتيب الظهور')->numeric()->integer()->minValue(0)->default(0)->required(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}
