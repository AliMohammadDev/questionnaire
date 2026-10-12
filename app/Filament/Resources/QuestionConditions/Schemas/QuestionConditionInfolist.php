<?php

namespace App\Filament\Resources\QuestionConditions\Schemas;

use App\Filament\Support\QuestionnaireEntries;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class QuestionConditionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->icon(Heroicon::OutlinedInformationCircle)->schema([
                TextEntry::make('question.title')->label('السؤال الذي سيظهر')->placeholder('—'),
                TextEntry::make('dependsOnQuestion.title')->label('السؤال الذي يعتمد عليه')->placeholder('—'),
                TextEntry::make('operator')->label('المقارنة')->placeholder('—')->badge()->formatStateUsing(fn (string $state): string => ['equals' => 'يساوي', 'not_equals' => 'لا يساوي', 'greater_than' => 'أكبر من', 'less_than' => 'أصغر من', 'contains' => 'يحتوي', 'selected' => 'تم اختيار الخيار', 'not_selected' => 'لم يتم اختيار الخيار'][$state] ?? $state),
                TextEntry::make('questionOption.label')->label('الخيار المقارن')->placeholder('—'),
                TextEntry::make('value')->label('القيمة المقارنة')->placeholder('—')->columnSpanFull(),
                TextEntry::make('logic_type')->label('ربط الشروط')->placeholder('—')->badge()->formatStateUsing(fn (string $state): string => ['AND' => 'تحقق جميع الشروط', 'OR' => 'تحقق أحد الشروط'][$state] ?? $state),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            QuestionnaireEntries::timestamps(),
        ]);
    }
}
