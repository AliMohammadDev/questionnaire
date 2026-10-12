<?php

namespace App\Filament\Resources\AnswerTypes\Pages;

use App\Filament\Resources\AnswerTypes\AnswerTypeResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAnswerType extends ViewRecord
{
    protected static string $resource = AnswerTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')->label('رجوع')->icon('heroicon-o-arrow-right')->color('gray')
                ->url(static::getResource()::getUrl('index')),
            EditAction::make(),
        ];
    }
}
