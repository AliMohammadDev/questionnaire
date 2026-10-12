<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Filament\Resources\Questions\QuestionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuestion extends EditRecord
{
    protected static string $resource = QuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')->label('رجوع')->icon('heroicon-o-arrow-right')->color('gray')
                ->url(static::getResource()::getUrl('index')),
            DeleteAction::make(),
        ];
    }
}
