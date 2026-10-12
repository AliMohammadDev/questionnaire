<?php

namespace App\Filament\Resources\ProjectTypes\Pages;

use App\Filament\Resources\ProjectTypes\ProjectTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectType extends CreateRecord
{
    protected static string $resource = ProjectTypeResource::class;
}
