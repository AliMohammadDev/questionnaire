<?php

namespace App\Filament\Resources\AnswerTypes\Pages;

use App\Filament\Resources\AnswerTypes\AnswerTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnswerType extends CreateRecord
{
    protected static string $resource = AnswerTypeResource::class;
}
