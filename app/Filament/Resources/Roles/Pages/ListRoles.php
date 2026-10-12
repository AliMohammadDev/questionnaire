<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Users\UserResource;
use BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles as ShieldListRoles;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;

class ListRoles extends ShieldListRoles
{
    protected static string $resource = RoleResource::class;

    protected static ?string $title = 'الأدوار';

    public function getSubheading(): ?string
    {
        return 'أضف الأدوار وحدد صلاحيات كل صفحة، ثم أسند الأدوار إلى المستخدمين من إدارة المستخدمين.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('إضافة دور جديد'),
            Action::make('assignRoles')->label('إسناد الأدوار للمستخدمين')->color('gray')
                ->url(fn (): string => UserResource::getUrl('index'))
                ->visible(fn (): bool => UserResource::canViewAny()),
        ];
    }
}
