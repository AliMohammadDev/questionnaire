<?php

namespace App\Filament\Resources\AnswerOptions\Pages;

use App\Filament\Resources\AnswerOptions\AnswerOptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnswerOptions extends ListRecords
{
    protected static string $resource = AnswerOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
