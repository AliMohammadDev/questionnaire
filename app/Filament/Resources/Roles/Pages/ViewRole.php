<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Resources\Roles\Pages\ViewRole as ShieldViewRole;
use Filament\Actions\EditAction;

class ViewRole extends ShieldViewRole
{
    protected static string $resource = RoleResource::class;

    protected static ?string $title = 'تفاصيل الدور والصلاحيات';

    protected function getHeaderActions(): array
    {
        return [EditAction::make()->label('تعديل الصلاحيات')];
    }
}
