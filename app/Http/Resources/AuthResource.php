<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\NewAccessToken;

/** @mixin NewAccessToken */
class AuthResource extends JsonResource
{
    /** @return array{token: string, token_type: string, user: array{id: int, name: string, email: string}} */
    public function toArray(Request $request): array
    {
        $user = $this->accessToken->tokenable;

        return [
            'token' => $this->plainTextToken,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];
    }
}
