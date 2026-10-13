<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectTypeResource;
use App\Services\ProjectTypeService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectTypeController extends Controller
{
    public function __construct(private readonly ProjectTypeService $projectTypeService) {}

    public function index(): AnonymousResourceCollection
    {
        return ProjectTypeResource::collection($this->projectTypeService->getAllProjectTypes());
    }
}
