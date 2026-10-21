<?php

namespace App\Filament\Resources\QuestionSections\Pages;

use App\Filament\Resources\QuestionSections\QuestionSectionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuestionSection extends ViewRecord
{
    protected static string $resource = QuestionSectionResource::class;

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
