<?php

namespace App\Filament\Resources\Questions\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class QuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label('السؤال')->searchable()->limit(60),
            TextColumn::make('questionSection.name')->label('القسم')->searchable(),
            TextColumn::make('answerType.name')->label('نوع الإجابة'),
            IconColumn::make('is_active')->label('فعال')->boolean(),
            TextColumn::make('sort_order')->label('الترتيب')->sortable(),
            TextColumn::make('created_at')->label('تاريخ الإضافة')->dateTime('Y/m/d H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            TernaryFilter::make('is_active')->label('الحالة')->trueLabel('فعال')->falseLabel('غير فعال'),
        ])->recordActions([ViewAction::make(), EditAction::make()])
            ->defaultSort('sort_order', 'asc')
            ->striped()->paginated([10, 25, 50])
            ->emptyStateHeading('لا توجد سجلات بعد')
            ->emptyStateDescription('ابدأ بإضافة أول سجل من زر الإضافة أعلى الصفحة.');
    }
}
