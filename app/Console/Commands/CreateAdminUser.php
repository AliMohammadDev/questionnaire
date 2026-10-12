<?php

namespace App\Console\Commands;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Create an administrator for the questionnaire panel';

    public function handle(): int
    {
        $data = [
            'name' => $this->ask('Administrator name'),
            'email' => $this->ask('Administrator email'),
            'password' => $this->secret('Password (at least 12 characters)'),
        ];
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12'],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }
        $user = new User($validator->validated());
        $user->save();
        $this->call('db:seed', ['--class' => RolesAndPermissionsSeeder::class, '--no-interaction' => true]);
        $user->assignRole(config('filament-shield.super_admin.name'));
        $this->info('Administrator created. Open the /admin panel to sign in.');

        return self::SUCCESS;
    }
}
