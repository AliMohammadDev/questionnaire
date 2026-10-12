<?php

namespace App\Filament\Resources\QuestionContexts\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuestionContextsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('question.title')->label('السؤال')->searchable()->limit(50),
            TextColumn::make('department.name')->label('القسم')->placeholder('جميع الأقسام'),
            TextColumn::make('projectCategory.name')->label('التصنيف')->placeholder('جميع التصنيفات'),
            TextColumn::make('projectType.name')->label('نوع المشروع')->placeholder('جميع الأنواع'),
            TextColumn::make('designStyle.name')->label('النمط')->placeholder('جميع الأنماط'),
            TextColumn::make('created_at')->label('تاريخ الإضافة')->dateTime('Y/m/d H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
        ])->recordActions([ViewAction::make(), EditAction::make()])
            ->defaultSort('id', 'desc')
            ->striped()->paginated([10, 25, 50])
            ->emptyStateHeading('لا توجد سجلات بعد')
            ->emptyStateDescription('ابدأ بإضافة أول سجل من زر الإضافة أعلى الصفحة.');
    }
}
