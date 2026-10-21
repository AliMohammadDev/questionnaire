<?php

namespace App\Filament\Resources\ProjectCategories\Pages;

use App\Filament\Resources\ProjectCategories\ProjectCategoryResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProjectCategory extends ViewRecord
{
    protected static string $resource = ProjectCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')->label('رجوع')->icon('heroicon-o-arrow-right')->color('gray')
                ->url(static::getResource()::getUrl('index')),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
