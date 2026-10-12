<?php

namespace App\Filament\Resources\QuestionContexts\Pages;

use App\Filament\Resources\QuestionContexts\QuestionContextResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuestionContexts extends ListRecords
{
    protected static string $resource = QuestionContextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
