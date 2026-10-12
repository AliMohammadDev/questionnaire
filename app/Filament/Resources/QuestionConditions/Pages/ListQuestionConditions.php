<?php

namespace App\Filament\Resources\QuestionConditions\Pages;

use App\Filament\Resources\QuestionConditions\QuestionConditionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuestionConditions extends ListRecords
{
    protected static string $resource = QuestionConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
