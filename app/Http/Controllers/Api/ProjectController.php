<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Services\ProjectService;

class ProjectController extends Controller
{
    public function __construct(private readonly ProjectService $projectService) {}

    public function store(StoreProjectRequest $request): ProjectResource
    {
        return new ProjectResource($this->projectService->storeProject($request->validated()));
    }
}
