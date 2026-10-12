<?php

namespace App\Filament\Support;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

abstract class RelatedRecordsRelationManager extends RelationManager
{
    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([])
            ->recordActions([ViewAction::make()])
            ->toolbarActions([])
            ->emptyStateHeading('لا توجد سجلات مرتبطة')
            ->emptyStateDescription('ستظهر هنا السجلات التابعة لهذا السجل عند إضافتها.');
    }
}
