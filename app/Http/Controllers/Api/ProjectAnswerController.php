<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectAnswersRequest;
use App\Models\Project;
use App\Services\ProjectAnswerService;
use Illuminate\Http\JsonResponse;

class ProjectAnswerController extends Controller
{
  public function __construct(private readonly ProjectAnswerService $projectAnswerService) {}

  public function store(StoreProjectAnswersRequest $request, Project $project): JsonResponse
  {
    $this->projectAnswerService->storeAnswers($project, $request->questions(), $request->validated('answers', []));

    return response()->json(['message' => 'تم حفظ إجابات الاستبيان بنجاح', 'project_id' => $project->id]);
  }
}
