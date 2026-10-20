<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('returns 401 for protected endpoints without valid credentials', function (string $method, string $uri, ?string $token): void {
    $headers = $token === null ? [] : ['Authorization' => 'Bearer '.$token];

    $this->json($method, $uri, [], $headers)->assertUnauthorized();
})->with([
    ['POST', '/api/logout'],
    ['GET', '/api/departments'],
    ['GET', '/api/answer-types'],
    ['GET', '/api/project-categories'],
    ['GET', '/api/project-types'],
    ['GET', '/api/design-styles'],
    ['POST', '/api/clients'],
    ['POST', '/api/projects'],
    ['GET', '/api/projects/1/questionnaire'],
    ['POST', '/api/projects/1/answers'],
    ['PATCH', '/api/projects/1/additional-questions'],
])->with([null, 'invalid-token']);
