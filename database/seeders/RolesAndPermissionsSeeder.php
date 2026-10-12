<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (array_keys(FilamentShield::getResources()) as $resource) {
            Utils::generateForResource($resource);
        }
        foreach (FilamentShield::getPages() as $page) {
            Utils::generateForPageOrWidget(array_key_first($page['permissions']));
        }

        $panelUser = Role::findOrCreate(config('filament-shield.panel_user.name'), 'web');
        $panelUser->givePermissionTo('View:Dashboard');
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
