<?php

namespace App\Filament\Resources\Questions\RelationManagers;

use App\Filament\Resources\QuestionConditions\QuestionConditionResource;
use App\Filament\Support\RelatedRecordsRelationManager;

class ConditionsRelationManager extends RelatedRecordsRelationManager
{
    protected static string $relationship = 'conditions';

    protected static ?string $relatedResource = QuestionConditionResource::class;

    protected static ?string $title = 'شروط ظهور السؤال';
}
