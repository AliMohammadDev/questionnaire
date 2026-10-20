<?php

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(LazilyRefreshDatabase::class);

it('stores validated client details and returns the created client', function (): void {
    Sanctum::actingAs(User::factory()->create());
    $data = Client::factory()->make(['address' => 'دمشق'])->only(['name', 'phone', 'email', 'address']);

    $response = $this->postJson(route('api.clients.store'), $data + ['id' => 999]);

    $response->assertCreated()->assertExactJson(['data' => ['id' => Client::sole()->id] + $data]);
    $this->assertDatabaseHas('clients', $data);
    expect(Client::sole()->id)->not->toBe(999);
});

it('stores a client with only the required fields', function (): void {
    Sanctum::actingAs(User::factory()->create());
    $data = Client::factory()->make()->only(['name', 'phone']);

    $this->postJson(route('api.clients.store'), $data)
        ->assertCreated()->assertJsonPath('data.email', null)->assertJsonPath('data.address', null);

    $this->assertDatabaseHas('clients', $data + ['email' => null, 'address' => null]);
});

it('rejects invalid client details without creating a client', function (array $data, array $errors): void {
    Sanctum::actingAs(User::factory()->create());
    $this->postJson(route('api.clients.store'), $data)
        ->assertUnprocessable()->assertJsonValidationErrors($errors);

    $this->assertDatabaseCount('clients', 0);
})->with([
    'missing required fields' => [[], ['name', 'phone']],
    'invalid email' => [['name' => 'أحمد', 'phone' => '0912345678', 'email' => 'invalid'], ['email']],
    'invalid field types' => [['name' => [], 'phone' => 123, 'address' => []], ['name', 'phone', 'address']],
    'oversized fields' => [['name' => str_repeat('a', 256), 'phone' => str_repeat('1', 101), 'email' => str_repeat('a', 250).'@example.com'], ['name', 'phone', 'email']],
]);
