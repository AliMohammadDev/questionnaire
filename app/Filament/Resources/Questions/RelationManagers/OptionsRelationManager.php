<?php

namespace App\Filament\Resources\Questions\RelationManagers;

use App\Filament\Resources\QuestionOptions\QuestionOptionResource;
use App\Filament\Support\RelatedRecordsRelationManager;

class OptionsRelationManager extends RelatedRecordsRelationManager
{
    protected static string $relationship = 'options';

    protected static ?string $relatedResource = QuestionOptionResource::class;

    protected static ?string $title = 'الخيارات';
}
