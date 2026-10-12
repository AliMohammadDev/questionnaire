<?php

namespace App\Filament\Resources\AnswerOptions\Pages;

use App\Filament\Resources\AnswerOptions\AnswerOptionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAnswerOption extends EditRecord
{
    protected static string $resource = AnswerOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')->label('رجوع')->icon('heroicon-o-arrow-right')->color('gray')
                ->url(static::getResource()::getUrl('index')),
            DeleteAction::make(),
        ];
    }
}
