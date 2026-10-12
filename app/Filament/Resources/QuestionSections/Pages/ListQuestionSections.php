<?php

namespace App\Filament\Resources\QuestionSections\Pages;

use App\Filament\Resources\QuestionSections\QuestionSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuestionSections extends ListRecords
{
    protected static string $resource = QuestionSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
