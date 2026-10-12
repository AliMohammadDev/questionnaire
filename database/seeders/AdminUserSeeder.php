<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $administrator = User::where('email', 'admin@gmail.com')->first();
        if ($administrator) {
            $administrator->assignRole(config('filament-shield.super_admin.name'));
            $this->command?->info('Administrator already exists; password unchanged.');

            return;
        }

        $password = 'password';
        $administrator = User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@gmail.com',
            'password' => $password,
        ]);
        $administrator->assignRole(config('filament-shield.super_admin.name'));

        $this->command?->info('Administrator email: admin@gmail.com');
        $this->command?->info('Administrator password: '.$password);
    }
}
