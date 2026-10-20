<?php

use App\Models\AnswerType;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(LazilyRefreshDatabase::class);

it('returns all answer types ordered by sort order and id', function (): void {
    Sanctum::actingAs(User::factory()->create());
    $last = AnswerType::factory()->multipleChoice()->create(['sort_order' => 5]);
    $first = AnswerType::factory()->create(['sort_order' => 1, 'is_active' => false]);
    $second = AnswerType::factory()->singleChoice()->create(['sort_order' => 1]);

    $this->getJson(route('api.answer-types.index'))->assertOk()->assertExactJson([
        'data' => collect([$first, $second, $last])->map(fn (AnswerType $answerType): array => [
            'id' => $answerType->id,
            'name' => $answerType->name,
            'code' => $answerType->code,
            'has_options' => $answerType->has_options,
            'allows_multiple' => $answerType->allows_multiple,
            'is_active' => $answerType->is_active,
            'sort_order' => $answerType->sort_order,
        ])->all(),
    ]);
});

it('returns an empty collection when there are no answer types', function (): void {
    Sanctum::actingAs(User::factory()->create());
    $this->getJson(route('api.answer-types.index'))->assertOk()->assertExactJson(['data' => []]);
});
