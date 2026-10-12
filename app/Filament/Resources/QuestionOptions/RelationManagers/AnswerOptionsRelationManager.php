<?php

namespace App\Filament\Resources\QuestionOptions\RelationManagers;

use App\Filament\Resources\AnswerOptions\AnswerOptionResource;
use App\Filament\Support\RelatedRecordsRelationManager;

class AnswerOptionsRelationManager extends RelatedRecordsRelationManager
{
    protected static string $relationship = 'answerOptions';

    protected static ?string $relatedResource = AnswerOptionResource::class;

    protected static ?string $title = 'اختيارات الإجابات';
}
