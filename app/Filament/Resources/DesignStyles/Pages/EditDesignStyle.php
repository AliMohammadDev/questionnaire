<?php

namespace App\Filament\Resources\DesignStyles\Pages;

use App\Filament\Resources\DesignStyles\DesignStyleResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDesignStyle extends EditRecord
{
    protected static string $resource = DesignStyleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')->label('رجوع')->icon('heroicon-o-arrow-right')->color('gray')
                ->url(static::getResource()::getUrl('index')),
            DeleteAction::make(),
        ];
    }
}
