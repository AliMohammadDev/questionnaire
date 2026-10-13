<?php

use App\Filament\Resources\QuestionContexts\Pages\ListQuestionContexts;
use App\Models\DesignStyle;
use App\Models\ProjectType;
use App\Models\QuestionContext;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $this->actingAs(User::factory()->administrator()->create());
});

it('filters question contexts by the selected dimensions and resets the results', function (array $dimensions): void {
    $type = ProjectType::factory()->create();
    $otherType = ProjectType::factory()->create();
    $selected = [
        'department_id' => $type->projectCategory->department_id,
        'project_category_id' => $type->project_category_id,
        'project_type_id' => $type->id,
        'design_style_id' => DesignStyle::factory()->create()->id,
    ];
    $other = [
        'department_id' => $otherType->projectCategory->department_id,
        'project_category_id' => $otherType->project_category_id,
        'project_type_id' => $otherType->id,
        'design_style_id' => DesignStyle::factory()->create()->id,
    ];
    $matching = QuestionContext::factory()->create($selected);
    $unrelated = QuestionContext::factory()->create($other);
    $general = QuestionContext::factory()->create();
    $filters = array_intersect_key($selected, array_flip($dimensions));
    $partialMatches = collect($dimensions)->map(fn (string $dimension): QuestionContext => QuestionContext::factory()->create(array_replace($selected, [$dimension => $other[$dimension]])));

    Livewire::test(ListQuestionContexts::class)
        ->loadTable()
        ->filterTable('context', $filters)
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$unrelated, $general, ...$partialMatches])
        ->resetTableFilters()
        ->assertCanSeeTableRecords([$matching, $unrelated, $general, ...$partialMatches]);
})->with([
    'department' => [['department_id']],
    'category' => [['project_category_id']],
    'project type' => [['project_type_id']],
    'design style' => [['design_style_id']],
    'all dimensions' => [['department_id', 'project_category_id', 'project_type_id', 'design_style_id']],
]);
