<?php

namespace App\Filament\Resources\QuestionConditions\Schemas;

use App\Filament\Support\QuestionnaireFields;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Exists;

class QuestionConditionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->description('اختر سؤالاً مختلفاً للمقارنة، ثم حدد خياراً أو قيمة تحقق الشرط.')->schema([
                QuestionnaireFields::relationship('question_id', 'question', 'title', 'السؤال الذي سيظهر'),
                QuestionnaireFields::relationship('depends_on_question_id', 'dependsOnQuestion', 'title', 'السؤال الذي يعتمد عليه')->different('question_id')->live()->afterStateUpdated(fn (Set $set) => $set('question_option_id', null)),
                Select::make('operator')->label('المقارنة')->options(['equals' => 'يساوي', 'not_equals' => 'لا يساوي', 'greater_than' => 'أكبر من', 'less_than' => 'أصغر من', 'contains' => 'يحتوي', 'selected' => 'تم اختيار الخيار', 'not_selected' => 'لم يتم اختيار الخيار'])->required()->live(),
                QuestionnaireFields::relationship('question_option_id', 'questionOption', 'label', 'الخيار المقارن')->required(false)->relationship('questionOption', 'label', modifyQueryUsing: fn (Builder $query, Get $get): Builder => $query->where('question_id', $get('depends_on_question_id') ?? 0))->exists(modifyRuleUsing: fn (Exists $rule, Get $get): Exists => $rule->where('question_id', $get('depends_on_question_id')))->required(fn (Get $get): bool => in_array($get('operator'), ['selected', 'not_selected'])),
                Textarea::make('value')->label('القيمة المقارنة')->rows(3)->columnSpanFull()->required(fn (Get $get): bool => ! $get('question_option_id')),
                Select::make('logic_type')->label('ربط الشروط')->options(['AND' => 'تحقق جميع الشروط', 'OR' => 'تحقق أحد الشروط'])->default('AND')->required(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}
