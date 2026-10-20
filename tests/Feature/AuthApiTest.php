<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

uses(LazilyRefreshDatabase::class);

it('rehashes an outdated password after successful login', function (): void {
    $user = User::factory()->create();
    Hash::driver()->setRounds(5);

    $this->postJson(route('api.login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertOk();

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
    expect(Hash::needsRehash($user->fresh()->password))->toBeFalse();
    $this->assertDatabaseCount('personal_access_tokens', 1);
});

it('returns a bearer token and safe user details for valid credentials', function (): void {
    $user = User::factory()->create();

    $response = $this->postJson(route('api.login'), [
        'email' => $user->email,
        'password' => 'password',
        'name' => 'Unexpected name',
    ]);

    $response->assertOk()->assertExactJson([
        'data' => [
            'token' => $response->json('data.token'),
            'token_type' => 'Bearer',
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        ],
    ]);
    $token = PersonalAccessToken::findToken($response->json('data.token'));
    expect($token?->tokenable_id)->toBe($user->id);
    expect($token?->token)->not->toBe($response->json('data.token'));
    expect($user->fresh()->name)->toBe($user->name);
    $this->assertDatabaseCount('personal_access_tokens', 1);
});

it('returns 422 for incorrect credentials without issuing tokens', function (bool $existingUser): void {
    $user = User::factory()->create();

    $this->postJson(route('api.login'), [
        'email' => $existingUser ? $user->email : 'missing@example.com',
        'password' => 'incorrect-password',
    ])->assertUnprocessable()->assertJsonValidationErrors(['email' => __('auth.failed')]);

    $this->assertDatabaseCount('personal_access_tokens', 0);
})->with([true, false]);

it('returns 422 for invalid login input without issuing tokens', function (array $data, array $errors): void {
    $this->postJson(route('api.login'), $data)
        ->assertUnprocessable()->assertJsonValidationErrors($errors);

    $this->assertDatabaseCount('personal_access_tokens', 0);
})->with([
    'missing credentials' => [[], ['email', 'password']],
    'invalid email' => [['email' => 'invalid', 'password' => 'password'], ['email']],
    'invalid types' => [['email' => [], 'password' => []], ['email', 'password']],
    'email too long' => [['email' => str_repeat('a', 244).'@example.com', 'password' => 'password'], ['email']],
]);

it('revokes only the current token on logout', function (): void {
    $user = User::factory()->create();
    $currentToken = $user->createToken('current');
    $otherToken = $user->createToken('other');
    $anotherUserToken = User::factory()->create()->createToken('another-user');

    $this->withToken($currentToken->plainTextToken)->postJson(route('api.logout'))
        ->assertOk()->assertExactJson(['message' => 'Logout successful']);

    $this->assertModelMissing($currentToken->accessToken);
    $this->assertModelExists($otherToken->accessToken);
    $this->assertModelExists($anotherUserToken->accessToken);
    $this->app['auth']->forgetGuards();
    $this->withToken($currentToken->plainTextToken)->postJson(route('api.logout'))->assertUnauthorized();
});

it('returns 401 on logout without a valid bearer token', function (?string $token): void {
    $user = User::factory()->create();
    $existingToken = $user->createToken('existing');
    $headers = $token === null ? [] : ['Authorization' => 'Bearer '.$token];

    $this->postJson(route('api.logout'), [], $headers)->assertUnauthorized();

    $this->assertModelExists($existingToken->accessToken);
})->with([null, 'invalid-token']);

it('returns 401 for session-only logout without revoking API tokens', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('existing');

    $this->actingAs($user, 'web')->postJson(route('api.logout'))->assertUnauthorized();

    $this->assertModelExists($token->accessToken);
});

it('returns 429 after five login attempts per minute', function (): void {
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->postJson(route('api.login'), [
            'email' => 'missing@example.com',
            'password' => 'incorrect-password',
        ])->assertUnprocessable();
    }

    $this->postJson(route('api.login'), [
        'email' => 'missing@example.com',
        'password' => 'incorrect-password',
    ])->assertTooManyRequests();

    $this->assertDatabaseCount('personal_access_tokens', 0);
});
