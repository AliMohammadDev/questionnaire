<?php

namespace App\Filament\Resources\Answers\Pages;

use App\Filament\Resources\Answers\AnswerResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAnswer extends EditRecord
{
    protected static string $resource = AnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')->label('رجوع')->icon('heroicon-o-arrow-right')->color('gray')
                ->url(static::getResource()::getUrl('index')),
            DeleteAction::make(),
        ];
    }
}
