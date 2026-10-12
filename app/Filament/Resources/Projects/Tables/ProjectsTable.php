<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('project_code')->label('الكود')->searchable()->copyable(),
            TextColumn::make('project_name')->label('المشروع')->searchable(),
            TextColumn::make('client.name')->label('العميل')->searchable(),
            TextColumn::make('projectType.name')->label('النوع'),
            TextColumn::make('created_at')->label('تاريخ الإضافة')->dateTime('Y/m/d H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            SelectFilter::make('client')->label('العميل')->relationship('client', 'name')->searchable()->preload(),
        ])->recordActions([ViewAction::make(), EditAction::make()])
            ->defaultSort('id', 'desc')
            ->striped()->paginated([10, 25, 50])
            ->emptyStateHeading('لا توجد سجلات بعد')
            ->emptyStateDescription('ابدأ بإضافة أول سجل من زر الإضافة أعلى الصفحة.');
    }
}
