<?php

namespace App\Filament\Resources\ProjectCategories\RelationManagers;

use App\Filament\Resources\ProjectTypes\ProjectTypeResource;
use App\Filament\Support\RelatedRecordsRelationManager;

class ProjectTypesRelationManager extends RelatedRecordsRelationManager
{
    protected static string $relationship = 'projectTypes';

    protected static ?string $relatedResource = ProjectTypeResource::class;

    protected static ?string $title = 'أنواع المشاريع';
}
