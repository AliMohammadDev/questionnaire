<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectCategoryResource;
use App\Services\ProjectCategoryService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectCategoryController extends Controller
{
  public function __construct(private readonly ProjectCategoryService $projectCategoryService) {}

  public function index(): AnonymousResourceCollection
  {
    return ProjectCategoryResource::collection($this->projectCategoryService->getAllProjectCategories());
  }
}
