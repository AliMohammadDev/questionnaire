<?php

namespace App\Filament\Resources\QuestionOptions\Pages;

use App\Filament\Resources\QuestionOptions\QuestionOptionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuestionOption extends ViewRecord
{
    protected static string $resource = QuestionOptionResource::class;

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
