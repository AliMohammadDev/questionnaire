<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Client> */
class ClientFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['أحمد العلي', 'ليلى حسن', 'محمد الخطيب', 'سارة إبراهيم', 'عمر الحسن', 'نور أحمد']),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
        ];
    }
}
