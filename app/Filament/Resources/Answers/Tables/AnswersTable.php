<?php

namespace App\Filament\Resources\Answers\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnswersTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('project.project_code')->label('المشروع')->searchable(),
            TextColumn::make('question.title')->label('السؤال')->searchable()->limit(50),
            TextColumn::make('value_text')->label('الإجابة النصية')->limit(40),
            TextColumn::make('value_number')->label('الرقم'),
            TextColumn::make('options.label')->label('الخيارات')->badge(),
            TextColumn::make('created_at')->label('تاريخ الإضافة')->dateTime('Y/m/d H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
        ])->recordActions([ViewAction::make(), EditAction::make()])
            ->defaultSort('id', 'desc')
            ->striped()->paginated([10, 25, 50])
            ->emptyStateHeading('لا توجد سجلات بعد')
            ->emptyStateDescription('ابدأ بإضافة أول سجل من زر الإضافة أعلى الصفحة.');
    }
}
