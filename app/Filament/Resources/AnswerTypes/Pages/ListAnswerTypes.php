<?php

namespace App\Filament\Resources\AnswerTypes\Pages;

use App\Filament\Resources\AnswerTypes\AnswerTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnswerTypes extends ListRecords
{
    protected static string $resource = AnswerTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
