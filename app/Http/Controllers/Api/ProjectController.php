<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectAdditionalQuestionsRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;

class ProjectController extends Controller
{
    public function __construct(private readonly ProjectService $projectService) {}

    public function updateAdditionalQuestions(UpdateProjectAdditionalQuestionsRequest $request, Project $project): ProjectResource
    {
        $project->update($request->validated());

        return new ProjectResource($project);
    }

    public function store(StoreProjectRequest $request): ProjectResource
    {
        return new ProjectResource($this->projectService->storeProject($request->validated(), $request->user()));
    }
}
