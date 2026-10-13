<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
  use HasPageShield;

  protected function getHeaderActions(): array
  {
    return [
      Action::make('createProject')
        ->label('إضافة  استبيان جديد')
        ->icon(Heroicon::OutlinedPlus)
        ->url(fn(): string => ProjectResource::getUrl('create'))
        ->visible(fn(): bool => ProjectResource::canCreate()),
    ];
  }
}
