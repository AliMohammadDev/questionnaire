<?php

namespace App\Filament\Resources\QuestionOptions\RelationManagers;

use App\Filament\Resources\QuestionConditions\QuestionConditionResource;
use App\Filament\Support\RelatedRecordsRelationManager;

class QuestionConditionsRelationManager extends RelatedRecordsRelationManager
{
    protected static string $relationship = 'questionConditions';

    protected static ?string $relatedResource = QuestionConditionResource::class;

    protected static ?string $title = 'الشروط المرتبطة بالخيار';
}
