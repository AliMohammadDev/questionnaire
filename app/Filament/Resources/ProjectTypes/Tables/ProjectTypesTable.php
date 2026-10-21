<?php

namespace App\Filament\Resources\ProjectTypes\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProjectTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            SpatieMediaLibraryImageColumn::make('image')->label('الصورة')->collection('image')->circular()->imageSize(48)->limit(1),
            TextColumn::make('name')->label('النوع')->searchable()->sortable(),
            TextColumn::make('projectCategory.name')->label('التصنيف')->searchable(),
            IconColumn::make('is_active')->label('فعال')->boolean(),
            TextColumn::make('sort_order')->label('الترتيب')->sortable(),
            TextColumn::make('created_at')->label('تاريخ الإضافة')->dateTime('Y/m/d H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            TernaryFilter::make('is_active')->label('الحالة')->trueLabel('فعال')->falseLabel('غير فعال'),
        ])->recordActions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->toolbarActions([DeleteBulkAction::make()])
            ->defaultSort('sort_order', 'asc')
            ->striped()->paginated([10, 25, 50])
            ->emptyStateHeading('لا توجد سجلات بعد')
            ->emptyStateDescription('ابدأ بإضافة أول سجل من زر الإضافة أعلى الصفحة.');
    }
}
