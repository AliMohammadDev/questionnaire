<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Resources\Roles\Pages\EditRole as ShieldEditRole;
use Filament\Actions\DeleteAction;

class EditRole extends ShieldEditRole
{
    protected static string $resource = RoleResource::class;

    protected static ?string $title = 'تعديل الدور والصلاحيات';

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['guard_name'] = 'web';
        $this->data['guard_name'] = 'web';

        return parent::mutateFormDataBeforeSave($data);
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
