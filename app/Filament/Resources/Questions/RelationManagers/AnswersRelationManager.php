<?php

namespace App\Filament\Resources\Questions\RelationManagers;

use App\Filament\Resources\Answers\AnswerResource;
use App\Filament\Support\RelatedRecordsRelationManager;

class AnswersRelationManager extends RelatedRecordsRelationManager
{
    protected static string $relationship = 'answers';

    protected static ?string $relatedResource = AnswerResource::class;

    protected static ?string $title = 'الإجابات';
}
