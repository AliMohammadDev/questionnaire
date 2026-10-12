<?php

namespace App\Filament\Resources\Departments\RelationManagers;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Support\RelatedRecordsRelationManager;

class ProjectsRelationManager extends RelatedRecordsRelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $relatedResource = ProjectResource::class;

    protected static ?string $title = 'المشاريع';
}
