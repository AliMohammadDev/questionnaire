<?php

namespace App\Filament\Resources\QuestionConditions\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuestionConditionsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('question.title')->label('السؤال')->searchable()->limit(40),
            TextColumn::make('dependsOnQuestion.title')->label('يعتمد على')->limit(40),
            TextColumn::make('operator')->label('المقارنة')->badge(),
            TextColumn::make('logic_type')->label('ربط الشروط')->badge(),
            TextColumn::make('created_at')->label('تاريخ الإضافة')->dateTime('Y/m/d H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
        ])->recordActions([ViewAction::make(), EditAction::make()])
            ->defaultSort('id', 'desc')
            ->striped()->paginated([10, 25, 50])
            ->emptyStateHeading('لا توجد سجلات بعد')
            ->emptyStateDescription('ابدأ بإضافة أول سجل من زر الإضافة أعلى الصفحة.');
    }
}
