<?php

use App\Models\Answer;
use App\Models\Project;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

it('groups project and answer attachments under the Arabic project name and keeps paths after renaming', function (): void {
    Storage::fake('public');
    $project = Project::factory()->create(['project_name' => 'فيلا العائلة']);
    $answer = Answer::factory()->for($project)->create();

    $first = $project->addMedia(UploadedFile::fake()->image('image.jpg'))->toMediaCollection('attachments');
    $second = $answer->addMedia(UploadedFile::fake()->image('image.jpg'))->toMediaCollection('attachments');

    expect($first->getPathRelativeToRoot())->toStartWith('projects/فيلا العائلة-'.$project->id.'/');
    expect($second->getPathRelativeToRoot())->toStartWith('projects/فيلا العائلة-'.$project->id.'/');
    $path = $first->getPathRelativeToRoot();
    $project->update(['project_name' => 'اسم جديد']);
    expect($first->fresh()->getPathRelativeToRoot())->toBe($path);
    $first->delete();
    Storage::disk('public')->assertExists($second->getPathRelativeToRoot());
});

it('reads legacy files and conversions without changing their content', function (): void {
    Storage::fake('public');
    $project = Project::factory()->create(['project_name' => 'مشروع تجريبي']);
    $media = $project->addMedia(UploadedFile::fake()->image('image.jpg'))->toMediaCollection('attachments');
    $original = $media->getPathRelativeToRoot();
    $contents = Storage::disk('public')->get($original);
    Storage::disk('public')->move($original, $media->id.'/image.jpg');
    Storage::disk('public')->put($media->id.'/conversions/thumb.jpg', 'thumbnail');
    $media->forgetCustomProperty('storage_path')->saveQuietly();

    $media->refresh();
    expect(Storage::disk('public')->get($media->getPathRelativeToRoot()))->toBe($contents);
    Storage::disk('public')->assertExists($media->id.'/conversions/thumb.jpg');
    Storage::disk('public')->assertExists($media->id.'/image.jpg');
    Storage::disk('public')->assertMissing($original);
});
