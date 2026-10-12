<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Resources\Roles\Pages\CreateRole as ShieldCreateRole;

class CreateRole extends ShieldCreateRole
{
    protected static string $resource = RoleResource::class;

    protected static ?string $title = 'إضافة دور جديد';

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['guard_name'] = 'web';
        $this->data['guard_name'] = 'web';

        return parent::mutateFormDataBeforeCreate($data);
    }
}
